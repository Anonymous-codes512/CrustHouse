<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');

    } 
    public function dineInTable()
    {
        return $this->belongsTo(DineInTable::class, 'table_id'); // Assuming you have a 'table_id' in the Cart table
    }

}
