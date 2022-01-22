<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurvivorRequest;
use App\Http\Requests\UpdateLastLocationRequest;
use App\Http\Requests\UpdateSurvivorRequest;
use App\Models\Survivor;
use Illuminate\Support\Facades\DB;

class SurvivorController extends Controller
{
    public function index()
    {
        return Survivor::all();
    }

    public function store(StoreSurvivorRequest $request)
    {
        return DB::transaction(function () use($request) {
            $survivor = Survivor::create($request->only(['name', 'age', 'gender']));
            $survivor->lastLocation()->create($request->only(['latitude', 'longitude']));
            $survivor->inventory()->create();
            $survivor->infected()->create();
            foreach($request->items as $item)
            {
                $survivor->inventory->items()->attach(
                    $item['id'],
                    ['qty' => $item['qty']]
                );
            }
            return $survivor->load('inventory', 'lastLocation', 'inventory.items');
        });
    }

    public function show(Survivor $survivor)
    {
        return $survivor->load('inventory', 'lastLocation', 'inventory.items', 'infected');
    }

    public function update(UpdateSurvivorRequest $request, Survivor $survivor)
    {
        return $survivor->update($request->validated());
    }

    public function updateSurvivorLocation(UpdateLastLocationRequest $request, Survivor $survivor)
    {
        return $survivor->lastLocation->update($request->validated());
    }

    public function flagSurvivorAsInfected(Survivor $survivor)
    {
        if($survivor->infected->was_infected) return ['message' => 'was infected'];
        
        $count = $survivor->infected->flags_count + 1;
        $count <= 4
        ? $survivor->infected->update([
            'flags_count' => $count
        ])
        : $survivor->infected->update([
            'flags_count' => $count,
            'was_infected' => true
        ]);
        
        return [
            'message' => 'reported',
            'count' => $count,
            'infected' => $survivor->infected->was_infected
        ];
    }
}
