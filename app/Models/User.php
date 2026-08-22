<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password_hash',
        'full_name',
        'email',
        'role_id',
        'is_active',
    ];

    /**
     * Get the role associated with the user.
     *
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function shiftSchedules(): HasMany
    {
        return $this->hasMany(ShiftSchedule::class, 'user_id', 'user_id');
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class, 'user_id', 'user_id');
    }

    /**
     * Get direct permissions assigned to this user.
     *
     * @return BelongsToMany<Permission, $this>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'userpermissions',
            'user_id',
            'permission_id'
        );
    }

    /**
     * Cache permissions list for the user during the request lifecycle.
     *
     * @var array<string>|null
     */
    protected ?array $resolvedPermissions = null;

    public function hasPermission(string $permissionKey): bool
    {
        if (! $this->role?->is_active) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->resolvedPermissions === null) {
            $rolePerms = $this->role->permissions()
                ->where('permissions.is_active', true)
                ->pluck('permissions.permission_key')
                ->toArray();

            $directPerms = $this->permissions()
                ->where('permissions.is_active', true)
                ->pluck('permissions.permission_key')
                ->toArray();

            $this->resolvedPermissions = array_unique(array_merge($rolePerms, $directPerms));
        }

        return in_array($permissionKey, $this->resolvedPermissions, true);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
            'is_active' => 'boolean',
        ];
    }


    public function isSuperAdmin(): bool
    {
        return (bool) ($this->role?->role_name === 'SUPER_ADMIN' || ($this->role?->is_system_admin && $this->role?->role_name !== 'ADMIN'));
    }

    public function isAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->role?->role_name === 'ADMIN' || $this->hasPermission('manage-users');
    }


    public function refresh()
    {
        $this->resolvedPermissions = null;

        return parent::refresh();
    }

    public function fresh($with = [])
    {
        $this->resolvedPermissions = null;

        return parent::fresh($with);
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }
}
