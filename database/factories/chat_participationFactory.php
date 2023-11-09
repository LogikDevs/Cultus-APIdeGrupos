<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class chat_participationFactory extends Factory
{

    public function definition()
    {

        return [
            'conversation_id' => \App\Models\chat::factory(),
            'messageable_id' => \App\Models\user::factory(),
            'messageable_type' => 'App\Models\user'
        ];
    }
}
