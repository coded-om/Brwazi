<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName as FilamentHasName;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable implements FilamentUser, FilamentHasName
{
    use Notifiable;

    // Use plural table name going forward
    protected $table = 'admins';

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Allow access to Filament admin panel(s).
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['admin', 'super_admin', 'moderator']);
    }

    /**
     * Display name inside Filament.
     */
    public function getFilamentName(): string
    {
        return $this->name ?: $this->email;
    }

    /**
     * Check if admin has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if admin is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Get all available roles
     */
    public static function getRoles(): array
    {
        return ['admin', 'super_admin', 'moderator'];
    }
}
