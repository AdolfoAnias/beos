<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'id' => 1,
                'name' => 'Dolar estadounidense',
                'symbol' => 'USD',
                'exchange_rate' => 2.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'Euro',
                'symbol' => 'EUR',
                'exchange_rate' => 2.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'name' => 'Libra esterlina',
                'symbol' => 'GBP',
                'exchange_rate' => 2.00,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($items as $item) {
            Currency::updateOrCreate(['id' => $item['id']], $item);
        };

    }
}
