<?php

namespace App\Actions;

use App\Models\Infected;
use App\Models\Item;
use App\Models\Survivor;

class MakeReportAction
{
    public function make()
    {
        $survivors = Survivor::all();
        $infecteds = Infected::where('was_infected', true)->get();
        $items = Item::all();

        $survivors_count = $survivors->count();
        $infecteds_count = $infecteds->count();
        $percent_survivors_infected = ($infecteds_count / $survivors_count) * 100;
        
        $avarege = $this->getAvaregeItemsPerSurvivor($items, $survivors_count);

        $total_points_lost = $this->getTotalPointsLost($infecteds);
        
        return [
            'Infected survivors' => $percent_survivors_infected,
            'Not infected survivors' => 100 - $percent_survivors_infected,
            'Average amount of each kind of resource by the survivor' => $avarege,
            'Points lost' => $total_points_lost,
        ];
    }

    private function getAvaregeItemsPerSurvivor($items, $total_survivors)
    {
        $avarege = null;
        foreach($items as $item)
        {
            $total = 0;
            foreach($item->inventorys as $inventory)
            {
                $total = $total + $inventory->pivot->qty;
            }
            $avarege[$item->name] = $total / $total_survivors;
        }
        return $avarege;
    }

    private function getTotalPointsLost($infecteds)
    {
        $total_points_lost = 0;
        foreach($infecteds as $infected)
        {
            $items_lost = $infected->survivor->inventory->items;
            foreach($items_lost as $item_lost)
            {
                $total_points_lost += $item_lost->pivot->qty * $item_lost->points;
            }
        }
        return $total_points_lost;
    }
}