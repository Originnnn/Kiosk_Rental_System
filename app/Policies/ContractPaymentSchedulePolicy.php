<?php

namespace App\Policies;

use App\Models\ContractPaymentSchedule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContractPaymentSchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ContractPaymentSchedule $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'employee';
    }

    public function update(User $user, ContractPaymentSchedule $model): bool
    {
        return $user->role === 'employee';
    }

    public function delete(User $user, ContractPaymentSchedule $model): bool
    {
        return $user->role === 'employee';
    }
}
