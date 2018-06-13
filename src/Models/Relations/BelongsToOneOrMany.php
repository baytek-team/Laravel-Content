<?php

namespace Baytek\Laravel\Content\Models\Relations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BelongsToOneOrMany extends BelongsToMany
{
    /**
     * Whether this relation should return a collection.
     *
     * @var boolean
     */
    protected $isMany = true;

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        $results = $this->get();

        return $this->isMany? $results : $results->first();
    }

    /**
     * Declare that a single result should be returned.
     *
     * @return $this
     */
    public function expectOne()
    {
        $this->isMany = false;

        return $this;
    }

    /**
     * Declare that a collection of results should be returned.
     *
     * @return $this
     */
    public function expectMany()
    {
        $this->isMany = true;

        return $this;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        $dictionary = $this->buildDictionary($results);

        // Once we have an array dictionary of child objects we can easily match the
        // children back to their parent using the dictionary and the keys on the
        // the parent models. Then we will return the hydrated models back out.
        foreach ($models as $model) {
            if (isset($dictionary[$key = $model->{$this->parentKey}])) {
                $model->setRelation(
                    $relation,
                    $this->isMany ? $this->related->newCollection($dictionary[$key]) : $this->related->newCollection($dictionary[$key])->first()
                );
            }
        }

        return $models;
    }
}
