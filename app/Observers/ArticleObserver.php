<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\ArticleVersion;
use Illuminate\Support\Facades\Auth;

class ArticleObserver
{
    public function created(Article $article): void
    {
        ArticleVersion::create([
            'article_id' => $article->id,
            'version'    => 1,
            'created_by' => Auth::id(),
            'title'      => $article->title,
            'excerpt'    => $article->excerpt,
            'content'    => $article->content,
            'status'     => $article->status,
        ]);
    }

    public function updating(Article $article): void
    {
        // Save a new version from the ORIGINAL data before update
        $last = $article->versions()->max('version') ?? 1;

        ArticleVersion::create([
            'article_id' => $article->id,
            'version'    => $last + 1,
            'created_by' => Auth::id(),
            'title'      => $article->getOriginal('title'),
            'excerpt'    => $article->getOriginal('excerpt'),
            'content'    => $article->getOriginal('content'),
            'status'     => $article->getOriginal('status'),
        ]);
    }
}
