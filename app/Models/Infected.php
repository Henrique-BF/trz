<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infected extends Model
{
    use HasFactory;

    protected $fillable = [
        'was_infected', 'flags_count'
    ];

    public function survivor()
    {
        return $this->belongsTo(Survivor::class);
    }
}
