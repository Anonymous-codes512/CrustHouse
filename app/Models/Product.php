<?php

namespace App\Models;

use App\Models\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'productImage',
        'productName',
        'productVariation',
        'productPrice',
        'category_name',
        'category_id',
        'branch_id',
    ];
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function handlers()
    {
        return $this->hasMany(handler::class);
    }

    public function deals()
    {
        return $this->belongsToMany(Deal::class, 'handlers')->withPivot('product_quantity', 'product_total_price');
    }

    public function recipe()
    {
        return $this->hasOne(Recipe::class);
    }
}
