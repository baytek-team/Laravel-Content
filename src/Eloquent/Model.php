<?php

namespace Baytek\Laravel\Content\Eloquent;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Baytek\Laravel\Content\Eloquent\Builder;

/**
 * @mixin \Illuminate\Database\Query\Builder
 */
class Model extends EloquentModel
{
    /**
     * Create new Eloquent Builder instance
     *
     * @param  string $query The query
     * @return Baytek\Laravel\Content\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }
}
