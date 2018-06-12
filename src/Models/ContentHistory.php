<?php

namespace Baytek\Laravel\Content\Models;

use Illuminate\Database\Eloquent\Model;

class ContentHistory extends Model
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

    /**
     * Indicates if timestamps are being used
     *
     * @var bool
     */
	public $timestamps = false;

    /**
     * Indicates the table to be used
     *
     * @var string
     */
    protected $table = 'content_history';

    /**
     * Content history related to content class
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content()
    {
    	return $this->belongsTo(Content::class);
    }
}
