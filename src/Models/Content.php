<?php

namespace Baytek\Laravel\Content\Models;

use Baytek\Laravel\Content\Models\ContentMeta;
use Baytek\Laravel\Content\Models\ContentRelation;
use Illuminate\Database\Eloquent\Model;

use DB;
use Illuminate\Support\Str;

class Content extends Model
{
    // Defining the table we want to use for all content
    protected $table = 'contents';

    // Defining the fillable fields when saving records
	protected $fillable = [
		'status',
		'language',
        'key',
		'title',
		'content',
	];

    // Setting up default relationships which are none
    public $relationships = [];

    // Eager loading relationship lists
    public static $eager = [
        'meta',
        'relations',
        'relations.relation',
        'relations.relationType'
    ];

    // Default list of content types
    public $types = [
        'content',
        'content-type',
        'relation-type',
    ];

    // This method saves the content relation
    public function saveRelation($type, $relation_id)
    {
        (new ContentRelation([
            'content_id' => $this->id,
            'relation_id' => $relation_id,
            'relation_type_id' => $this->getContentByKey($type)->id,
        ]))->save();
    }

    public function getContentByKey($type)
    {
        return static::withoutGlobalScopes()->where('key', $type)->first();
    }

    public function getParents()
    {
        return $this->getParentsOf($this->id);
    }

    public function getParentsOf($id)
    {
        $prefix = env('DB_PREFIX');
        return DB::select("SELECT T2.id, T2.status, T2.revision, T2.language, T2.key, T2.title
            FROM (
                SELECT
                    @r AS _id,
                    (
                        SELECT @r := rel.relation_id
                        FROM ${prefix}contents
                        LEFT JOIN ${prefix}content_relations rel
                        ON rel.content_id = @r AND rel.relation_type_id = 3
                        WHERE ${prefix}contents.id = _id
                    ) AS parent_id,
                    @l := @l + 1 AS lvl
                FROM
                    (SELECT @r := ?, @l := 0) vars,
                    ${prefix}contents m
                WHERE @r <> 0) T1
            JOIN ${prefix}contents T2
            ON T1._id = T2.id
            ORDER BY T1.lvl DESC;
        ", [$id]);
    }

    public function scopeChildrenOf($query, $key, $column = 'key', $depth = 1)
    {
        return $query
            ->select('r.id', 'r.status', 'r.revision', 'r.language', 'r.title', 'r.key')
            ->leftJoin('content_relations AS relations', 'contents.id', '=', 'relations.relation_id')
            ->leftJoin('contents AS r', 'r.id', '=', 'relations.content_id')
            ->where('relations.relation_type_id', 4)
            ->where('contents.'.$column, $key);
    }

    // public function scopeChildrenOfWithId($query, $key, $depth = 1)
    // {
    //     return $query
    //         ->select('r.id', 'r.status', 'r.revision', 'r.language', 'r.title', 'r.key')
    //         ->leftJoin('content_relations AS relations', 'contents.id', '=', 'relations.relation_id')
    //         ->leftJoin('contents AS r', 'r.id', '=', 'relations.content_id')
    //         ->where('relations.relation_type_id', 4)
    //         ->where('contents.id', );
    // }

    public function scopeOfContentType($query, $key)
    {
        return $query
            ->select('contents.id', 'contents.status', 'contents.revision', 'contents.language', 'contents.title', 'contents.key', 'contents.content')
            ->leftJoin('content_relations AS relations', 'contents.id', '=', 'relations.content_id')
            ->leftJoin('contents AS types', 'types.id', '=', 'relations.relation_id')
            ->where('types.key', $key);
    }

    public function meta()
    {
    	return $this->hasMany(ContentMeta::class, 'content_id');
    }

    public function relations()
    {
    	return $this->hasMany(ContentRelation::class, 'content_id');
    }


    /**
     * Get the model's relationships in array form.
     *
     * @return array
     */
    public function relationsToArray()
    {
        $attributes = parent::relationsToArray();

        foreach ($this->getArrayableRelations() as $key => $value) {

            if($key == 'meta') {
                if(! isset($attributes['metadata'])) {
                    $attributes['metadata'] = [];
                }

                $value->each(function ($metadata) use (&$attributes) {
                    $attributes['metadata'][str_replace('-', '_', $metadata->key)] = $metadata->value;
                });
            }

            if($key == 'relations') {
                if(! isset($attributes['related'])) {
                    $attributes['relationships'] = [];
                }

                foreach($value as $relation) {

                    $newKey = str_replace('-', '_', $relation->relations['relationType']->key);

                    if(! isset($attributes['relationships'][$newKey])) {
                        $attributes['relationships'][$newKey] = [];
                    }

                    if($newKey == str_plural($newKey)) {
                        $attributes['relationships'][$newKey][] = $relation->relations['relation']->key;
                    }
                    else {
                        if(! is_array($attributes['relationships'][$newKey])) {
                            // $attributes['relationships'][$newKey] = [$attributes['relationships'][$newKey]];
                            throw new \Exception('Content relationship is not plural, but has many relations.');
                        }

                        $attributes['relationships'][$newKey] = $relation->relations['relation']->key;
                    }
                }
            }
        }

        return $attributes;
    }
}
