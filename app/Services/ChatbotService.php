<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ChatbotService
{
    protected string $apiKey;
    protected string $apiReferrer;
    protected string $model = 'google/gemma-3n-e4b-it:free';

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->apiReferrer = config('services.openrouter.referrer');
    }

    /**
     * Proses utama untuk menganalisis pesan dan mendapatkan jawaban.
     */
    public function processMessage(string $userInput): string
    {
        // 1. Analisis intent (maksud) pengguna secara sederhana
        $context = $this->getIntentAndContext($userInput);

        // Jika tidak ada maksud yang dikenali
        if (empty($context['data'])) {
            return $this->askOpenRouter(
                "Anda adalah asisten AI. Pengguna bertanya: '{$userInput}'. Karena Anda tidak memiliki data spesifik, jawablah dengan ramah bahwa Anda tidak dapat menemukan informasi yang diminta dan sarankan untuk bertanya tentang penjualan, pembelian, atau stok.",
            );
        }

        // 2. Buat prompt yang kaya konteks
        $prompt = "Anda adalah ERPav yang cerdas untuk sistem ERP. Berdasarkan data berikut: \n\n";
        $prompt .= "Konteks Data: " . $context['description'] . "\n";
        $prompt .= "Data Aktual: \n" . json_encode($context['data'], JSON_PRETTY_PRINT) . "\n\n";
        $prompt .= "Jawab pertanyaan pengguna berikut dengan natural dan ramah, seolah-olah Anda menyimpulkan dari data yang diberikan. Pertanyaan Pengguna: '{$userInput}'";

        // 3. Tanyakan ke OpenRouter
        return $this->askOpenRouter($prompt);
    }

    /**
     * Menganalisis maksud pengguna dan mengambil data dari DB.
     * Ini adalah versi sederhana menggunakan kata kunci.
     */
    private function getIntentAndContext(string $userInput): array
    {
        $userInput = strtolower($userInput);

        // Intent: Mengetahui total penjualan
        if (str_contains($userInput, 'penjualan') || str_contains($userInput, 'terjual')) {
            $range = 'hari ini';
            if(str_contains($userInput, 'kemarin')) $range = 'kemarin';
            if(str_contains($userInput, 'bulan ini')) $range = 'bulan ini';
            if(str_contains($userInput, 'bulan lalu')) $range = 'bulan lalu';

            return $this->getSalesData($range);
        }

        if (str_contains($userInput, 'pembelian') || str_contains($userInput, 'terjual')) {
            $range = 'hari ini';
            if(str_contains($userInput, 'kemarin')) $range = 'kemarin';
            if(str_contains($userInput, 'bulan ini')) $range = 'bulan ini';
            if(str_contains($userInput, 'bulan lalu')) $range = 'bulan lalu';

            return $this->getSalesData($range);
        }

        // Intent: Mengetahui stok produk
        if (str_contains($userInput, 'stok') || str_contains($userInput, 'sisa')) {
            // Prioritaskan pencarian stok rendah jika ada kata 'rendah' atau hanya kata 'stok' saja
            if (str_contains($userInput, 'rendah') || trim($userInput) === 'stok') {
                return $this->getStockData(null); // Cari semua stok rendah
            }

            // Jika tidak, coba cari nama produk spesifik
            $productName = trim(str_ireplace(['cek stok', 'stok', 'sisa'], '', $userInput));
            if (!empty($productName)) {
                return $this->getStockData($productName);
            }
            
            // Fallback jika hanya kata "stok" yang diberikan
            return $this->getStockData(null);
        }

        // --- KEMAMPUAN BARU: Menghitung Pelanggan ---
        if (str_contains($userInput, 'customer') || str_contains($userInput, 'pelanggan')) {
             return $this->getCustomerCountData();
        }

        // --- KEMAMPUAN BARU: Melihat Semua Produk ---
        if (str_contains($userInput, 'produk apa aja') || str_contains($userInput, 'semua produk') || str_contains($userInput, 'list produk')) {
            return $this->getAllProductsData();
        }

        return ['description' => 'Tidak ada data', 'data' => []];
    }

    /**
     * Mengambil data penjualan dari database.
     */
    private function getSalesData(string $range): array
    {
        $query = Sale::query();

        switch ($range) {
            case 'kemarin':
                $query->whereDate('date', Carbon::yesterday());
                break;
            case 'bulan ini':
                $query->whereMonth('date', Carbon::now()->month)->whereYear('date', Carbon::now()->year);
                break;
            case 'bulan lalu':
                $query->whereMonth('date', Carbon::now()->subMonth()->month)->whereYear('date', Carbon::now()->subMonth()->year);
                break;
            case 'hari ini':
            default:
                $query->whereDate('date', Carbon::today());
                break;
        }

        $total = $query->sum('total_amount');
        $count = $query->count();

        return [
            'description' => "Data penjualan untuk periode '{$range}'",
            'data' => [
                'total_transaksi' => $count,
                'total_pendapatan' => 'Rp ' . number_format($total, 0, ',', '.'),
            ]
        ];
    }

    /**
     * Mengambil data stok dari database.
     */
    private function getStockData(?string $productName): array
    {
        if (!$productName) {
            return [
                'description' => 'Stok semua produk dengan stok rendah (<= 10)',
                'data' => Product::where('stock', '<=', 10)->pluck('stock', 'name')->toArray()
            ];
        }

        $product = Product::where('name', 'like', "%{$productName}%")->first();

        if (!$product) {
            return ['description' => "Produk '{$productName}' tidak ditemukan.", 'data' => []];
        }

        return [
            'description' => "Stok untuk produk '{$product->name}'",
            'data' => [
                'nama_produk' => $product->name,
                'sisa_stok' => $product->stock,
            ]
        ];
    }

    /**
     * FUNGSI BARU: Mengambil jumlah pelanggan.
     */
    private function getCustomerCountData(): array
    {
        $count = Customer::count();
        return [
            'description' => "Jumlah total pelanggan yang terdaftar.",
            'data' => [
                'jumlah_pelanggan' => $count
            ]
        ];
    }

    /**
     * FUNGSI BARU: Mengambil semua nama produk.
     */
    private function getAllProductsData(): array
    {
        $products = Product::all()->pluck('name')->toArray();
        return [
            'description' => "Daftar semua produk yang tersedia.",
            'data' => $products
        ];
    }

    /**
     * Mengirimkan prompt ke API OpenRouter.
     */
    private function askOpenRouter(string $prompt): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => $this->apiReferrer,
                'X-Title' => 'Mini ERP', // Ganti dengan nama aplikasi Anda
            ])
            ->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            return "Maaf, terjadi masalah saat menghubungi AI. Error: " . $response->body();

        } catch (\Exception $e) {
            return "Maaf, terjadi kesalahan teknis: " . $e->getMessage();
        }
    }
}