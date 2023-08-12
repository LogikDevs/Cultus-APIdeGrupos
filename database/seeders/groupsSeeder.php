<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class groupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\groups::factory()->count(100)->create();
    }
}
