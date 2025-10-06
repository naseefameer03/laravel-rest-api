<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Article $article): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create articles');
    }

    /**
     * A custom policy method for publishing.
     * This is where the fine-grained permission shines.
     */
    public function publish(User $user, Article $article): bool
    {
        // Rule: Only users with the 'publish articles' permission can publish.
        return $user->can('publish articles');
    }

    /**
     * Determine whether the user can update the model.
     * For example, an author should only be able to update their own articles
     */
    public function update(User $user, Article $article): bool
    {
        if ($user->can('edit articles')) {
            // An author can only edit their own articles
            if ($user->hasRole('author')) {
                return $user->id === $article->user_id;
            }

            // An editor or admin can edit any article
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Article $article): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Article $article): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Article $article): bool
    {
        return false;
    }
}
