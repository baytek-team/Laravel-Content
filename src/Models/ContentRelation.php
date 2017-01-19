<?php

namespace Baytek\LaravelContent;

use Baytek\LaravelContent\Content;
use Illuminate\Database\Eloquent\Model;

class ContentRelation extends Model
{
	protected $fillable = [
		'content_id',
		'relation_id',
		'relation_type_id',
	];

    public function content()
    {
    	return $this->belongsTo(Content::class);
    }
}
