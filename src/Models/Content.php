<?php

namespace Baytek\LaravelContent\Models;

use Baytek\LaravelContent\ContentMeta;
use Baytek\LaravelContent\ContentRelations;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
	protected $fillable = [
		'status',
		'language',
		'title',
		'content',
	];

    public function meta()
    {
    	return $this->hasMany(ContentMeta::class);
    }

    public function relations()
    {
    	return $this->hasMany(ContentRelations::class);
    }
}
