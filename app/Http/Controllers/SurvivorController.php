<?php

namespace App\Http\Controllers;

use App\Actions\CreateSurvivorAction;
use App\Http\Requests\StoreSurvivorRequest;
use App\Http\Requests\UpdateLastLocationRequest;
use App\Http\Requests\UpdateSurvivorRequest;
use App\Models\Survivor;

class SurvivorController extends Controller
{
    public function index()
    {
        return Survivor::with('lastLocation', 'infected')->get();
    }

    public function store(StoreSurvivorRequest $request, CreateSurvivorAction $action)
    {
        return $action->create($request->validated());
    }

    public function show(Survivor $survivor)
    {
        return $survivor->load('inventory', 'lastLocation', 'inventory.items', 'infected');
    }

    public function update(UpdateSurvivorRequest $request, Survivor $survivor)
    {
        $response = $survivor->update($request->validated());
        return $response ? ['message' => 'Updated'] : response()->json(['message' => 'Fail'], 500);
    }

    public function updateSurvivorLocation(UpdateLastLocationRequest $request, Survivor $survivor)
    {
        $response = $survivor->lastLocation->update($request->validated());
        return $response ? ['message' => 'Updated'] : response()->json(['message' => 'Fail'], 500);
    }
}
