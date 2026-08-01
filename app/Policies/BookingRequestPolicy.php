<?php

namespace App\Policies;

use App\Models\BookingRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BookingRequest $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'employee';
    }

    public function update(User $user, BookingRequest $model): bool
    {
        return $user->role === 'employee';
    }

    public function delete(User $user, BookingRequest $model): bool
    {
        return $user->role === 'employee';
    }
}
