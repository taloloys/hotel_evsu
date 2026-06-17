<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChargeCode extends Model
{
    protected $table = 'chargecodes';

    protected $primaryKey = 'charge_code';

    public $timestamps = false;

    protected $fillable = [
        'description',
        'category',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'charge_code', 'charge_code');
    }
}
