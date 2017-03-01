<?php

namespace Baytek\Laravel\Content\Policies;

use Baytek\Laravel\Users\User;
use Baytek\Laravel\Content\Models\Content;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user is admin or root.
     * If so they can do all the things.
     *
     * @param  Baytek\Laravel\Users\User  $user
     * @return mixed
     */
    public function before(User $user)
    {
        return $user->can('Manage Content');
    }

    /**
     * Determine whether the user can view the user.
     *
     * @param  Baytek\Laravel\Users\User  $user
     * @param  Baytek\Laravel\Users\User  $content
     * @return mixed
     */
    public function view(User $user, Content $content)
    {
        return $user->can('Manage Content');
    }

    /**
     * Determine whether the user can create contents.
     *
     * @param  Baytek\Laravel\Users\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
        return $user->can('Manage Content');
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  Baytek\Laravel\Users\User  $user
     * @param  Baytek\Laravel\Users\User  $content
     * @return mixed
     */
    public function update(User $user, Content $content)
    {
        return $user->can('Manage Content');
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  Baytek\Laravel\Users\User  $user
     * @param  Baytek\Laravel\Users\User  $content
     * @return mixed
     */
    public function delete(User $user, Content $content)
    {
        return $user->can('Manage Content');
    }
}
