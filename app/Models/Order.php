<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function dineInTable()
    {
        return $this->belongsTo(DineInTable::class, 'table_id');
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function salesman()
    {
        return $this->belongsTo(User::class, 'salesman_id');
    }

    public function customers()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function rider()
    {
        return $this->belongsTo(Rider::class, 'assign_to_rider'); // Specify the correct foreign key
    }
    
}
 