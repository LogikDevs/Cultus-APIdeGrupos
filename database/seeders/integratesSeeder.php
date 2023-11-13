<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class integratesSeeder extends Seeder
{

    public function run()
    {
        \App\Models\integrates::factory()->create([
            "id_user" => 1,
            "id_group" => 100,
            "rol"=> "Member",
        ]);
        \App\Models\integrates::factory()->create([
            "id_user" => 2,
            "id_group" => 100,
            "rol"=> "Admin",
        ]);
        \App\Models\integrates::factory()->create([
            "id_user" => 11,
            "id_group" => 100,
            "rol"=> "Admin",
        ]);
        \App\Models\integrates::factory()->create([
            "id_user" => 2,
            "id_group" => 101,
            "rol"=> "Admin",
        ]);
        \App\Models\integrates::factory()->create([
            "id_user" => 11,
            "id_group" => 101,
            "rol"=> "Member",
        ]);
        \App\Models\integrates::factory()->count(100)->create();
    }
}
