<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Ball & Bearing', 'Stationery', 'Sewing & printing', 'Loom F SN-4', 'Loom F SN-6 m' , 'Loom LSL-610 m', 'Loom LSL-620 m', 'Tape Line  Machine 1 & 2', 'Electrical(General)', 'Nut and Bolt'];

    foreach ($categories as $index => $name) {
        DB::table('categories')->insert([
            'name' => $name
        ]);
    }
    }
}
