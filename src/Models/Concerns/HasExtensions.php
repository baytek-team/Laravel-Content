<?php

namespace Baytek\Laravel\Content\Models\Concerns;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

trait HasExtensions
{
    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $query->where("{$this->getTable()}.{$this->getKeyName()}", '=', $this->getKeyForSaveQuery());

        return $query;
    }

    /**
     * Get a new query builder that doesn't have any global scopes.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newQueryWithoutScopes()
    {
        if(!method_exists($this, 'extends'))
            return parent::newQueryWithoutScopes();

        list($class, $forgienKey, $localKey) = $this->extends();

        $builder = $this->newEloquentBuilder($this->newBaseQueryBuilder());

        $model = new $class;

        // Once we have the query builders, we will set the model instances so the
        // builder can easily access any information it may need from the model
        // while it is constructing and executing various queries against it.
        return $builder->setModel($this)
            ->select('*', "{$model->getTable()}.*", "{$this->getTable()}.*")
            ->leftJoin($model->getTable(), "{$this->getTable()}.$localKey", '=', "{$model->getTable()}.$forgienKey")
            ->with($this->with)
            ->withCount($this->withCount);
    }

    /**
     * Prepare statement values
     *
     * @param  Model  $model
     * @param  array  $attributes
     * @return array
     */
    protected function prepareInsert($model, $attributes)
    {
        $additional = [];

        if($model->usesTimestamps()) {
            $model->updateTimestamps();
            $additional = [
                static::CREATED_AT,
                static::UPDATED_AT
            ];
        }

        return array_only(
            array_merge($model->attributes, $attributes),
            array_merge($model->getFillable(), $additional)
        );
    }

    /**
     * Insert the given attributes and set the ID on the model.
     *
     * @param  array  $attributes
     * @return int
     */
    protected function insertAndSetIdParent($attributes)
    {
        list($class, $forgienKey, $localKey) = $this->extends();
        $query = $this->newEloquentBuilder($this->newBaseQueryBuilder())->setModel(new $class);

        $attributes = $this->prepareInsert(new $class, $attributes);
        $id = $query->insertGetId($attributes, $keyName = $this->getKeyName());

        return $id;
    }

    /**
     * Insert the given attributes and set the ID on the model.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array  $attributes
     * @return void
     */
    protected function insertAndSetId(Builder $query, $attributes)
    {
        list($class, $forgienKey, $localKey) = $this->extends();

        $attrs = $this->prepareInsert($this, $attributes);
        $attrs[$localKey] = $this->insertAndSetIdParent($attributes);

        $id = $query->insertGetId($attrs, $keyName = $this->getKeyName());

        $this->setAttribute($keyName, $id);
    }
}
