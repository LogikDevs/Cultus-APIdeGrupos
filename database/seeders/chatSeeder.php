<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class chatSeeder extends Seeder
{

    public function run()
    {
        \App\Models\chat::factory()
        ->count(100)
        ->create();     
    }
}
