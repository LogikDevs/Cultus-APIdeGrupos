<?php

namespace Database\Seeders;
use App\Models\user;
use App\Models\country;
use Illuminate\Database\Seeder;

class countrySeeder extends Seeder
{

    public function run()
    {
        \App\Models\country::factory()->count(100)->create();
    }
}
