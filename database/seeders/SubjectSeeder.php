<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subjects')->insert([
            ['name' => 'Maths', 'order' => 0],
            ['name' => 'Science', 'order' => 1],
            ['name' => 'History', 'order' => 2],
        ]);
    }
}
