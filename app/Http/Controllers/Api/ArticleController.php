<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $articles = Article::query()
            ->when($request->boolean('published_only', true), fn ($qq) => $qq->published())
            ->when($status = $request->get('status'), fn ($qq) => $qq->where('status', $status))
            ->when($request->filled('category_ids'), fn ($qq) => $qq->whereHas('categories', function ($q2) use ($request) {
                $q2->whereIn('categories.id', (array) $request->category_ids);
            }))
            ->when($request->filled('tags'), fn ($qq) => $qq->withAnyTags((array) $request->tags))
            ->with(['author:id,name', 'categories:id,name,slug', 'media'])
            ->orderByDesc('published_at')->paginate(15);

        return response()->json(['message' => 'Article Listed successfully', 'data' => $articles], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'status' => 'in:draft,published,archived',
            'published_at' => 'nullable|date',
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:categories,id',
            'tags' => 'array',
            'tags.*' => 'string',
            'images.*' => 'file|image|max:5120', // 5MB each
            'cover' => 'file|image|max:5120',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $article = Article::create([
                'user_id' => $request->user()->id,
                'title' => $data['title'],
                'slug' => $data['slug'] ?? null,
                'excerpt' => $data['excerpt'] ?? null,
                'content' => $data['content'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'published_at' => $data['status'] === 'published'
                    ? ($data['published_at'] ?? now())
                    : null,
                'meta' => [],
            ]);

            if (! empty($data['category_ids'])) {
                $article->categories()->sync($data['category_ids']);
            }

            if (! empty($data['tags'])) {
                $article->syncTags($data['tags']);
            }

            if ($request->hasFile('cover')) {
                $article->addMediaFromRequest('cover')->toMediaCollection('cover');
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $article->addMedia($file)->toMediaCollection('images');
                }
            }

            return response()->json(['message' => 'Articles created successfully', 'data' => $article->refresh()->load(['categories', 'tags', 'media'])], 201);
        });
    }

    public function show(Article $article)
    {
        $article->load(['author:id,name', 'categories:id,name,slug', 'media', 'tags']);

        return response()->json($article, 200);
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:articles,slug,'.$article->id,
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'status' => 'in:draft,published,archived',
            'published_at' => 'nullable|date',
            'category_ids' => 'array',
            'category_ids.*' => 'integer|exists:categories,id',
            'tags' => 'array',
            'tags.*' => 'string',
        ]);

        $article->fill($data);

        if (($data['status'] ?? null) === 'published' && ! $article->published_at) {
            $article->published_at = now();
        }

        $article->save();

        if (array_key_exists('category_ids', $data)) {
            $article->categories()->sync($data['category_ids'] ?? []);
        }
        if (array_key_exists('tags', $data)) {
            $article->syncTags($data['tags'] ?? []);
        }

        return response()->json(['message' => 'Article updated successfully', 'data' => $article->load(['categories', 'tags', 'media'])], 200);
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return response()->noContent();
    }

    public function publish(Article $article)
    {
        $this->authorize('publish', $article);

        $article->update([
            'status' => 'published',
            'published_at' => $article->published_at ?? now(),
        ]);

        return response()->json(['message' => 'Article published successfully.', 'data' => $article->fresh()], 200);

    }

    public function uploadImages(Request $request, Article $article)
    {
        $request->validate([
            'images.*' => 'required|image|max:5120',
        ]);

        foreach ($request->file('images', []) as $file) {
            $article->addMedia($file)->toMediaCollection('images');
        }

        return $article->load('media');
    }

    public function replaceCover(Request $request, Article $article)
    {
        $request->validate([
            'cover' => 'required|image|max:5120',
        ]);

        $article->clearMediaCollection('cover');
        $article->addMediaFromRequest('cover')->toMediaCollection('cover');

        return $article->load('media');
    }
}
