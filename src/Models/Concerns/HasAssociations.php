<?php

namespace Baytek\Laravel\Content\Models\Concerns;

use Baytek\Laravel\Content\Models\Relations\HasMany;
use Illuminate\Support\Str;

trait HasAssociations
{

    /**
     * [association description]
     *
     * @param  [type] $related     [description]
     * @param  [type] $relationKey [description]
     * @param  [type] $relation    [description]
     * @return Baytek\Laravel\Content\Models\Relations\HasMany              [description]
     */
    public function association($related, $relationKey = null, $relation = null)
    {
        // If no relation name was given, we will use this debug backtrace to extract
        // the calling method's name and use that as the relationship name as most
        // of the time this will be what we desire to use for the relationships.
        if (is_null($relation)) {
            $relation = $this->guessBelongsToRelation();
        }

        $instance = $this->newRelatedInstance($related);

        // If no foreign key was supplied, we can use a backtrace to guess the proper
        // foreign key name by using the name of the relationship function, which
        // when combined with an "_id" should conventionally match the columns.
        // if (is_null($foreignKey)) {
        //     $foreignKey = Str::snake($relation).'_'.$instance->getKeyName();
        // }

        // $foreignKey = 'contents.id';

        // Once we have the foreign key names, we'll just create a new Eloquent query
        // for the related models and returns the relationship instance which will
        // actually be responsible for retrieving and hydrating every relations.
        $ownerKey = $instance->getKeyName();

        return new HasMany(
            $instance->newQuery(), $this, $ownerKey, $relationKey, $relation
        );
    }

    /**
     * Alias for association
     *
     * @return Baytek\Laravel\Content\Models\Concerns\HasAssociations [description]
     */
    public function kin()
    {
        return call_user_func_array([$this, 'association'], func_get_args());
    }
}
