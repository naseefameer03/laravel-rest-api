<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, Article $article)
    {
        $depth = (int) $request->get('depth', 3); // max levels to load
        $query = $article->comments()->with(['author:id,name']);

        // Eager-load nested replies up to N levels
        $with = [];
        $relation = 'childrenRecursive';
        for ($i = 0; $i < $depth; $i++) {
            $with[] = str_repeat('childrenRecursive.', $i).'author:id,name';
        }

        return $query->with($with)->orderBy('created_at')->paginate(20);
    }

    public function store(Request $request, Article $article)
    {
        $data = $request->validate([
            'body' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        if (! empty($data['parent_id'])) {
            $parent = Comment::find($data['parent_id']);
            abort_if($parent->article_id !== $article->id, 422, 'Parent comment not in this article.');
        }

        $comment = $article->allComments()->create([
            'user_id' => $request->user()?->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => $data['body'],
            'status' => 'approved', // or 'pending' then moderate
        ]);

        return response()->json($comment->load('author'), 201);
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $data = $request->validate(['body' => 'required|string']);
        $comment->update($data);

        return $comment->fresh('author');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return response()->noContent();
    }
}
