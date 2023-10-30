<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class chatFactory extends Factory
{

    public function definition()
    {
        return [
            'private' => $this->faker->boolean(),
            'direct_message' => $this->faker->boolean(),
        ];
    }
}
