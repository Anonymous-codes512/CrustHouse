<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchCategory extends Model
{
    protected $fillable = [
        'category_id',
        'branch_id',
    ];
    use HasFactory;
    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
