<?php

namespace Baytek\Laravel\Content\Controllers;

use Baytek\Laravel\Content\Traits\Contentable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        // $this->content = new ContentController;

        // Register view namespace for this Route/Controller/ContentType
        // View::addNamespace($this->type, app_path()."/ContentTypes/$this->type/Views");
    }
}