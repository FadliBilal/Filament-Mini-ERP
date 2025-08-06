<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Ambil data terlebih dahulu
        $totalPendapatan = Sale::sum('total_amount');
        $jumlahPelanggan = Customer::count();

        // Siapkan array untuk menampung semua stat yang akan ditampilkan
        $stats = [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalPendapatan, 0, ',', '.'))
                ->description('Seluruh pendapatan dari penjualan - kustomer')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];

        // Cek jika user adalah Admin
        if (Auth::user()->isAdmin()) {
            // Jika Admin, tambahkan stat khusus Admin ke dalam array
            $totalPembelian = Purchase::sum('total_amount');
            $jumlahProduk = Product::count();
            
            // Masukkan Stat Pembelian
            $stats[] = Stat::make('Total Pembelian', 'Rp ' . number_format($totalPembelian, 0, ',', '.'))
                ->description('Seluruh biaya pembelian stok - supplier')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger');
        }

        // Tambahkan stat yang bisa dilihat semua orang
        $stats[] = Stat::make('Jumlah Pelanggan', $jumlahPelanggan)
            ->description('Total pelanggan terdaftar')
            ->descriptionIcon('heroicon-m-user-group')
            ->color('info');

        // Cek lagi jika user adalah Admin untuk stat terakhir
        if (Auth::user()->isAdmin()) {
             // Jika Admin, tambahkan stat khusus Admin ke dalam array
             $jumlahProduk = Product::count();

            $stats[] = Stat::make('Jumlah Produk', $jumlahProduk)
                ->description('Total jenis produk tersedia')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('warning');
        }

        // Kembalikan array stat yang sudah lengkap
        return $stats;
    }
}