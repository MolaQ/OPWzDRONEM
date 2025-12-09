<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'description', 'active'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Wychowawcy przypisani do grupy
     */
    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'group_supervisors')
            ->withTimestamps();
    }

    /**
     * Instruktorzy przypisani do grupy
     */
    public function instructors()
    {
        return $this->belongsToMany(User::class, 'group_instructors')
            ->withTimestamps();
    }
}
