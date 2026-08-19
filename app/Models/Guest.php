<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    protected $primaryKey = 'guest_id';

    public const UPDATED_AT = null;

    protected $fillable = [
        'last_name',
        'first_name',
        'address_line1',
        'address_line2',
        'contact_number',
        'email',
        'guest_type',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function scopeRealGuests($query)
    {
        return $query->where('guest_type', '!=', 'SYSTEM');
    }

    public function scopeSystemAccounts($query)
    {
        return $query->where('guest_type', 'SYSTEM');
    }

    public function folios(): HasMany
    {
        return $this->hasMany(Folio::class, 'guest_id', 'guest_id');
    }
}
