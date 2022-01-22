<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'latitude', 'longitude'
    ];

    public function survivor()
    {
        return $this->belongsTo(Survivor::class);
    }
}
