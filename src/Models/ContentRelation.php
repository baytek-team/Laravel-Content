<?php

namespace Baytek\LaravelContent\Models;

// use Baytek\LaravelContent\Models\Content;
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

    public function relation()
    {
    	return $this->belongsTo(Content::class);
    }

    public function relationType()
    {
    	return $this->belongsTo(Content::class);
    }
}
