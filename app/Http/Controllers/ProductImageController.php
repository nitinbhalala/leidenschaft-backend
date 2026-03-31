<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProductImageController extends Controller
{
    /**
     * =====================================================
     * PRODUCT SLUG MAPPING
     * =====================================================
     * Tamara product IDs ne leidenschaft.in slugs sathe map kare chhe
     * =====================================================
     */
    private $productSlugs = [
        // ── Tables (Essentials) ──
        1  => 'arcane-coffee-table',
        2  => 'axis',
        3  => 'harmony-dining-table',
        4  => 'flow',
        5  => 'fold-verse',
        6  => 'geo-cube',
        7  => 'mono',
        8  => 'domino-side-tables',
        9  => 'zenith',
        10 => 'drift',

        // ── Lights (Essentials) ──
        11 => 'discord-floor-lamp',
        12 => 'discord-table-lamp',
        13 => 'drape-hanging-light',
        14 => 'drape-table-lamp',
        15 => 'floating-rock',
        16 => 'papilio',
        17 => 'pearl-nest-light',
        18 => 'pollylune',
        19 => 'rise-table-lamp-table-lamp-conical',
        20 => 'synergy',

        // ── Gifting (Essentials) ──
        21 => 'slowburn-tealight-holder-i',
        22 => 'slowburn-multi-light-holder-i',
        23 => 'slowburn-multi-light-holder-ii',
        24 => 'slowburn-dual-candle-holder',
        25 => 'slowburn-candle-holder-i',
        26 => 'slowburn-candle-holder-ii',
        27 => 'slowburn-incense-stick-holder-i',
        28 => 'slowburn-incense-stick-holder-ii',
        29 => 'slowburn-multi-light-holder-iii',
        30 => 'balancing-act-table-lamp-i',

        // ── Chair (Echoes of the Earth) ──
        31 => 'boulder-chair',
        32 => 'abacus-chair',
        33 => 'discord-bench',
        34 => 'tranquil-arc',
        35 => 'loam-crest-tables',
        36 => 'terra-curve',
        37 => 'serenity-console',
        38 => 'nimbus',
        39 => 'shroom',
        40 => 'sage-stone-table',

        // ── Artful Pieces (Malleka) ──
        41 => 'balancing-act-sculpture',
        42 => 'abacus',
        43 => 'abacus-swatch',
        44 => 'discord-wall-panel',
        45 => 'enclose-fluid-concrete-mirror',
        46 => 'evolve-console',
        47 => 'isle-n1',
        48 => 'isle-n2',
        49 => 'dimension-wall-panels-n2',
        50 => 'wall-panels-n1',

        // ── Tables (Echoes of the Earth) ──
        51 => 'balancing-act-coffee-table',
        52 => 'balancing-act-table',
        53 => 'drapes-side-table-round',
        54 => 'drapes-sde-table-square',
        55 => 'morph-coffee-table-sturdy-fluid',
        56 => 'luna',
        57 => 'pearl-peg-table',
        58 => 'sage-stone-table',
        59 => 'serenity-console',
        60 => 'fluted-drum',

        // ── Lights (Echoes of the Earth) ──
        61 => 'lume-lamp',
        62 => 'trilume-lamp',
        63 => 'anchored-floor-lamp',
        64 => 'anchored-hanging-lamp-i-ii-iii-iv',
        65 => 'anchored-table-lamp',
        66 => 'balancing-act-table-lamp-i',
        67 => 'balancing-act-table-lamp-ii',
        68 => 'balancing-act-table-lamp-iii',
        69 => 'on-axis-table-lamp-ii',
        70 => 'morph-coffee-table-sturdy-fluid',

        // ── Gifting (Malleka 2025) ──
        71 => 'slowburn-tealight-holder-i',
        72 => 'slowburn-incense-stick-holder-i',
        73 => 'fluted-drum',
        74 => 'pearl-peg-table',
        75 => 'slowburn-candle-holder-ii',
        76 => 'balancing-act-sculpture',
        77 => 'slowburn-dual-candle-holder',
        78 => 'slowburn-candle-holder-i',
        79 => 'geo-cube',
        80 => 'wall-panels-n1',

        // ── Artful Pieces (Malleka 2024 I) ──
        81 => 'abacus',
        82 => 'abacus-swatch',
        83 => 'evolve-console',
        84 => 'balancing-act-sculpture',
        85 => 'enclose-fluid-concrete-mirror',
        86 => 'discord-wall-panel',
        87 => 'isle-n1',
        88 => 'isle-n2',
        89 => 'dimension-wall-panels-n2',
        90 => 'wall-panels-n1',

        // ── Artful Pieces (Malleka 2024 II) ──
        91 => 'isle-n2',
        92 => 'abacus',
        93 => 'discord-wall-panel',
        94 => 'abacus-swatch',
        95 => 'enclose-fluid-concrete-mirror',
        96 => 'evolve-console',
        97 => 'isle-n1',
        98 => 'balancing-act-sculpture',
        99 => 'dimension-wall-panels-n2',
        100 => 'wall-panels-n1',
    ];

    /**
     * =====================================================
     * SINGLE PRODUCT: Ek product na images fetch karo
     * Route: POST /admin/products/{id}/fetch-images
     * =====================================================
     */
    public function fetchImages($productId)
    {
        if (!isset($this->productSlugs[$productId])) {
            return response()->json([
                'success' => false,
                'message' => "Product ID {$productId} mate koi mapping nathi."
            ], 404);
        }

        $slug = $this->productSlugs[$productId];
        $result = $this->fetchAndStoreImages($productId, $slug);

        return response()->json($result);
    }

    /**
     * =====================================================
     * ALL PRODUCTS: Badha products na images fetch karo
     * Route: POST /admin/products/fetch-all-images
     * =====================================================
     */
    public function fetchAllImages()
    {
        $results = [];
        $success = 0;
        $failed = 0;

        foreach ($this->productSlugs as $productId => $slug) {
            $result = $this->fetchAndStoreImages($productId, $slug);

            if ($result['success']) {
                $success++;
            } else {
                $failed++;
            }

            $results[] = $result;

            // Shopify rate limiting thi bachva
            usleep(500000); // 0.5 second
        }

        return response()->json([
            'success'       => true,
            'message'       => "Images fetch complete! Success: {$success}, Failed: {$failed}",
            'total'         => count($this->productSlugs),
            'success_count' => $success,
            'failed_count'  => $failed,
            'details'       => $results,
        ]);
    }

    /**
     * =====================================================
     * CORE FUNCTION: Images fetch + store
     * =====================================================
     */
    private function fetchAndStoreImages(int $productId, string $slug): array
    {
        try {
            // ── Step 1: Product page fetch karo ──
            $url = "https://leidenschaft.in/products/{$slug}";
            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                return [
                    'success'    => false,
                    'product_id' => $productId,
                    'message'    => "Page fetch failed (HTTP {$response->status()})"
                ];
            }

            // ── Step 2: Images extract karo ──
            $imageUrls = $this->extractProductImages($response->body());

            if (empty($imageUrls)) {
                return [
                    'success'    => false,
                    'product_id' => $productId,
                    'message'    => 'No images found on product page'
                ];
            }

            // ── Step 3: Old images delete karo ──
            $this->deleteExistingImages($productId);

            // ── Step 4: Download + save ──
            $savedCount = 0;
            foreach ($imageUrls as $index => $imageUrl) {
                if ($this->downloadAndSaveImage($productId, $imageUrl)) {
                    $savedCount++;
                }
            }

            return [
                'success'      => true,
                'product_id'   => $productId,
                'slug'         => $slug,
                'images_found' => count($imageUrls),
                'images_saved' => $savedCount,
                'message'      => "{$savedCount} images saved successfully"
            ];
        } catch (\Exception $e) {
            return [
                'success'    => false,
                'product_id' => $productId,
                'message'    => $e->getMessage()
            ];
        }
    }

    /**
     * HTML ma thi product images extract karo
     */
    private function extractProductImages(string $html): array
    {
        $imageUrls = [];

        preg_match_all(
            '/cdn\/shop\/files\/[^"?\s]+\.(?:jpg|png|jpeg|webp)/i',
            $html,
            $matches
        );

        if (!empty($matches[0])) {
            $seen = [];
            foreach ($matches[0] as $match) {
                $cleanPath = preg_replace('/\?.*$/', '', $match);

                if (in_array($cleanPath, $seen)) continue;
                if (!$this->isProductImage($cleanPath)) continue;

                $seen[] = $cleanPath;
                $imageUrls[] = 'https://leidenschaft.in/' . $cleanPath;
            }
        }

        return array_slice(array_unique($imageUrls), 0, 6);
    }

    /**
     * Product image chhe ke nahi check karo (logos/nav skip)
     */
    private function isProductImage(string $path): bool
    {
        $skipPatterns = [
            'Leidenschaft_Logo',
            'Leidenschaft-Logo',
            'Carousel_page',
            'IMG_1.jpg',
            'IMG_2.jpg',
            'Essentials_',
            'signature-log',
            'ICON818',
        ];

        foreach ($skipPatterns as $pattern) {
            if (stripos($path, $pattern) !== false) return false;
        }

        return true;
    }

    /**
     * Existing images delete karo
     */
    private function deleteExistingImages(int $productId): void
    {
        DB::table('product_images')->where('product_id', $productId)->delete();

        $folderPath = "public/products/{$productId}";
        if (Storage::exists($folderPath)) {
            Storage::deleteDirectory($folderPath);
        }
    }

    /**
     * Image download + storage + DB save
     */
    private function downloadAndSaveImage(int $productId, string $imageUrl): bool
    {
        try {
            $response = Http::timeout(60)->get($imageUrl);
            if (!$response->successful()) return false;

            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $randomName = Str::random(40) . '.' . $extension;

            // Storage: storage/app/public/products/{id}/filename.jpg
            $storagePath = "public/products/{$productId}/{$randomName}";
            Storage::put($storagePath, $response->body());

            // DB: products/{id}/filename.jpg
            $dbPath = "products/{$productId}/{$randomName}";
            DB::table('product_images')->insert([
                'product_id' => $productId,
                'image'      => $dbPath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
