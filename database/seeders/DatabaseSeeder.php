<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\user;
use App\Models\country;
use App\Models\groups;
use App\Models\integrates;
class DatabaseSeeder extends Seeder
{

    public function run()
    {
        $this->call(countrySeeder::class);
        $this->call(groupsSeeder::class);
        $this->call(integratesSeeder::class);
        $this->call(userSeeder::class);
    }
}
