<?php

namespace Baytek\Laravel\Content\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ContentTypeScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $prefix = $builder->getQuery()->grammar->getTablePrefix();
        $context = $builder->getModel()->getTable();

        if(!$builder->getModel()->isAliased()) {
            $builder->from($builder->getQuery()->from ." AS " . $builder->getModel()->getTable());
        }

        $builder
            ->select($context . '.*')
            ->addSelect(\DB::raw($prefix.'content_type.content as content_type'))
            ->leftJoin('content_relations AS content_type_relation', function ($join) use ($context) {
                $join->on($context.'.id', '=', 'content_type_relation.content_id')
                     ->where('content_type_relation.relation_type_id', 3);
            })
            ->leftJoin('contents AS content_type', function ($join) {
                $join->on('content_type.id', '=', 'content_type_relation.relation_id');
            });

        return $builder;
    }
}