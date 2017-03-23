<?php

namespace Baytek\Laravel\Content\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TranslationScope implements Scope
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
        $context = isset($builder->selectContext) ? $builder->selectContext : 'contents';

        $query = $builder
            ->select(
                \DB::raw("
                    $prefix$context.id,
                    $prefix$context.key,
                    $prefix$context.status,
                    $prefix$context.revision,
                    IFNULL(${prefix}language.language, $prefix$context.language) as language,
                    IFNULL(${prefix}language.title, $prefix$context.title) as title,
                    IFNULL(${prefix}language.content, $prefix$context.content) as content
                ")
            )
            // ->select($context . '.id', $context . '.key', $context . '.status', $context . '.revision', 'language.language', 'language.title', 'language.content')
            // ->leftJoin('content_relations AS languages', 'contents.id', '=', 'languages.content_id')
            ->leftJoin('content_relations AS languages', function ($join) use ($context) {
                $join->on($context . '.id', '=', 'languages.content_id')
                     ->where('languages.relation_type_id', 5);
            })
            ->leftJoin('contents AS language', function ($join) {
                $join->on('language.id', '=', 'languages.relation_id')
                     ->where('language.language', \App::getLocale());
            });

        return $query;
    }
}