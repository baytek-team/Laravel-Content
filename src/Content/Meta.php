<?php

namespace Baytek\Laravel\Content;

use Baytek\LaravelStatusBit\Statusable;
use Baytek\LaravelStatusBit\Interfaces\StatusInterface;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Meta extends Model implements StatusInterface
{
	use Traits\HasCompositePrimaryKey,
		Statusable;
	/**
	 * primaryKey
	 *
	 * @var integer
	 * @access protected
	 */
	protected $primaryKey = ['content_id', 'key'];

	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = false;

	/**
     * Set the user meta table
     * @var string
     */
	protected $table = 'content_meta';

	/**
     * Set the fillable fields
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
	 * @var boolean
	 */
	public $timestamps = false;

	/**
	 * Model boot method
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
     * @return BelongsTo Content model
     */
    public function content()
    {
    	return $this->belongsTo(\Baytek\Laravel\Content::class);
    }
}
