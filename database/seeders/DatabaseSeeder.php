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
            ['name' => 'Mineral Water 1.5L', 'barcode' => '1000001', 'price' => 500, 'stock' => 100, 'category_id' => 1],
            ['name' => 'Coca-Cola 33cl', 'barcode' => '1000002', 'price' => 750, 'stock' => 80, 'category_id' => 1],
            ['name' => 'Orange Juice 1L', 'barcode' => '1000003', 'price' => 1200, 'stock' => 60, 'category_id' => 1],
            ['name' => 'Whole Milk 1L', 'barcode' => '1000004', 'price' => 900, 'stock' => 50, 'category_id' => 2],
            ['name' => 'Yogurt 250g', 'barcode' => '1000005', 'price' => 600, 'stock' => 40, 'category_id' => 2],
            ['name' => 'Baguette', 'barcode' => '1000006', 'price' => 350, 'stock' => 30, 'category_id' => 3],
            ['name' => 'Croissant', 'barcode' => '1000007', 'price' => 450, 'stock' => 25, 'category_id' => 3],
            ['name' => 'Apple 1kg', 'barcode' => '1000008', 'price' => 1500, 'stock' => 45, 'category_id' => 4],
            ['name' => 'Banana 1kg', 'barcode' => '1000009', 'price' => 800, 'stock' => 55, 'category_id' => 4],
            ['name' => 'Chicken Breast 1kg', 'barcode' => '1000010', 'price' => 3500, 'stock' => 20, 'category_id' => 5],
            ['name' => 'Potato Chips 100g', 'barcode' => '1000011', 'price' => 650, 'stock' => 70, 'category_id' => 6],
            ['name' => 'Chocolate Bar 50g', 'barcode' => '1000012', 'price' => 500, 'stock' => 90, 'category_id' => 6],
            ['name' => 'Dish Soap 500ml', 'barcode' => '1000013', 'price' => 1100, 'stock' => 35, 'category_id' => 7],
            ['name' => 'Laundry Detergent 1kg', 'barcode' => '1000014', 'price' => 2200, 'stock' => 28, 'category_id' => 7],
            ['name' => 'Shampoo 400ml', 'barcode' => '1000015', 'price' => 1800, 'stock' => 22, 'category_id' => 8],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
