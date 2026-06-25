<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosInventoryLog extends Model
{
    protected $primaryKey = 'log_id';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'change_qty',
        'reason',
        'reference_type',
        'reference_id',
        'user_id',
        'notes',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(PosProduct::class, 'product_id', 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
