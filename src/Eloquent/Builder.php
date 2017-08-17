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
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model[]
     */
    public function getModels($columns = ['*'])
    {
        $records = $this->query->get($columns);

        return $records->map(function($record) {
            if(isset($record->content_type) && $record->content_type) {
                return (new $record->content_type)->newFromBuilder($record);
            }
            return $this->model->newFromBuilder($record);
        })->all();
    }
}