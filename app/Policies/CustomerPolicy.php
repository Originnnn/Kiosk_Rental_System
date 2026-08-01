<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Customer $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'employee';
    }

    public function update(User $user, Customer $model): bool
    {
        return $user->role === 'employee';
    }

    public function delete(User $user, Customer $model): bool
    {
        return $user->role === 'employee';
    }
}
