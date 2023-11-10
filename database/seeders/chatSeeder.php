<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class chatSeeder extends Seeder
{

    public function run()
    {
        \App\Models\chat::factory()
        ->count(105)
        ->create();
        \App\Models\chat_participation::factory()->create([
            "conversation_id" => "100",
            "messageable_id" => "11",
        ]);       
    }
}
