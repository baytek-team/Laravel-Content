<?php

namespace Baytek\Laravel\Content\Policies;

use Baytek\Laravel\Users\User;
use Baytek\Laravel\Content\Models\Content;
use Illuminate\Auth\Access\HandlesAuthorization;

class GeneralPolicy
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
        return $user->hasRole('Root') ?: null;
    }

    /**
     * Determine whether the user can view the content.
     *
     * @param  Baytek\Laravel\Users\User  $user
     * @param  mixed  $content
     * @return mixed
     */
    public function view(User $user, $content = null)
    {
        return $user->can('View '.title_case($this->contentType));
        // || (!is_null($content) && $content->metadata->author_id == $user->id);
    }

    /**
     * Determine whether the user can create contents.
     *
     * @param  Baytek\Laravel\Users\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('Create '.title_case($this->contentType));
    }

    /**
     * Determine whether the user can update the content.
     *
     * @param  Baytek\Laravel\Users\User  $user
     * @param  mixed  $content
     * @return mixed
     */
    public function update(User $user, $content)
    {
        return $user->can('Update '.title_case($this->contentType));
    }

    /**
     * Determine whether the user can delete the content.
     *
     * @param  Baytek\Laravel\Users\User  $user
     * @param  mixed  $content
     * @return mixed
     */
    public function delete(User $user, $content)
    {
        return $user->can('Delete '.title_case($this->contentType));
    }
}
