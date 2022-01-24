<?php

namespace App\Http\Controllers;

use App\Actions\FlagSurvivorAction;
use App\Actions\MakeReportAction;
use App\Http\Requests\FlagSurvivorAsInfectedRequest;
use App\Models\Survivor;

class ReportController extends Controller
{
    public function index(MakeReportAction $action)
    {
        return $action->make();
    }

    public function flagSurvivorAsInfected(FlagSurvivorAsInfectedRequest $request, FlagSurvivorAction $action)
    {
        $survivor = Survivor::find($request->survivor_id);
        $flag_survivor = Survivor::find($request->flag_survivor_id);
        
        $report = $survivor->reports()
            ->where('flag_survivor_id', $flag_survivor->id)
            ->first();
        
        abort_if(
            $survivor->infected->was_infected || isset($report) || $survivor->id == $flag_survivor->id,
            403,
            'You can not do this!'
        );
        
        return $action->flagSurvivorAsInfected($survivor, $flag_survivor);
    }
}
