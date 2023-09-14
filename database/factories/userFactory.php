<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\user;
use Illuminate\Database\Eloquent\Factories\Factory;

final class userFactory extends Factory
{

    protected $model = user::class;

    public function definition(): array
    {
     
        return [
            'name' => $this->faker->firstName,
            'surname' => $this->faker->lastName,
            'age' => $this->faker->numberBetween(0, 100),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'profile_pic' => $this->faker->imageUrl(200, 200),
            'description' => $this->faker->sentence,
            'homeland' => \App\Models\country::factory(),
            'residence' => \App\Models\country::factory(),
        ];
    }
}
