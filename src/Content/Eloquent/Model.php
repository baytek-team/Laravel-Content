<?php

namespace Baytek\Laravel\Content\Eloquent;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Baytek\Laravel\Content\Eloquent\Builder;
/**
 * @mixin \Illuminate\Database\Query\Builder
 */
class Model extends EloquentModel
{

	public function newEloquentBuilder($query)
	{
	   return new Builder($query);
	}
}