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

        list($class, $foreignKey, $localKey) = $this->extends();

        $builder = $this->newEloquentBuilder($this->newBaseQueryBuilder());

        // Once we have the query builders, we will set the model instances so the
        // builder can easily access any information it may need from the model
        // while it is constructing and executing various queries against it.
        // $builder->setModel($model = new $class);
        // $builder->getQuery()->from($this->getTable());
        $model = new $class;

        return $builder->setModel($this)
            ->select('*', "{$model->getTable()}.*", "{$this->getTable()}.*")
            ->leftJoin($model->getTable(), "{$this->getTable()}.$localKey", '=', "{$model->getTable()}.$foreignKey")
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

        $metadata = method_exists($model, 'getMetadataKeys') ? $model->getMetadataKeys(): [];

        return array_only(
            array_merge($model->getAttributes(), $attributes),
            array_except(array_merge($model->getFillable(), $additional), $metadata)
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
        list($class, $foreignKey, $localKey) = $this->extends();

        $model = new $class($attributes);

        if ($model->fireModelEvent('creating') === false) {
            return false;
        }

        $query = $model
            ->newEloquentBuilder($model->newBaseQueryBuilder())
            ->setModel($model);

        $attributes = $this->prepareInsert($model, $attributes);

        $id = $query->insertGetId($attributes, $keyName = $model->getKeyName());

        $model->exists = true;

        $model->wasRecentlyCreated = true;

        $model->setAttribute($keyName, $id);

        $model->fireModelEvent('created', false);

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
        list($class, $foreignKey, $localKey) = $this->extends();

        $attrs = $this->prepareInsert($this, $attributes);
        $attrs[$localKey] = $this->insertAndSetIdParent($attributes);

        if ($this->fireModelEvent('creating') === false) {
            return false;
        }

        $id = $query->insertGetId($attrs, $keyName = $this->getKeyName());

        $this->setAttribute($keyName, $id);
    }
}
