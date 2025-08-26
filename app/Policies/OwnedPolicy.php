<?php

namespace App\Policies;

use App\Models\User;

class OwnedPolicy
{
    // Admin može sve
    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function viewAny(User $user): bool { return true; }
    public function create(User $user): bool  { return true; }

    // $record je bilo koji model koji ima user_id
    public function view(User $user, $record): bool
    {
        return (int) $record->user_id === (int) $user->id;
    }

    public function update(User $user, $record): bool
    {
        return (int) $record->user_id === (int) $user->id;
    }

    public function delete(User $user, $record): bool
    {
        return (int) $record->user_id === (int) $user->id;
    }

    public function restore(User $user, $record): bool
    {
        return (int) $record->user_id === (int) $user->id;
    }

    public function forceDelete(User $user, $record): bool
    {
        return (int) $record->user_id === (int) $user->id;
    }
}
