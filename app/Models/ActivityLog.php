<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activitylogs';

    protected $primaryKey = 'log_id';

    public const CREATED_AT = 'timestamp';

    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action_type',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'timestamp' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Helper to easily create an activity log record.
     */
    public static function log(string $actionType, string $description, ?int $userId = null): self
    {
        return self::create([
            'user_id' => $userId ?? auth()->id() ?? 1,
            'action_type' => $actionType,
            'description' => $description,
        ]);
    }
}
