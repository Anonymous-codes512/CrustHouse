<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
    use HasFactory;

    /**
     * Get the user associated with the rider.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


