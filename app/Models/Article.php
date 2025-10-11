<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Tags\HasTags;

class Article extends Model
{
    use HasFactory, HasTags, InteractsWithMedia, SoftDeletes, Searchable;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'published_at',
        'meta',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'meta' => 'array',
    ];

    const STATUS_DRAFT = 'draft';

    const STATUS_PUBLISHED = 'published';

    const STATUS_ARCHIVED = 'archived';

    protected static function booted(): void
    {
        static::creating(function (Article $article): void {
            if (! $article->slug) {
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
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,    // related model
            'article_category', // pivot table
            'article_id',       // foreign key on pivot referencing articles
            'category_id'       // foreign key on pivot referencing categories
        );
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

    // Media Library
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->useDisk('public'); // configure disk as needed
        $this->addMediaCollection('cover')->singleFile()->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 300, 300)
            ->format('webp');

        $this->addMediaConversion('medium')
            ->fit(Fit::Contain, 800, 800)
            ->format('webp');

        $this->addMediaConversion('large')
            ->fit(Fit::Contain, 1600, 1600)
            ->format('webp');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => (string) $this->title,
            'content' => (string) $this->content,
            'published_at' => optional($this->published_at)?->toDateTimeString(),
        ];
    }

    public function searchableAs(): string
    {
        return 'articles';
    }
}
