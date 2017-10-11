<?php

namespace Baytek\Laravel\Content;

use Baytek\Laravel\Content;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
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

	public $timestamps = false;
    protected $table = 'content_relations';
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