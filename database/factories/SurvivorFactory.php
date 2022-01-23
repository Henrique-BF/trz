<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\LastLocation;
use App\Models\Report;
use App\Models\Survivor;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurvivorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {   
        return [
            'name' => $this->faker->firstNameMale(),
            'age' => $this->faker->numberBetween(20, 50),
            'gender' => 'male',
        ];
    }

    public function infected()
    {
        return $this->afterCreating(function (Survivor $survivor) {
            $survivor->infected()->create([
                'was_infected' => true,
                'flags_count' => 5,
            ]);
            Report::factory()->count(5)->create([
                'flag_survivor_id' => $survivor->id,
            ]);
        });
    }
    
    public function notInfected()
    {
        return $this->afterCreating(function (Survivor $survivor) {
            $survivor->infected()->create();
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (Survivor $survivor) {
            $survivor->lastLocation()->create(LastLocation::factory()->make()->toArray()); 
            $survivor->inventory()->create();
            $item = Item::all()->random();
            $survivor->inventory->items()->attach(
                $item->id,
                ['qty' => $this->faker->numberBetween(1, 10)]
            );
        });
    }
}
