<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::create(['name' => 'Budi Santoso', 'contact_info' => '081234567890']);
        Customer::create(['name' => 'Citra Lestari', 'contact_info' => '089876543210']);
    }
}