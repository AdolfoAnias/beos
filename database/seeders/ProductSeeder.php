<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'id' => 1,
                'name' => 'Taza',
                'description' => 'Producto exclusivo',
                'price' => 200.00,
                'currency_id' => 1,
                'tax_cost' => 15.00,
                'manufacturing_cost' => 10.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'Vaso',
                'description' => 'Producto exclusivo',
                'price' => 100.00,
                'currency_id' => 1,
                'tax_cost' => 10.00,
                'manufacturing_cost' => 5.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'name' => 'Plato',
                'description' => 'Producto exclusivo',
                'price' => 250.00,
                'currency_id' => 1,
                'tax_cost' => 25.00,
                'manufacturing_cost' => 8.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($items as $item) {
            Product::updateOrCreate(['id' => $item['id']], $item);
        }

        }
}
