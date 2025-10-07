<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'published_at',
        'meta'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'meta' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function (Article $article) {
            if (empty($article->slug)) {
                $base = Str::slug($article->title);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$base}-{$i}";
                    $i++;
                }
                $article->slug = $slug;
            }
        });
    }

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }

    public function versions()
    {
        return $this->hasMany(ArticleVersion::class);
    }

    // Scopes
    public function scopePublished($q)
    {
        return $q->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
