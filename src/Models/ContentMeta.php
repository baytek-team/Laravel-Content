<?php

namespace Baytek\LaravelContent;

use Baytek\LaravelContent\Content;
use Illuminate\Database\Eloquent\Model;

class ContentMeta extends Model
{
	protected $fillable = [
		'status',
		'key',
		'value',
	];

    public function content()
    {
    	return $this->belongsTo(Content::class);
    }
}
