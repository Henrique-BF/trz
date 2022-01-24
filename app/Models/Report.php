<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'survivor_id', 'flag_survivor_id'
    ];

    public function survivor()
    {
        return $this->belongsTo(Survivor::class);
    }
}
