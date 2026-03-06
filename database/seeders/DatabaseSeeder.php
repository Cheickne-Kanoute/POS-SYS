<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@pos.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Manager
        User::create([
            'name' => 'Store Manager',
            'email' => 'manager@pos.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_active' => true,
        ]);

        // Create Cashiers with PINs
        User::create([
            'name' => 'Jean Dupont',
            'pin' => Hash::make('1234'),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Marie Koné',
            'pin' => Hash::make('5678'),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        // Create Categories
        $categories = [
            ['name' => 'Beverages'],
            ['name' => 'Dairy'],
            ['name' => 'Bakery'],
            ['name' => 'Fruits & Vegetables'],
            ['name' => 'Meat & Seafood'],
            ['name' => 'Snacks'],
            ['name' => 'Household'],
            ['name' => 'Personal Care'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Create Products
        $products = [
            ['name' => 'Mineral Water 1.5L', 'price' => 500, 'stock' => 100, 'category_id' => 1],
            ['name' => 'Coca-Cola 33cl', 'price' => 750, 'stock' => 80, 'category_id' => 1],
            ['name' => 'Orange Juice 1L', 'price' => 1200, 'stock' => 60, 'category_id' => 1],
            ['name' => 'Whole Milk 1L', 'price' => 900, 'stock' => 50, 'category_id' => 2],
            ['name' => 'Yogurt 250g', 'price' => 600, 'stock' => 40, 'category_id' => 2],
            ['name' => 'Baguette', 'price' => 350, 'stock' => 30, 'category_id' => 3],
            ['name' => 'Croissant', 'price' => 450, 'stock' => 25, 'category_id' => 3],
            ['name' => 'Apple 1kg', 'price' => 1500, 'stock' => 45, 'category_id' => 4],
            ['name' => 'Banana 1kg', 'price' => 800, 'stock' => 55, 'category_id' => 4],
            ['name' => 'Chicken Breast 1kg', 'price' => 3500, 'stock' => 20, 'category_id' => 5],
            ['name' => 'Potato Chips 100g', 'price' => 650, 'stock' => 70, 'category_id' => 6],
            ['name' => 'Chocolate Bar 50g', 'price' => 500, 'stock' => 90, 'category_id' => 6],
            ['name' => 'Dish Soap 500ml', 'price' => 1100, 'stock' => 35, 'category_id' => 7],
            ['name' => 'Laundry Detergent 1kg', 'price' => 2200, 'stock' => 28, 'category_id' => 7],
            ['name' => 'Shampoo 400ml', 'price' => 1800, 'stock' => 22, 'category_id' => 8],
        ];

        foreach ($products as $index => $product) {
            // EAN-13 generation: 12 digits (base) + 1 checksum
            $body = str_pad($index + 100000000000, 12, '0', STR_PAD_LEFT);
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += (int) $body[$i] * (($i % 2 === 0) ? 1 : 3);
            }
            $checkDigit = (10 - ($sum % 10)) % 10;
            $product['barcode'] = $body . $checkDigit;

            Product::create($product);
        }
    }
}
