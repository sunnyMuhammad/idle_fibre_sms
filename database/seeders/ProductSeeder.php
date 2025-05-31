<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 2000; $i++) {
            DB::table('products')->insert([
            'name' => 'Product ' . $i,
            'category_id' => rand(1, 5), // Make sure categories with these IDs exist
            'unit' => rand(10, 100),
            'minimum_stock' => rand(1, 10),
            'unit_type' => collect(['KG', 'Feet', 'Coel', 'pcs', 'pkt'])->random(),
            'parts_no' => 'P' . str_pad($i, 4, '0', STR_PAD_LEFT),
            'rack_no' => 'R' . rand(1, 10),
            'column_no' => 'C' . rand(1, 5),
            'row_no' => 'RW' . rand(1, 5),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            ]);
        }
    }
}
