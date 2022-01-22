<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\Survivor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SurvivorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, Survivor $survivor)
    {
        //
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, Survivor $survivor)
    {
        //
    }

    public function report(Survivor $survivor, Survivor $flag_survivor)
    {
        $report = Report::where('survivor_id', $survivor->id)
            ->where('flag_survivor_id', $flag_survivor->id)
            ->first();
        return !($survivor->infected->was_infected || isset($report));
    }
}
