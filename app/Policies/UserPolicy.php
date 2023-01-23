<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function isAdmin(User $user)
    {
        return $user->is_admin;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isOrganizationUser(User $user)
    {
        return $user->organization_iz;
    }

    /**
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function update(User $user, User $model)
    {
        return $user->is_admin;
    }

    /**
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function delete(User $user, User $model)
    {
        return $user->is_admin;
    }
}
