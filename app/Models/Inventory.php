<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    
    public function survivor()
    {
        return $this->belongsTo(Survivor::class);
    }

    public function items()
    {
        return $this
            ->belongsToMany(Item::class)
            ->withPivot('qty');
    }
}
