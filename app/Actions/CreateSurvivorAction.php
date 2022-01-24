<?php

namespace App\Actions;

use App\Models\Survivor;
use Illuminate\Support\Facades\DB;

class CreateSurvivorAction
{
    public function create($input)
    {
        return DB::transaction(function () use($input) {
            $survivor = Survivor::create([
                'name' => $input['name'],
                'age' => $input['age'],
                'gender' => $input['gender'],
            ]);
            $survivor->lastLocation()->create([
                'latitude' => $input['latitude'],
                'longitude' => $input['longitude'],
            ]);
            $survivor->inventory()->create();
            $survivor->infected()->create();

            $this->storeItems($survivor, $input['items']);
            
            return $survivor->load('inventory', 'lastLocation', 'inventory.items', 'infected');
        });
    }

    private function storeItems($survivor, $items)
    {
        $items_for_attach = null;
        foreach($items as $item)
        {
            $items_for_attach[$item['id']] = ['qty' => $item['qty']];
        }
        $survivor->inventory->items()->attach($items_for_attach);
    }
}
