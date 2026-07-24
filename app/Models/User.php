<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // public function isAdmin(): bool
    // {
    //     return $this->role === 'admin';
    // }

    // public function isReceptionist(): bool
    // {
    //     return $this->role === 'receptionist';
    // }

    // public function isHousekeeping(): bool
    // {
    //     return $this->role === 'housekeeping';
    // }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // public function hasAnyRole(array $roles): bool
    // {
    //     return in_array($this->role, $roles, true);
    // }

    // public function canManageFrontDesk(): bool
    // {
    //     return $this->hasAnyRole(['admin', 'receptionist']);
    // }

    // public function canManageHousekeeping(): bool
    // {
    //     return $this->hasAnyRole(['admin', 'housekeeping']);
    // }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function canAccessModule(
        string $module,
        string $action = 'read'
    ): bool {
        $role = $this->role;

        if (! $role) {
            return false;
        }
        // Owner has full access everywhere.
        if ($role->is_super_admin) {
            return true;
        }

        $allowedActions = [
            'read',
            'create',
            'update',
            'delete',
        ];

        if (! in_array($action, $allowedActions, true)) {
            return false;
        }

        $permissionColumn = 'can_'.$action;

        if (! $role->{$permissionColumn}) {
            return false;
        }

        return $role->modules()
            ->where('modules.slug', $module)
            ->exists();
    }
}
