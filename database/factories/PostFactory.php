<?php

namespace Database\Factories;

use App\Models\user;
use App\Models\post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition()
    {
        $text = $this->faker->paragraph();
        $text = Str::limit($text, 500);

        return [
            'fk_id_user' => \App\Models\user::factory(),
            'fk_id_group' => \App\Models\groups::factory(),
            'text' => $text,
            'location' => \App\Models\country::factory(),
            'date' => $this->faker->dateTime()
        ];
    }
}
