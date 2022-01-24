<?php

namespace App\Actions;

use App\Models\Report;

class FlagSurvivorAction
{
    public function flagSurvivorAsInfected($survivor, $flag_survivor)
    {
        if($flag_survivor->infected->was_infected) return ['message' => 'was infected'];
        
        $this->createReport($survivor, $flag_survivor);

        $count = $this->countReports($flag_survivor);
        
        return [
            'message' => 'reported',
            'count' => $count,
            'infected' => $flag_survivor->infected->was_infected
        ];
    }

    private function countReports($flag_survivor)
    {
        $count = $flag_survivor->infected->flags_count + 1;
        $count <= 4
        ? $flag_survivor->infected->update([
            'flags_count' => $count
        ])
        : $flag_survivor->infected->update([
            'flags_count' => $count,
            'was_infected' => true
        ]);

        return $count;
    }
    
    private function createReport($survivor, $flag_survivor)
    {
        Report::create([
            'survivor_id' => $survivor->id,
            'flag_survivor_id' => $flag_survivor->id
        ]);
    }
}
