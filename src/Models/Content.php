<?php

namespace Baytek\LaravelContent\Models;

use Baytek\LaravelContent\ContentMeta;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    public function meta()
    {
    	return $this->hasMany(ContentMeta::class);
    }
}
