<?php

namespace Baytek\Laravel\Content\Models;

use Baytek\Laravel\StatusBit\Statusable;
use Baytek\Laravel\StatusBit\Interfaces\StatusInterface;
use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Traits\HasCompositePrimaryKey;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContentMeta extends Model implements StatusInterface
{
    use HasCompositePrimaryKey,
        Statusable;

    /**
     * primaryKey
     *
     * @var integer
     * @access protected
     */
    protected $primaryKey = ['content_id', 'key', 'language'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Set the user meta table
     *
     * @var string
     * @access protected
     */
    protected $table = 'content_meta';

    /**
     * Set the fillable fields
     *
     * @var array
     * @access protected
     */
    protected $fillable = [
        'content_id',
        'status',
        'key',
        'language',
        'value',
    ];

    /**
     * Do not use timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Model boot method
     *
     * @return void
     * @access protected
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('not_restricted', function (Builder $builder) {
            $builder->withStatus(['exclude' => [self::RESTRICTED]]);
        });
    }

    /**
     * Content model relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}
