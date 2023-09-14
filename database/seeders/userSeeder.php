<?php

namespace Database\Seeders;
use App\Models\user;
use App\Models\country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
class userSeeder extends Seeder
{
    
    public function run()
    {
        {
          
            \App\Models\user::factory()
            ->has(Country::factory(), 'homeland')
            ->has(Country::factory(), 'residence')
            ->count(10)
            ->create();     

            \App\Models\User::factory(1)
            ->has(Country::factory(), 'homeland')
            ->has(Country::factory(), 'residence')
            ->count(1)
            ->create([
                "name" => "usuario",
                "email" => "usuario@email.com",
                'password' => Hash::make("12345678")
            ]);
        }

    }
}
