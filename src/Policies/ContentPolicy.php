<?php

namespace Baytek\Laravel\Content\Policies;

use App\User;
use Baytek\Laravel\Content\Models\Content;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the content.
     *
     * @param  \App\User  $user
     * @param  Baytek\Laravel\Content\Content  $content
     * @return mixed
     */
    public function view(User $user, Content $content)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can create contents.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can update the content.
     *
     * @param  \App\User  $user
     * @param  Baytek\Laravel\Content\Content  $content
     * @return mixed
     */
    public function update(User $user, Content $content)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can delete the content.
     *
     * @param  \App\User  $user
     * @param  Baytek\Laravel\Content\Content  $content
     * @return mixed
     */
    public function delete(User $user, Content $content)
    {
        //
        return true;
    }
}
