<?php

namespace Baytek\LaravelContent\Controllers;

use Baytek\LaravelContent\Traits\Contentable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ContentController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Contentable;
}