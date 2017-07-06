<?php

namespace Baytek\Laravel\Content\Models\Relations;

use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Collection;

class HasSome extends HasOneOrMany
{
    /**
     * The intermediate table for the relation.
     *
     * @var string
     */
    protected $table = 'content_relations';

    /**
     * The associated key of the relation.
     *
     * @var string
     */
    protected $relatedKey = 'content_id';

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->query->get();
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array   $models
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints()
    {
        $this->performJoin();

        if (static::$constraints) {
            $this->addWhereConstraints();
        }
    }

    /**
     * Set the join clause for the relation query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|null  $query
     * @return $this
     */
    protected function performJoin($query = null)
    {
        $query = $query ?: $this->query;

        // We need to join to the intermediate table on the related model's primary
        // key column with the intermediate table's foreign key for the related
        // model instance. Then we can set the "where" for the parent models.
        $baseTable = $this->related->getTable();

        $key = $baseTable.'.'.$this->related->getKeyName();

        $query->join($this->table, $key, '=', $this->getQualifiedRelatedKeyName());

        return $this;
    }

    /**
     * Get the fully qualified "related key" for the relation.
     *
     * @return string
     */
    public function getQualifiedRelatedKeyName()
    {
        return $this->table.'.'.$this->relatedKey;
    }

    /**
     * Set the where clause for the relation query.
     *
     * @return $this
     */
    protected function addWhereConstraints()
    {
        $this->query->where(
            $this->getQualifiedForeignKeyName(), '=', $this->parent->getKey()
        );

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
        return $this->matchMany($models, $results, $relation);
    }
}
