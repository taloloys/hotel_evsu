<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageShowcase extends Model
{
    protected $fillable = [
        'type',
        'title',
        'category',
        'price_rate',
        'capacity',
        'badge',
        'timing',
        'icon',
        'images',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
