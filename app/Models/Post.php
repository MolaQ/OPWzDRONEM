<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'author_id',
        'is_published',
        'published_at',
        'image',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the author of the post
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get all comments for the post
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get all reactions for the post
     */
    public function reactions()
    {
        return $this->hasMany(PostReaction::class);
    }

    /**
     * Get likes count
     */
    public function likes()
    {
        return $this->reactions()->where('type', 'like');
    }

    /**
     * Get dislikes count
     */
    public function dislikes()
    {
        return $this->reactions()->where('type', 'dislike');
    }
}
