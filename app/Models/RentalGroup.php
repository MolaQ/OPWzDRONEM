<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class RentalGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Members of this rental group
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'rental_group_members')
            ->withTimestamps();
    }

    /**
     * Alias for members (for compatibility)
     */
    public function users(): BelongsToMany
    {
        return $this->members();
    }

    /**
     * User who processed the rental (from first rental)
     */
    public function rentedByUser()
    {
        return $this->hasOneThrough(
            User::class,
            Rental::class,
            'rental_group_id', // Foreign key on rentals table
            'id', // Foreign key on users table
            'id', // Local key on rental_groups table
            'rented_by_user_id' // Local key on rentals table
        )->orderBy('rentals.created_at', 'asc')->limit(1);
    }

    /**
     * Rentals by this group
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Active rentals (not returned yet)
     */
    public function activeRentals(): HasMany
    {
        return $this->rentals()->whereNull('returned_at');
    }

    /**
     * Generate group name from members
     */
    public static function generateName(array $userIds): string
    {
        $users = User::whereIn('id', $userIds)->pluck('name')->toArray();

        if (count($users) === 1) {
            return $users[0];
        }

        return implode(', ', array_slice($users, 0, 2)) . (count($users) > 2 ? ' +' . (count($users) - 2) : '');
    }
}
