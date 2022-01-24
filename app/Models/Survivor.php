<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survivor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'age', 'gender'
    ];

    public function lastLocation()
    {
        return $this->hasOne(LastLocation::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function infected()
    {
        return $this->hasOne(Infected::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
