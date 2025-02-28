<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_method',
        'order_type',
        'discount_type',
        'branch_id',
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
