<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use App\Models\Product;
use App\Models\WarehouseInventory;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Warehouse staff
        User::create([
            'name' => 'Warehouse Staff',
            'email' => 'warehouse@example.com',
            'password' => bcrypt('password'),
            'role' => 'warehouse_staff',
            'status' => 'active',
        ]);

        // Branch 1
        $branch1 = Branch::create([
            'name' => 'North Branch',
            'code' => 'BR-N-001',
            'region' => 'North',
            'city' => 'Capital',
            'address' => '123 North St',
            'status' => 'active',
        ]);

        // Branch manager 1
        User::create([
            'name' => 'Branch Manager 1',
            'email' => 'bm1@example.com',
            'password' => bcrypt('password'),
            'role' => 'branch_manager',
            'branch_id' => $branch1->id,
            'status' => 'active',
        ]);

        // Branch staff 1
        User::create([
            'name' => 'Branch Staff 1',
            'email' => 'bs1@example.com',
            'password' => bcrypt('password'),
            'role' => 'branch_staff',
            'branch_id' => $branch1->id,
            'status' => 'active',
        ]);

        // Branch 2
        $branch2 = Branch::create([
            'name' => 'South Branch',
            'code' => 'BR-S-002',
            'region' => 'South',
            'city' => 'Coastal',
            'address' => '456 South Ave',
            'status' => 'active',
        ]);

        // Branch manager 2
        User::create([
            'name' => 'Branch Manager 2',
            'email' => 'bm2@example.com',
            'password' => bcrypt('password'),
            'role' => 'branch_manager',
            'branch_id' => $branch2->id,
            'status' => 'active',
        ]);

        // Sample products
        $products = [
            ['name' => 'UltraBook Pro 14"', 'sku' => 'UB-PRO-14-SLV', 'category' => 'Electronics', 'supplier' => 'Apex Systems', 'purchase_price' => 840, 'selling_price' => 1299, 'reorder_point' => 10],
            ['name' => 'Wireless Mouse', 'sku' => 'WM-BLK-001', 'category' => 'Accessories', 'supplier' => 'GearCo', 'purchase_price' => 25, 'selling_price' => 49, 'reorder_point' => 50],
            ['name' => 'Mechanical Keyboard', 'sku' => 'MK-RGB-002', 'category' => 'Accessories', 'supplier' => 'GearCo', 'purchase_price' => 75, 'selling_price' => 119, 'reorder_point' => 20],
            ['name' => 'Monitor 27"', 'sku' => 'MON-27-FHD', 'category' => 'Electronics', 'supplier' => 'Apex Systems', 'purchase_price' => 180, 'selling_price' => 299, 'reorder_point' => 8],
            ['name' => 'USB-C Hub', 'sku' => 'HUB-7IN1-01', 'category' => 'Accessories', 'supplier' => 'ConnectPro', 'purchase_price' => 30, 'selling_price' => 55, 'reorder_point' => 30],
        ];

        foreach ($products as $p) {
            $product = Product::create($p);
            WarehouseInventory::create([
                'product_id' => $product->id,
                'quantity' => rand(50, 200),
            ]);
        }
    }
}