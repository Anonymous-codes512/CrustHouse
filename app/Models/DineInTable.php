<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DineInTable extends Model
{
    use HasFactory;
    protected $fillable = [
        "table_number",
        "max_sitting_capacity",
        "table_status",
        "branch_id",
    ];
    public function carts()
    {
        return $this->hasMany(Cart::class, 'table_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id');
    }
}
