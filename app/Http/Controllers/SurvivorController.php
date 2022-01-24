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
        return Survivor::with('lastLocation', 'infected')->get();
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
            return $survivor->load('inventory', 'lastLocation', 'inventory.items', 'infected');
        });
    }

    public function show(Survivor $survivor)
    {
        return $survivor->load('inventory', 'lastLocation', 'inventory.items', 'infected');
    }

    public function update(UpdateSurvivorRequest $request, Survivor $survivor)
    {
        $response = $survivor->update($request->validated());
        return $response ? ['message' => 'Updated'] : ['message' => 'Fail']; 
    }

    public function updateSurvivorLocation(UpdateLastLocationRequest $request, Survivor $survivor)
    {
        $response = $survivor->lastLocation->update($request->validated());
        return $response ? ['message' => 'Updated'] : ['message' => 'Fail'];
    }
}
