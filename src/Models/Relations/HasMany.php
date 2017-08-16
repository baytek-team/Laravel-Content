<?php

namespace Baytek\Laravel\Content\Models\Relations;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class HasMany extends Relation
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
     * @note This should be a variable set in the config that can change based on the setup
     * @var string
     */
    protected $foreignKey = 'contents.id';

    /**
     * The associated key of the relation.
     *
     * @note This should be a variable set in the config that can change based on the setup
     * @var string
     */
    protected $relatedKey = 'content_id';

    /**
     * The count of self joins.
     *
     * @var int
     */
    protected static $selfJoinCount = 0;

    /**
     * Create a new has one or many relationship instance.
     *
     * @note need to add detection for singular and plural to return collections or not
     * @note need to add metadata clauses
     * @note need to add specification if getting children or just relation
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $localKey
     * @param  string  $relationKey
     * @return void
     */
    public function __construct(Builder $query, Model $parent, $localKey, $relationKey, $constraints)
    {
        $this->localKey = $localKey;
        $this->relationKey = $relationKey;

        parent::__construct($query, $parent);
    }

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
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        $this->query->whereIn(
            $this->foreignKey, $this->getKeys($models, $this->localKey)
        );
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

        $query->getModel()->setAlias('r', true);
        $table = $query->getModel()->getTable();

        // We need to join to the intermediate table on the related model's primary
        // key column with the intermediate table's foreign key for the related
        // model instance. Then we can set the "where" for the parent models.
        $baseTable = $this->related->getTable();

        $key = $baseTable.'.'.$this->related->getKeyName();

        $query->join('content_relations AS '.$childrenHash = $this->getRelationCountHash(), function ($join) use ($childrenHash) {
            $join->on('contents.id', '=', $childrenHash.'.relation_id')
                ->where($childrenHash.'.relation_type_id', content_id('parent-id'));
        })
        ->join('contents AS ' . $table, $table.'.id', '=', $childrenHash.'.content_id');

        if($this->relationKey) {
            $query->join('content_relations AS '.$typeHash = $this->getRelationCountHash(), function ($join) use ($childrenHash, $typeHash) {
                $join->on($typeHash.'.content_id', '=', $childrenHash.'.content_id')
                    ->where($typeHash.'.relation_type_id', content_id('content-type'));
                    if(is_array($this->relationKey)) {
                        $join->whereIn($typeHash.'.relation_id', content_ids($this->relationKey));
                    }
                    else {
                        $join->where($typeHash.'.relation_id', content_id($this->relationKey));
                    }
            });
        }

        return $this;
    }

    /**
     * Get the fully qualified "related key" for the relation.
     *
     * @return string
     */
    public function getQualifiedRelatedKeyName()
    {
        return $this->table.$this->relatedKey;
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
        dd('Something calls me, I need to know where and why');
        return $this->matchMany($models, $results, $relation);
    }

    /**
     * Get the foreign key for the relationship.
     *
     * @return string
     */
    public function getQualifiedForeignKeyName()
    {
        return $this->foreignKey;
    }

    /**
     * Get a relationship join table hash.
     *
     * @return string
     */
    public function getRelationCountHash()
    {
        return 'laravel_reserved_'.static::$selfJoinCount++;
    }
}
