<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleVersion;

class ArticleVersionController extends Controller
{
    public function index(Article $article)
    {
        return $article->versions()->orderByDesc('version')->paginate(20);
    }

    public function show(Article $article, ArticleVersion $version)
    {
        abort_unless($version->article_id === $article->id, 404);

        return $version;
    }

    public function revert(Article $article, ArticleVersion $version)
    {
        abort_unless($version->article_id === $article->id, 404);

        $article->update([
            'title' => $version->title,
            'excerpt' => $version->excerpt,
            'content' => $version->content,
            'status' => $version->status,
        ]);

        return $article->fresh();
    }
}
