<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\chat;
class groupsSeeder extends Seeder
{
    public function run()
    {
        \App\Models\groups::factory()->create([
            "id_group" => "1",
            "name" => "grupoTest",
        ]);
        \App\Models\groups::factory()
        ->has(Chat::factory(), 'id_chat')
        ->count(100)
        ->create();      
    }
}
