<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserModulePreference extends Model
{
    protected $table = 'user_module_preferences';

    protected $fillable = [
        'user_id',
        'module_key',
        'is_visible',
    ];

    protected static function booted(): void
    {
        static::saved(function ($preference): void {
            User::$requestSidebarPreferences = [];
        });

        static::deleted(function ($preference): void {
            User::$requestSidebarPreferences = [];
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
        ];
    }

    /**
     * Get the user that owns this preference.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
