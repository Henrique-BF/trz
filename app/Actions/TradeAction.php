<?php

namespace App\Actions;

use App\Models\Item;
use App\Models\Survivor;
use Illuminate\Support\Facades\DB;

class TradeAction
{
    protected $survivor_1;
    protected $survivor_2;

    public function hundle(Survivor $survivor_1, Survivor $survivor_2, $ids_and_qty_1, $ids_and_qty_2)
    {
        return DB::transaction(function() use($survivor_1, $survivor_2, $ids_and_qty_1, $ids_and_qty_2){
            $items_for_trade_survivor_1 = $this->takeItems($ids_and_qty_1);
            $items_for_trade_survivor_2 = $this->takeItems($ids_and_qty_2);
            
            $total_trade_survivor_1 = $this->calculateTotalPoints($items_for_trade_survivor_1, $ids_and_qty_1);
            $total_trade_survivor_2 = $this->calculateTotalPoints($items_for_trade_survivor_2, $ids_and_qty_2);
            if($total_trade_survivor_1 != $total_trade_survivor_2)
            {
                return response()->json([
                    'message' => 'NOT ENOUGH POINTS STRANGER!!! Survivor 1 offer '.$total_trade_survivor_1.' points and survivor 2 offer '.$total_trade_survivor_2.' points for trade.'
                ], 422);
            }

            $inventory_survivor_1 = $survivor_1->inventory;
            $inventory_survivor_2 = $survivor_2->inventory;
            
            $error = $this->compareItems($inventory_survivor_1, $ids_and_qty_1); 
            if($error) return response()->json([
                'message' => 'NOT ENOUGH ITEMS! Stranger 1'
            ], 422); 

            $error = $this->compareItems($inventory_survivor_2, $ids_and_qty_2);
            if($error) return response()->json([
                'message' => 'NOT ENOUGH ITEMS! Stranger 2'
            ], 422);

            $this->makeTrade($inventory_survivor_1, $ids_and_qty_2, $ids_and_qty_1);
            $this->makeTrade($inventory_survivor_2, $ids_and_qty_1, $ids_and_qty_2);

            return ['message' => 'HEHEHEA! Thank you. Is that all, stranger? Come back any time.'];
        });
        
    }

    private function makeTrade($inventory, $ids_and_qty_in, $ids_and_qty_out)
    {
        foreach($ids_and_qty_in as $id_qty_in)
        {
            $item = $inventory->items->find($id_qty_in['id']);
            !isset($item)
                ? $inventory->items()->attach($id_qty_in['id'], ['qty' => $id_qty_in['qty']])
                : $item->pivot->update(['qty' => $item->pivot->qty + $id_qty_in['qty']]);
        }
        foreach($ids_and_qty_out as $id_qty_out)
        {
            $item = $inventory->items->find($id_qty_out['id']);
            $item->pivot->qty == $id_qty_out['qty']
                ? $inventory->items()->detach($id_qty_out['id'])
                : $item->pivot->update(['qty' => $item->pivot->qty - $id_qty_out['qty']]);
        }
    }

    private function compareItems($inventory, $ids_and_qty)
    {   
        foreach($ids_and_qty as $id_qty)
        {
            $match = $inventory->items->find($id_qty['id']);
            if(!isset($match)) return true;
            if($match->pivot->qty < $id_qty['qty']) return true;
        }
        return false;
    }

    private function takeItems($items)
    {
        $keys = null;
        foreach($items as $item)
        {
            $keys[] = $item['id'];
        }
        return Item::find($keys);
    }

    private function calculateTotalPoints($items_for_trade, $ids_and_qty)
    {
        $total = 0;
        foreach($ids_and_qty as $id_qty)
        {
            $total = $total + $this->calculatePoints($items_for_trade->find($id_qty['id']), $id_qty['qty']);
        }
        return $total;
    }

    private function calculatePoints($item, $qty)
    {
        return $item->points * $qty;
    }
}
