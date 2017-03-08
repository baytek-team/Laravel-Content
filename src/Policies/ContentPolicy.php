<?php

namespace Baytek\Laravel\Content\Policies;

use Baytek\Laravel\Users\User;
use Baytek\Laravel\Content\Models\Content;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContentPolicy extends GeneralPolicy
{
    public $contentType = 'Content';
}
