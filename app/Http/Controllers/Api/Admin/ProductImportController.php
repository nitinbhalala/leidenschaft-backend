<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductInventory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductImportController extends BaseController
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:51200',
        ]);

        set_time_limit(0);
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');
        ini_set('default_socket_timeout', 120);

        try {
            $records = $this->parseFile($request->file('file'));
        } catch (Exception $e) {
            return $this->error('Could not read the uploaded file: ' . $e->getMessage(), 422);
        }

        if (empty($records['products'])) {
            return $this->error('No products were found in the uploaded file.', 422);
        }

        try {
            $this->resetProductData();

            $categoriesCreated = [];
            $failedImages = [];
            $imported = 0;
            $activeCount = 0;
            $inactiveCount = 0;

            foreach ($records['products'] as $record) {
                [$categoryId, $subCategoryId, $createdCategoryNames] = $this->resolveCategories($record['category_candidates']);
                $categoriesCreated = array_merge($categoriesCreated, $createdCategoryNames);

                $product = DB::transaction(function () use ($record, $categoryId, $subCategoryId) {
                    $product = Product::create([
                        'category_id'         => $categoryId,
                        'sub_category_id'     => $subCategoryId,
                        'name'                => $record['name'],
                        'sku'                 => $record['sku'],
                        'description'         => $record['description'],
                        'material_dimensions' => $record['material_dimensions'],
                        'price'               => $record['price'],
                        'stock'               => $record['stock'],
                        'status'              => $record['status'],
                    ]);

                    ProductInventory::create([
                        'product_id'          => $product->id,
                        'stock'               => $product->stock,
                        'low_stock_threshold' => 5,
                        'is_active'           => (bool) $record['status'],
                    ])->updateStockStatus();

                    return $product;
                });

                foreach ($record['images'] as $imageUrl) {
                    try {
                        $this->downloadAndStoreImage($product->id, $imageUrl);
                    } catch (Exception $e) {
                        $failedImages[] = "{$record['name']} ({$imageUrl}): {$e->getMessage()}";
                    }
                }

                $imported++;
                $record['status'] === 1 ? $activeCount++ : $inactiveCount++;
            }

            return $this->success([
                'imported'           => $imported,
                'active'             => $activeCount,
                'inactive'           => $inactiveCount,
                'categories_created' => array_values(array_unique($categoriesCreated)),
                'failed_images'      => $failedImages,
            ], 'Products imported successfully');
        } catch (Exception $e) {
            return $this->error('Import failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Parse the uploaded CSV/XLSX file into grouped product records.
     * Every handle in the file is imported; its DB `status` is set to 1 when
     * the file's Status column is "active" for that handle, 0 otherwise.
     */
    private function parseFile($file): array
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (empty($rows)) {
            throw new Exception('The file is empty.');
        }

        $header = array_shift($rows);
        $header = array_map(fn($h) => trim((string) $h), $header);

        $handles = [];
        foreach ($rows as $row) {
            $assoc = [];
            foreach ($header as $i => $key) {
                if ($key === '') {
                    continue;
                }
                $assoc[$key] = $row[$i] ?? null;
            }

            $handle = trim((string) ($assoc['Handle'] ?? ''));
            if ($handle === '') {
                continue;
            }

            $handles[$handle][] = $assoc;
        }

        $products = [];

        foreach ($handles as $handle => $groupRows) {
            $isActive = collect($groupRows)->contains(
                fn($r) => strtolower(trim((string) ($r['Status'] ?? ''))) === 'active'
            );

            // De-duplicate consecutive rows that share the same Variant SKU
            // (they are just extra image rows for the same variant).
            $distinctBySku = [];
            $seenSkus = [];
            foreach ($groupRows as $r) {
                $sku = trim((string) ($r['Variant SKU'] ?? ''));
                if ($sku !== '' && in_array($sku, $seenSkus, true)) {
                    continue;
                }
                if ($sku !== '') {
                    $seenSkus[] = $sku;
                }
                $distinctBySku[] = $r;
            }

            $first = $distinctBySku[0];

            $stock = 0;
            foreach ($distinctBySku as $r) {
                $stock += (int) ($r['Variant Inventory Qty'] ?? 0);
            }

            $images = [];
            foreach ($groupRows as $r) {
                $src = trim((string) ($r['Image Src'] ?? ''));
                if ($src !== '' && !in_array($src, $images, true)) {
                    $images[] = $src;
                }
            }

            $materialDimensions = array_values(array_unique(array_filter([
                trim((string) ($first['Dimension (product.metafields.custom.dimension)'] ?? '')),
                trim((string) ($first['Finish (product.metafields.custom.finish)'] ?? '')),
                trim((string) ($first['Material (product.metafields.custom.material)'] ?? '')),
                trim((string) ($first['Material (product.metafields.shopify.material)'] ?? '')),
            ])));

            $tags = array_filter(array_map('trim', explode(',', (string) ($first['Tags'] ?? ''))));
            $type = trim((string) ($first['Type'] ?? ''));
            $categoryCandidates = array_values(array_unique(array_filter(array_merge([$type], $tags))));

            $products[] = [
                'name'                => trim((string) ($first['Title'] ?? $handle)),
                'sku'                 => trim((string) ($first['Variant SKU'] ?? '')) ?: null,
                'description'         => $first['Body (HTML)'] ?? null,
                'price'               => (float) ($first['Variant Price'] ?? 0),
                'stock'               => $stock,
                'material_dimensions' => $materialDimensions ? implode("\n", $materialDimensions) : null,
                'images'              => $images,
                'category_candidates' => $categoryCandidates,
                'status'              => $isActive ? 1 : 0,
            ];
        }

        return ['products' => $products];
    }

    /**
     * Resolve category_id / sub_category_id from candidate names, creating a
     * top-level category if none of the candidates match an existing one.
     *
     * @return array{0: int, 1: ?int, 2: string[]}
     */
    private function resolveCategories(array $candidates): array
    {
        $created = [];

        if (empty($candidates)) {
            $candidates = ['Uncategorized'];
        }

        $topLevel = Category::whereNull('parent_id')->get(['id', 'name']);

        $categoryId = null;
        $matchedCandidate = null;

        foreach ($candidates as $candidate) {
            $match = $topLevel->first(fn($c) => strcasecmp($c->name, $candidate) === 0);
            if ($match) {
                $categoryId = $match->id;
                $matchedCandidate = $candidate;
                break;
            }
        }

        if (!$categoryId) {
            $name = $candidates[0];
            $category = Category::create([
                'name'   => $name,
                'status' => 1,
            ]);
            $categoryId = $category->id;
            $matchedCandidate = $name;
            $created[] = $name;
        }

        $subCategoryId = null;
        $children = Category::where('parent_id', $categoryId)->get(['id', 'name']);

        foreach ($candidates as $candidate) {
            if ($candidate === $matchedCandidate) {
                continue;
            }
            $match = $children->first(fn($c) => strcasecmp($c->name, $candidate) === 0);
            if ($match) {
                $subCategoryId = $match->id;
                break;
            }
        }

        return [$categoryId, $subCategoryId, $created];
    }

    /**
     * Wipe products and everything that references them, including orders and payments.
     */
    private function resetProductData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach (['product_reviews', 'product_images', 'product_inventories', 'order_items', 'orders', 'payments', 'carts', 'wishlists', 'scene_pins', 'products'] as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        if (\Illuminate\Support\Facades\Schema::hasTable('home_page_settings')) {
            DB::table('home_page_settings')->update([
                'section_2_product_ids'      => null,
                'section_2_product_position' => null,
            ]);
        }

        Storage::disk('public')->deleteDirectory('products');
    }

    private function downloadAndStoreImage(int $productId, string $url): void
    {
        $response = Http::timeout(15)->retry(1, 500)->get($url);

        if (!$response->successful()) {
            throw new Exception('HTTP ' . $response->status());
        }

        $extension = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg';
        $extension = strtolower(preg_replace('/[^a-z0-9]/i', '', $extension)) ?: 'jpg';

        $path = "products/{$productId}/" . Str::random(20) . '.' . $extension;

        Storage::disk('public')->put($path, $response->body());

        ProductImage::create([
            'product_id' => $productId,
            'image'      => $path,
        ]);
    }
}
