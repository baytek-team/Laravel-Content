<?php

namespace Baytek\Laravel\Content\Models\Relations;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Baytek\Laravel\Content\Models\Concerns\HasMatches;

class HasMany extends Relation
{
    use HasMatches;

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

    protected $children = false;
    protected $hasSetAlias = false;
    protected $metadata;
    protected $order;
    protected $parent;
    protected $relation;

    /**
     * The count of self joins.
     *
     * @var int
     */
    protected static $selfJoinCount = 0;

    /**
     * Whether this relation should return a collection.
     *
     * @var boolean
     */
    protected $isMany = true;

    /**
     * Create a new has one or many relationship instance.
     *
     * @note need to add detection for singular and plural to return collections or not
     * @note need to add metadata clauses
     * @note need to add specification if getting children or just relation
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $localKey
     * @param  string  $relation
     * @return void
     */
    public function __construct(Builder $query, Model $parent, $localKey, $constraints)
    {
        $this->localKey = $localKey;
        $this->parent = $parent;

        $this->relation = isset($constraints['relation']) ? $constraints['relation'] : null;
        $this->direction = isset($constraints['direction']) ? $constraints['direction'] : 'down';
        // $this->children = isset($constraints['children']) && $constraints['children'] == true;
        $this->order = isset($constraints['order-by']) ? $constraints['order-by'] : false;

        if (isset($constraints['metadata'])) {
            foreach ($constraints['metadata'] as $key => $metadata) {

                if (count($metadata) === 1) {
                    $this->metadata[] = [$key, '=', $metadata];
                } else {
                    $this->metadata[] = $metadata;
                }
            }

        }

        parent::__construct($query, $parent);
    }



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
            $this->foreignKey,
            $this->getKeys(
                $models,
                $this->localKey
            )
        );
    }



    public function addJoinCondition()
    {

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
        $table = $query->getModel()->getTable();

        // We need to join to the intermediate table on the related model's primary
        // key column with the intermediate table's foreign key for the related
        // model instance. Then we can set the "where" for the parent models.
        // $baseTable = $this->related->getTable();

        if (!$this->children && !$this->relation && !$this->metadata) {
            throw new \Exception('You do not have any conditions, this would simply return the item in stack');
        }

        // If children is set
        // if ($this->children) {
        //     $query->getModel()->setAlias('r', true);
        //     $table = $query->getModel()->getTable();

        //     $query->join('content_relations AS '.$childrenHash = $this->getRelationCountHash(), function ($join) use ($childrenHash) {
        //         $join->on('contents.id', '=', $childrenHash.'.relation_id')
        //             ->where($childrenHash.'.relation_type_id', content_id('parent-id'));
        //     })
        //     ->join('contents AS '.$table, $table.'.id', '=', $childrenHash.'.content_id');
        // }


        if ($this->relation) {
            $this->doRelationJoins($query, $this->relation);
        }

        $this->doMetadataJoins($query);

        return $this;
    }


    /**
     * Add the relation joins to the complex relations
     *
     * @return string
     */
    public function doRelationJoins($query, $conditions)
    {
        $type = false;
        $value = false;

        // If the relation is a string
        if (is_string($relationValue = $conditions)) {

            // we will assume that we are looking for a content-type relation
            $type = 'content-type';

        // If the relation is an array
        } elseif (is_array($conditions)) {

            if (count($conditions) > 1) {
                throw new \Exception('You have passed in too many items for the relation constraint.');
            }

            // Set the relation value that we want to match on
            $value = array_values($conditions)[0];

            // Check to see if the array key is numeric, if it is that means the key was not defined by the programmer
            // I am also doing a bit of trickery here, if the relation type is not a number it will be set to $type
            if (is_numeric($type = array_keys($conditions)[0])) {
                $type = 'content-type';
            }


        // If the relation is equal to true
        } elseif ($conditions === true) {

        }

        $query->join('content_relations AS '.$relationTypeJoinHash = $this->getRelationCountHash(), function ($join) use ($type, $value, $query, $relationTypeJoinHash) {

            if ($this->order && ! $this->hasSetAlias) {
                $query->orderBy($relationTypeJoinHash.'.'.$this->order);
                $query->addSelect($relationTypeJoinHash.'.'.$this->order.'');
            }

            $column = ($this->direction == 'down') ? 'content_id': 'relation_id';
            $join->on($relationTypeJoinHash.'.'.$column, '=', $query->getModel()->getTable().'.id');

            if ($type) {
                $join->where($relationTypeJoinHash.'.relation_type_id', content_id($type));
            }

            if (!is_array($value)) {
                if (is_string($value)) {
                    $join->where($relationTypeJoinHash.'.relation_id', content_id($value));
                } elseif ($value) {
                    $join->where($relationTypeJoinHash.'.relation_id', $value);
                }
            }
        })
        ->join('contents AS '.$depthTable = $this->getContentCountHash(), function ($join) use ($query, $type, $value, $depthTable, $relationTypeJoinHash) {

            if (is_array($value) || ! $this->hasSetAlias) {
                $this->hasSetAlias = true;
                $query->getModel()->setAlias($depthTable, true);
            }

            $column = ($this->direction == 'down') ? 'relation_id': 'content_id';
            $join->on($depthTable.'.id', '=', $relationTypeJoinHash.'.'.$column);

            if ($type) {
                $join->where($relationTypeJoinHash.'.relation_type_id', content_id($type));
            }
        });

        if (is_array($value)) {
            $this->doRelationJoins($query, $value);
        }
    }

    /**
     * Add the metadata joins to the complex relations
     *
     * @return string
     */
    public function doMetadataJoins($query)
    {
        if ($this->metadata) {
            foreach ($this->metadata as $metadata) {
                $query->join('content_meta AS metadata', function ($join) use ($metadata, $table) {
                    $join->on($table.'.id', '=', 'metadata.content_id')
                        ->where('metadata.key', $metadata[0])
                        ->where('metadata.value', $metadata[1], $metadata[2]);
                });
            }
        }

        return $query;
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
            $this->getQualifiedForeignKeyName(),
            '=',
            $this->parent->getKey()
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
        return 'content_relation_join_'.static::$selfJoinCount++;
    }


    /**
     * Get a relationship join table hash.
     *
     * @return string
     */
    public function getContentCountHash()
    {
        return 'content_join_'.static::$selfJoinCount++;
    }
}
