<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    use AuthorizesRequests;

    // GET /articles
    public function index()
    {
        $articles = Article::with('user')->paginate(10);

        return response()->json($articles, 200);
    }

    // POST /articles
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $article = Article::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Article created successfully', 'data' => $article], 201);
    }

    // GET /articles/{id}
    public function show($id)
    {
        $article = Article::with('user')->findOrFail($id);

        return response()->json($article, 200);
    }

    // PUT /articles/{id}
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        $article = Article::findOrFail($id);
        $article->update($validated);

        return response()->json(['message' => 'Article updated successfully', 'data' => $article], 200);
    }

    // DELETE /articles/{id}
    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return response()->json(['message' => 'Article deleted successfully'], 200);
    }

    public function publish(Article $article)
    {
        $this->authorize('publish', $article);

        $article->published_at = now();
        $article->save();

        return response()->json(['message' => 'Article published successfully.'], 200);
    }
}
