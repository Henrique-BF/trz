<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = config('items');

        foreach($items as $item)
        {
            Item::create([
                'name' => $item['name'],
                'points' => $item['points']
            ]);
        }
    }
}
