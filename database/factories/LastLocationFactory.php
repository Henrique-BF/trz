<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LastLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {   
        return [
            'latitude' => $this->faker->numberBetween(-90, 90),
            'longitude' => $this->faker->numberBetween(-90, 90),
        ];
    }
}
