<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleVersion extends Model
{
    protected $fillable = [
        'article_id',
        'version',
        'created_by',
        'title',
        'excerpt',
        'content',
        'status',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
