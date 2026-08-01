<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContractPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Contract $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'employee';
    }

    public function update(User $user, Contract $model): bool
    {
        return $user->role === 'employee';
    }

    public function delete(User $user, Contract $model): bool
    {
        return $user->role === 'employee';
    }
}
