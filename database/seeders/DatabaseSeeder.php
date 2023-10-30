<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\user;
use App\Models\country;
use App\Models\groups;
use App\Models\chat;
use App\Models\integrates;
use Laravel\Passport\Client;
class DatabaseSeeder extends Seeder
{

    public function run()
    {
        $this->call(countrySeeder::class);
        $this->call(chatSeeder::class);
        $this->call(groupsSeeder::class);
        $this->call(integratesSeeder::class);
        $this->call(userSeeder::class);
        Client::create([
            'id' => 100,
            'name' => 'Tests',
            'secret' => "wsBa0mp4jwSTYssUGHX5xoqD9IC0X95Gfpg0w3uY",
            'redirect' => 'http://localhost',
            'provider' => 'users',
            'personal_access_client' => false,
            'password_client' => true,
            'revoked' => false
        ]);
    }
}
