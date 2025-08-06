<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create(['name' => 'Buku Tulis Sinar Dunia', 'harga_jual' => 5000, 'stock' => 3]);
        Product::create(['name' => 'Pulpen Pilot G2', 'harga_jual' => 25000, 'stock' => 50]);
        Product::create(['name' => 'Pensil 2B Faber-Castell', 'harga_jual' => 4000, 'stock' => 0]);
        Product::create(['name' => 'Penghapus Joyko', 'harga_jual' => 2000, 'stock' => 10]);
        Product::create(['name' => 'Sticky Notes Post-it', 'harga_jual' => 15000, 'stock' => 1]);
    }
}