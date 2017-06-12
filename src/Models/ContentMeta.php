<?php

namespace Baytek\Laravel\Content\Models;

use Baytek\Laravel\Content\Models\Content;
use Illuminate\Database\Eloquent\Model;

class ContentMeta extends Model
{
	/**
	 * primaryKey
	 *
	 * @var integer
	 * @access protected
	 */
	protected $primaryKey = 'content_id';

	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = false;

	protected $table = 'content_meta';
	protected $fillable = [
		'content_id',
		'status',
		'key',
		'language',
		'value',
	];

	public $timestamps = false;

    public function content()
    {
    	return $this->belongsTo(Content::class);
    }
}
