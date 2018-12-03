<?php

namespace Baytek\Laravel\Content\Models;

use Illuminate\Database\Eloquent\Model;
// use Baytek\Laravel\Content\Traits\HasCompositePrimaryKey;

class ContentRelation extends Model
{
    // use HasCompositePrimaryKey;

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
    	return $this->belongsTo(Content::class)->withoutGlobalScopes();
    }

    public function relation()
    {
    	return $this->belongsTo(Content::class)->withoutGlobalScopes();
    }

    public function relationType()
    {
    	return $this->belongsTo(Content::class)->withoutGlobalScopes();
    }
}
