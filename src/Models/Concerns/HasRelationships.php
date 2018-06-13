<?php

namespace Baytek\Laravel\Content\Models\Concerns;

use Baytek\Laravel\Content\Models\Relations\HasMany;
use Baytek\Laravel\Content\Models\Relations\HasOne;
use Baytek\Laravel\Content\Models\Relations\BelongsToOneOrMany;
use Illuminate\Support\Str;

trait HasRelationships
{
    /**
     * Has many content relates models to the content model
     *
     * @param  Model $class           Class to invoke when creating a builder instance
     * @param  array $constraints     Model join constraints
     * @return Baytek\Laravel\Content\Models\Relations\HasMany
     */
    public function hasManyContent($class, $constraints = [])
    {
        // Create the instance of model we want to join from, this will apply all global
        // scopes to the subsequent queries.
        $instance = $this->newRelatedInstance($class);

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

    /**
     * [association description]
     *
     * @param  [type] $modelClass     Class to invoke when creating a builder instance
     * @param  [type] $relationKey    [description]
     * @param  [type] $constraints    [description]
     * @return Baytek\Laravel\Content\Models\Relations\HasOne              [description]
     */
    public function hasOneContent($modelClass, $constraints = [])
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

        return new HasOne(
            $instance->newQuery(),
            $this,
            $ownerKey,
            $constraints
        );
    }

    /**
     * Create a new belongs to many relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string  $relationName
     * @return void
     */
    public function belongsToOneOrMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null, $relation = null)
    {
        // If no relationship name was passed, we will pull backtraces to get the
        // name of the calling function. We will use that function name as the
        // title of this relation since that is a great convention to apply.
        if (is_null($relation)) {
            $relation = $this->guessBelongsToManyRelation();
        }

        // First, we'll need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we'll make the query
        // instances as well as the relationship instances we need for this.
        $instance = $this->newRelatedInstance($related);

        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();

        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();

        // If no table name was provided, we can guess it by concatenating the two
        // models using underscores in alphabetical order. The two model names
        // are transformed to snake case from their default CamelCase also.
        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        return new BelongsToOneOrMany($instance->newQuery(), $this, $table, $foreignPivotKey, $relatedPivotKey, $parentKey ?: $this->getKeyName(), $relatedKey ?: $instance->getKeyName(), $relation);
    }
}
