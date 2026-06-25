<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PosTab extends Model
{
    protected $primaryKey = 'tab_id';

    public $timestamps = false;

    protected $fillable = [
        'tab_name',
        'tab_type',
        'guest_id',
        'folio_id',
        'booking_id',
        'room_id',
        'status',
        'payment_method',
        'subtotal',
        'total',
        'opened_by',
        'closed_by',
        'opened_at',
        'closed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosTabItem::class, 'tab_id', 'tab_id');
    }

    public function order(): HasOne
    {
        return $this->hasOne(PosOrder::class, 'tab_id', 'tab_id');
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'guest_id', 'guest_id');
    }

    public function folio(): BelongsTo
    {
        return $this->belongsTo(Folio::class, 'folio_id', 'folio_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    public function openedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by', 'user_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->sum('line_total');
        $this->update([
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);
    }
}
