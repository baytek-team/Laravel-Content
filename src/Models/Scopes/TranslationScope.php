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
        return $builder
            ->select('contents.id', 'contents.key', 'contents.status', 'contents.revision', 'language.language', 'language.title', 'language.content')
            ->join('content_relations AS languages', 'contents.id', '=', 'languages.content_id')
            ->join('contents AS language', function ($join) {
                $join->on('language.id', '=', 'languages.relation_id')
                     ->where('language.language', \App::getLocale());
            });
    }
}