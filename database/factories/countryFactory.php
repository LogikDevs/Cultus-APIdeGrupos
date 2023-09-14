<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\country;
use Illuminate\Database\Eloquent\Factories\Factory;


final class countryFactory extends Factory
{
 
    protected $model = \App\Models\country::class;

    public function definition(): array
    {
        return [
            'country_name' =>$this->faker->country()
        ];
    }
}
