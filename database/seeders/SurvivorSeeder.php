<?php

namespace Database\Seeders;

use App\Models\Survivor;
use Illuminate\Database\Seeder;

class SurvivorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Survivor::factory()->notInfected()->create();
    }
}
