<?php

namespace Baytek\Laravel\Content\Models\Concerns;

use Illuminate\Support\Str;

trait HasExtensions
{
    /**
     * Get a new query builder that doesn't have any global scopes.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newQueryWithoutScopes()
    {
        $builder = $this->newEloquentBuilder($this->newBaseQueryBuilder());

        if(!method_exists($this, 'extends'))
            return $builder->setModel($this)
                        ->with($this->with)
                        ->withCount($this->withCount);

        $extends = $this->extends();
        $model = new $extends[0];

        // Once we have the query builders, we will set the model instances so the
        // builder can easily access any information it may need from the model
        // while it is constructing and executing various queries against it.
        return $builder->setModel($this)
            ->select('*', "{$model->getTable()}.*", "{$this->getTable()}.*")
            ->leftJoin($model->getTable(), "{$this->getTable()}.{$extends[2]}", '=', "{$model->getTable()}.{$extends[1]}")
            ->with($this->with)
            ->withCount($this->withCount);
    }
}
