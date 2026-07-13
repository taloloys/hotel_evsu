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

    public static function log(string $actionType, string $description, ?int $userId = null): ?self
    {
        $resolvedUserId = $userId ?? auth()->id();

        if ($resolvedUserId && ! User::where('user_id', $resolvedUserId)->exists()) {
            $resolvedUserId = null;
        }

        if (! $resolvedUserId) {
            $resolvedUserId = User::first()?->user_id;
        }

        if (! $resolvedUserId) {
            return null;
        }

        return self::create([
            'user_id' => $resolvedUserId,
            'action_type' => $actionType,
            'description' => $description,
        ]);
    }
}
