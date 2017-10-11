<?php

namespace Baytek\Laravel\Content;

use Baytek\Laravel\Content;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    /**
     * primaryKey
     *
     * @var integer
     * @access protected
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    // public $incrementing = false;

	public $timestamps = false;
    protected $table = 'content_history';
	// protected $fillable = [
	// 	'content_id',
	// 	'relation_id',
	// 	'relation_type_id',
	// ];

    public function content()
    {
    	return $this->belongsTo(Content::class);
    }
}
