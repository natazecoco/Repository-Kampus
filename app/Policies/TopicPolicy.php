<?php

namespace App\Policies;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TopicPolicy
{
    use HandlesAuthorization;

    public function before(?User $user, $ability)
    {
        if (! $user) {
            return false;
        }

        // Super-admin pattern: if role is 'admin' allow everything
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'editor' || $user->role === 'student';
    }

    public function view(User $user, Topic $topic): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'editor'], true);
    }

    public function update(User $user, Topic $topic): bool
    {
        return in_array($user->role, ['admin', 'editor'], true);
    }

    public function delete(User $user, Topic $topic): bool
    {
        return $user->role === 'admin';
    }

    // Custom abilities
    public function merge(User $user, Topic $topic): bool
    {
        return $user->role === 'admin';
    }

    public function toggleActive(User $user, Topic $topic): bool
    {
        return $user->role === 'admin';
    }

    public function undoMerge(User $user, Topic $topic): bool
    {
        return $user->role === 'admin';
    }
}
