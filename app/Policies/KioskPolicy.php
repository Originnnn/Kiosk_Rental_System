<?php

namespace App\Policies;

use App\Models\Kiosk;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class KioskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Kiosk $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'manager';
    }

    public function update(User $user, Kiosk $model): bool
    {
        return $user->role === 'manager';
    }

    public function delete(User $user, Kiosk $model): bool
    {
        return $user->role === 'manager';
    }
}
