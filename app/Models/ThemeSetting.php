<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        "pos_logo",
        "pos_primary_color",
        "pos_secondary_color",
        "branch_id",
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
