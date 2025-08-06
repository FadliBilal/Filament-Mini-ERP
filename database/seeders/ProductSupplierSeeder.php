<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSupplierSeeder extends Seeder
{
    public function run(): void
    {
        $supplier1 = Supplier::find(1);
        $supplier2 = Supplier::find(2);

        $product1 = Product::find(1); // Buku
        $product2 = Product::find(2); // Pulpen
        $product3 = Product::find(3); // Pensil
        $product4 = Product::find(4); // Penghapus

        $supplier1->products()->attach($product1->id, ['price' => 3500]);
        $supplier1->products()->attach($product2->id, ['price' => 20000]);

        $supplier2->products()->attach($product1->id, ['price' => 3600]);
        $supplier2->products()->attach($product3->id, ['price' => 2800]);
        $supplier2->products()->attach($product4->id, ['price' => 1200]);
    }
}