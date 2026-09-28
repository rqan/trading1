<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Casting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'series',
        'year',
        'rarity_tier',
        'base_value',
    ];
}
