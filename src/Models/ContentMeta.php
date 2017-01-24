<?php

namespace Baytek\LaravelContent\Models;

use Baytek\LaravelContent\Models\Content;
use Illuminate\Database\Eloquent\Model;

class ContentMeta extends Model
{
	protected $table = 'content_metas';
	protected $fillable = [
		'status',
		'key',
		'value',
	];

	public $timestamps = false;

    public function content()
    {
    	return $this->belongsTo(Content::class);
    }
}
