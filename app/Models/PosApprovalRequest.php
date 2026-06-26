<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosApprovalRequest extends Model
{
    protected $table = 'pos_approval_requests';

    protected $primaryKey = 'request_id';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'tab_id',
        'request_type',
        'status',
        'requested_by',
        'resolved_by',
        'reason',
        'created_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(PosOrder::class, 'order_id', 'order_id');
    }

    public function tab(): BelongsTo
    {
        return $this->belongsTo(PosTab::class, 'tab_id', 'tab_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by', 'user_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by', 'user_id');
    }
}
