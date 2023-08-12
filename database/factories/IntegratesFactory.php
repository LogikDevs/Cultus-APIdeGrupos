<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class IntegratesFactory extends Factory
{

    public function definition()
    {
        return [
            'id_user' => \App\Models\user::factory(),
            'id_group' => \App\Models\groups::factory(),
        ];
    }
}
