<?php

namespace App\Models;

use App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'categoryImage',
        'categoryName',
        'branch_id',
    ];

    use HasFactory;
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_categories');
    }
}
