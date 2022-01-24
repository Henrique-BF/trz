<?php

namespace App\Http\Controllers;

use App\Actions\TradeAction;
use App\Http\Requests\TradeRequest;
use App\Models\Item;
use App\Models\Survivor;
use Exception;

class TradeController extends Controller
{
    public function indexItems()
    {
        return Item::all();
    }

    public function trade(TradeRequest $request, TradeAction $action)
    {
        $survivor_1 = Survivor::find($request->survivor_1_id);
        $survivor_2 = Survivor::find($request->survivor_2_id);
        $items_survivor_1 = $request->items_survivor_1;
        $items_survivor_2 = $request->items_survivor_2;

        abort_if($survivor_1->infected->was_infected, 403, 'Danger. Survivor infected!!');
        abort_if($survivor_2->infected->was_infected, 403, 'Danger. Survivor infected!!');

        try{
            $response = $action->hundle($survivor_1, $survivor_2, $items_survivor_1, $items_survivor_2);
        }
        catch(Exception $exception){
            return response()->json(['message' => $exception->getMessage()], 422);
        }
        return $response;
    }
}
