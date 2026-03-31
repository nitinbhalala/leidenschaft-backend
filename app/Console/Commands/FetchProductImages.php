<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FetchProductImages extends Command
{
    protected $signature = 'fetch:product-images {--id= : Fetch images for a specific product ID}';

    protected $description = 'Fetch product images from leidenschaft.in and store in storage + product_images table';

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

    public function handle()
    {
        // ── Storage path show karo ──
        $storagePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'products');
        $this->info("📁 Storage path: {$storagePath}");

        // ── Products folder banavo ──
        if (!File::isDirectory($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
            $this->info("📁 Products folder created!");
        }

        $specificId = $this->option('id');

        if ($specificId) {
            if (!isset($this->productSlugs[$specificId])) {
                $this->error("Product ID {$specificId} no mapping nathi!");
                return 1;
            }
            $this->info("🔄 Fetching images for Product ID: {$specificId}");
            $this->fetchAndStoreImages($specificId, $this->productSlugs[$specificId]);

            // ── Verify: File exist chhe ke nahi ──
            $dir = storage_path("app" . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "products" . DIRECTORY_SEPARATOR . $specificId);
            if (File::isDirectory($dir)) {
                $files = File::files($dir);
                $this->info("📂 Files in storage: " . count($files));
                foreach ($files as $file) {
                    $this->line("   📄 " . $file->getFilename() . " (" . round($file->getSize() / 1024) . " KB)");
                }
            } else {
                $this->error("❌ Storage folder nathi bani: {$dir}");
            }
        } else {
            $this->info("🚀 Starting image fetch for all 100 products...");
            $this->newLine();

            $bar = $this->output->createProgressBar(count($this->productSlugs));
            $bar->start();

            $totalSaved = 0;
            foreach ($this->productSlugs as $productId => $slug) {
                $count = $this->fetchAndStoreImages($productId, $slug);
                $totalSaved += $count;
                $bar->advance();
                sleep(1);
            }

            $bar->finish();
            $this->newLine(2);
            $this->info("✅ Complete! Total {$totalSaved} images saved in storage.");
        }

        return 0;
    }

    /**
     * Returns: number of images saved
     */
    private function fetchAndStoreImages(int $productId, string $slug): int
    {
        try {
            $url = "https://leidenschaft.in/products/{$slug}";
            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                $this->warn(" ⚠️ Product {$productId}: Page fetch failed");
                return 0;
            }

            $imageUrls = $this->extractProductImages($response->body());

            if (empty($imageUrls)) {
                $this->warn(" ⚠️ Product {$productId}: No images found");
                return 0;
            }

            // Old images delete karo
            $this->deleteExistingImages($productId);

            // Download + save
            $savedCount = 0;
            foreach ($imageUrls as $imageUrl) {
                if ($this->downloadAndSaveImage($productId, $imageUrl)) {
                    $savedCount++;
                }
            }

            $this->line("  ✅ Product {$productId}: {$savedCount} images saved");
            return $savedCount;
        } catch (\Exception $e) {
            $this->error(" ❌ Product {$productId}: " . $e->getMessage());
            return 0;
        }
    }

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
            'favicon',
            'icon',
            '.ico',
        ];

        foreach ($skipPatterns as $pattern) {
            if (stripos($path, $pattern) !== false) return false;
        }

        return true;
    }

    private function deleteExistingImages(int $productId): void
    {
        // DB clear
        DB::table('product_images')->where('product_id', $productId)->delete();

        // Storage clear (Windows compatible path)
        $folderPath = storage_path("app" . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "products" . DIRECTORY_SEPARATOR . $productId);
        if (File::isDirectory($folderPath)) {
            File::deleteDirectory($folderPath);
        }
    }

    /**
     * =====================================================
     * FIX: file_put_contents use kare chhe
     * Storage::put() Windows WAMP ma kaam nathi karti
     * Directly absolute path ma save kare chhe
     * =====================================================
     */
    private function downloadAndSaveImage(int $productId, string $imageUrl): bool
    {
        try {
            // ── Download ──
            $response = Http::timeout(60)->get($imageUrl);

            if (!$response->successful()) {
                return false;
            }

            $imageContent = $response->body();

            // ── Empty check ──
            if (empty($imageContent) || strlen($imageContent) < 100) {
                return false;
            }

            // ── File name ──
            $extension = $this->getExtension($imageUrl);
            $randomName = Str::random(40) . '.' . $extension;

            // ── ABSOLUTE PATH (Windows compatible with DIRECTORY_SEPARATOR) ──
            // C:\wamp64\www\projects\leidenschaft\storage\app\public\products\{id}\
            $directory = storage_path("app" . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "products" . DIRECTORY_SEPARATOR . $productId);
            $fullPath  = $directory . DIRECTORY_SEPARATOR . $randomName;

            // ── Directory banavo ──
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // ── FILE SAVE (file_put_contents - Windows ma 100% kaam kare) ──
            $bytes = file_put_contents($fullPath, $imageContent);

            if ($bytes === false || $bytes === 0) {
                $this->warn("    ⚠️ Write failed: {$fullPath}");
                return false;
            }

            // ── DB SAVE ──
            $dbPath = "products/{$productId}/{$randomName}";

            DB::table('product_images')->insert([
                'product_id' => $productId,
                'image'      => $dbPath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            $this->warn("    ⚠️ Error: " . $e->getMessage());
            return false;
        }
    }

    private function getExtension(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        return in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp', 'gif']) ? $ext : 'jpg';
    }
}
