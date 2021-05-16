<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class InventoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('inventories')->insert([
            [
                'product_id' => 1,
                'deposit_id' => 1,
                'quantity' => 5,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
            [
                'product_id' => 1,
                'deposit_id' => 2,
                'quantity' => 7,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
            [
                'product_id' => 2,
                'deposit_id' => 1,
                'quantity' => 8,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
            [
                'product_id' => 2,
                'deposit_id' => 2,
                'quantity' => 0,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
            [
                'product_id' => 3,
                'deposit_id' => 1,
                'quantity' => 4,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
            [
                'product_id' => 3,
                'deposit_id' => 1,
                'quantity' => 10,
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ],
        ]);
    }
}
