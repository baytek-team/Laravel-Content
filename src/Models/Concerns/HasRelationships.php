<?php

namespace Baytek\Laravel\Content\Models\Concerns;

use Baytek\Laravel\Content\Models\Relations\HasMany;
use Illuminate\Support\Str;

trait HasRelationships
{
    /**
     * [association description]
     *
     * @param  [type] $modelClass     Class to invoke when creating a builder instance
     * @param  [type] $relationKey    [description]
     * @param  [type] $constraints    [description]
     * @return Baytek\Laravel\Content\Models\Relations\HasMany              [description]
     */
    public function hasManyContent($modelClass, $constraints = [])
    {
        // If no relation name was given, we will use this debug backtrace to extract
        // the calling method's name and use that as the relationship name as most
        // of the time this will be what we desire to use for the relationships.
        // if (is_null($relation)) {
        //     $relation = $this->guessBelongsToRelation();
        // }

        // Create the instance of model we want to join from, this will apply all global
        // scopes to the subsequent queries.
        $instance = $this->newRelatedInstance($modelClass);

        // Once we have the foreign key names, we'll just create a new Eloquent query
        // for the related models and returns the relationship instance which will
        // actually be responsible for retrieving and hydrating every relations.
        $ownerKey = $instance->getKeyName();

        return new HasMany(
            $instance->newQuery(),
            $this,
            $ownerKey,
            $constraints
        );
    }
}
