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

    /**
     * Indicates if we are to use the time stamps
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Assign the table for the model
     *
     * @var string
     */
    protected $table = 'content_relations';

    /**
     * List of fields that are fillable
     *
     * @var array
     */
    protected $fillable = [
        'content_id',
        'relation_id',
        'relation_type_id',
    ];

    /**
     * Content relation back to content
     *
     * @return void
     */
    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * Get the relation of a content
     *
     * @return void
     */
    public function relation()
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * Get the relation type of a content
     *
     * @return void
     */
    public function relationType()
    {
        return $this->belongsTo(Content::class);
    }
}
