<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable {
        hasRole as traitHasRole;
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'avatar_url',
        'last_login_at',
        'last_login_ip',
        'role',
        'is_active',
    ];

    protected $appends = ['role', 'all_roles', 'created_at_human', 'all_permissions'];

    /**
     * Always return the first role name.
     * Fallback to the 'role' column if Spatie roles are not loaded.
     */
    public function getRoleAttribute(): string
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->first()?->name ?? $this->getAttributeFromArray('role') ?? 'user';
        }

        return $this->getAttributeFromArray('role') ?? 'user';
    }

    /**
     * Get all role names from Spatie.
     */
    public function getAllRolesAttribute(): array
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->pluck('name')->toArray();
        }

        $singleRole = $this->getAttributeFromArray('role');

        return $singleRole ? [$singleRole] : [];
    }

    /**
     * Get all permission names from Spatie.
     */
    public function getAllPermissionsAttribute(): array
    {
        if ($this->relationLoaded('permissions') || $this->relationLoaded('roles.permissions')) {
            return $this->getAllPermissions()->pluck('name')->toArray();
        }

        return [];
    }

    public function getCreatedAtHumanAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user's detailed profile.
     */
    public function detail(): HasOne
    {
        return $this->hasOne(UserDetail::class);
    }

    /**
     * Get the user's internship applications.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function savedInternships(): HasMany
    {
        return $this->hasMany(SavedInternship::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function companyMemberships(): HasMany
    {
        return $this->hasMany(CompanyMember::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_members')
            ->withPivot('role', 'is_active')
            ->withTimestamps();
    }

    /**
     * Check if the user has a specific role.
     * Supports both the 'role' column and Spatie roles.
     */
    public function hasRole($role, $guard = null): bool
    {
        // Support array of roles
        if (is_array($role)) {
            foreach ($role as $r) {
                if ($this->hasRole($r, $guard)) {
                    return true;
                }
            }

            return false;
        }

        // Check the 'role' column first (for simplicity/legacy)
        if ($this->getAttributeFromArray('role') === $role) {
            return true;
        }

        // Fallback to Spatie's role check if needed
        return $this->traitHasRole($role, $guard);
    }
}
