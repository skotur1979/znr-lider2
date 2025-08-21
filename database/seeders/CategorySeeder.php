<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert(
            ['name' => 'Klime'],
            ['name' => 'Hidranti'],
            ['name' => 'Gromobrani'],
            ['name' => 'Dizalice'],
            ['name' => 'Posude pod tlakom'],
            ['name' => 'Sigurnosni ventili'],
        );
    }
}
