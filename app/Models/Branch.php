<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    
    protected $fillable = [
        'branch_state',
        'branch_city',
        'company_name',
        'branch_initial',
        'branch_name',
        'branch_code',
        'branch_address',
        'branch_web_address',
        'max_discount_percentage',
        'receipt_message',
        'feedback',
        'receipt_tagline',
        'riderOption',
        'onlineDeliveryOption',
        'DiningOption',
    ];
    
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function paymentMethod()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'branch_categories');
    }
    public function ThemeSettings()
    {
        return $this->belongsTo(ThemeSetting::class);
    }
}
 