<?php

namespace Database\Factories;

use App\Models\Survivor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'survivor_id' => Survivor::factory()->notInfected()->create(),
            'flag_survivor_id' => '',
        ];
    }
}
