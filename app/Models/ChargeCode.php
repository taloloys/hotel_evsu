<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChargeCode extends Model
{
    protected $table = 'chargecodes';

    protected $primaryKey = 'charge_code';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'charge_code',
        'description',
        'category',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'charge_code', 'charge_code');
    }
}
