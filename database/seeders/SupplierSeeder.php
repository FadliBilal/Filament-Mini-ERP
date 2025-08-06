<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::create(['name' => 'PT Sinar Jaya Abadi', 'contact_info' => 'Jl. Merdeka No. 123, Jakarta']);
        Supplier::create(['name' => 'CV Makmur Sentosa', 'contact_info' => 'Jl. Pahlawan No. 45, Surabaya']);
    }
}