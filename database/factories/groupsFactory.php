<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\groups;
class GroupsFactory extends Factory
{

    public function definition()
    {
        return [
            'name' => $this->faker->firstName,
            'description' => $this->faker->sentence,
            'picture' => $this->faker->imageUrl(200, 200),
            'privacy' => $this->faker->randomElement(['Public', 'Private']),
            'id_chat' => \App\Models\chat::factory(),
        ];
    }
}
