<?php

namespace Baytek\Laravel\Content\Eloquent;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
/**
 * @mixin \Illuminate\Database\Query\Builder
 */
class Builder extends EloquentBuilder
{
    /**
     * Get the hydrated models without eager loading.
     * Build that model if its content type is set.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model[]
     */
    public function getModels($columns = ['*'])
    {
        $records = $this->query->get($columns);

        return $records->map(function($record) {
            // Check if the content type is set, if so build that type.
            if(isset($record->content_type) && $record->content_type) {
                return (new $record->content_type)->newFromBuilder($record);
            }
            return $this->model->newFromBuilder($record);
        })->all();
    }

    /**
     * Determine which where to use with key value pair parameters
     *
     * @param  string $column
     * @param  mixed $value
     * @return Baytek\Laravel\Content\Eloquent\Builder
     */
    public function determineWhere($column, $value)
    {
        if(is_array($value)) {
            $this->query->whereIn($column, $value);
        }
        else {
            $this->query->where($column, $value);
        }

        return $this;
    }
}