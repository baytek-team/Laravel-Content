<?php

namespace Baytek\Laravel\Content\Models;

use Baytek\Laravel\Content\Models\Content;
use Illuminate\Database\Eloquent\Model;

class ContentMeta extends Model
{
	protected $table = 'content_meta';
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
