<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method bool hasRole(string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles, string|null $guard = null)
 * @method bool hasAnyRole(string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles, string|null $guard = null)
 * @method bool hasAllRoles(string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles, string|null $guard = null)
 * @method $this assignRole(...$roles)
 * @method $this removeRole($role)
 * @method $this syncRoles(...$roles)
 * @method bool hasPermissionTo(string|int|\Spatie\Permission\Contracts\Permission $permission, string|null $guard = null)
 * @method bool can(string $ability, mixed $arguments = [])
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'barcode', 'password',
        'pilot_license', 'operator_license', 'license_expiry_date', 'group_id', 'active'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Rental groups this user is member of
     */
    public function rentalGroups()
    {
        return $this->belongsToMany(RentalGroup::class, 'rental_group_members')
            ->withTimestamps();
    }

    /**
     * All rentals this user is involved in (through rental groups)
     */
    public function rentals()
    {
        return Rental::whereIn('rental_group_id', $this->rentalGroups()->pluck('id'));
    }

    /**
     * Active rentals (not returned yet)
     */
    public function activeRentals()
    {
        return $this->rentals()->whereNull('returned_at');
    }

    /**
     * Osiągnięcia użytkownika (gwiazdki)
     */
    public function achievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'license_expiry_date' => 'date',
        ];
    }

    /**
     * Scope a query to search by barcode
     */
    public function scopeByBarcode($query, string $barcode)
    {
        return $query->where('barcode', $barcode);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
