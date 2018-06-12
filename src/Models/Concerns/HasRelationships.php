<?php

namespace Baytek\Laravel\Content\Models\Concerns;

use Baytek\Laravel\Content\Models\Relations\HasMany;
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
}
