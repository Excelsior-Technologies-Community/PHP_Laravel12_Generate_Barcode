<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop Pro',
                'sku' => 'LP-001',
                'price' => 1299.99,
                'description' => 'High-performance laptop for professionals',
                'barcode' => 'BC-LP001PRO'
            ],
            [
                'name' => 'Wireless Mouse',
                'sku' => 'WM-002',
                'price' => 29.99,
                'description' => 'Ergonomic wireless mouse with long battery life',
                'barcode' => 'BC-WM002WL'
            ],
            [
                'name' => 'Mechanical Keyboard',
                'sku' => 'MK-003',
                'price' => 89.99,
                'description' => 'RGB mechanical keyboard with blue switches',
                'barcode' => 'BC-MK003RB'
            ],
            [
                'name' => 'USB-C Hub',
                'sku' => 'UH-004',
                'price' => 49.99,
                'description' => '7-in-1 USB-C hub with HDMI and Ethernet',
                'barcode' => 'BC-UH0047I'
            ],
            [
                'name' => 'Portable SSD 1TB',
                'sku' => 'SSD-005',
                'price' => 129.99,
                'description' => 'High-speed portable SSD with 1TB storage',
                'barcode' => 'BC-SSD0051T'
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}