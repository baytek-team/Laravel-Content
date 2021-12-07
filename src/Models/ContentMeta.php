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
	use Statusable;

	/**
     * Set the user meta table
     *
     * @var string
     */
	protected $table = 'content_meta';

	/**
     * Set the fillable fields
     *
     * @var array
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
