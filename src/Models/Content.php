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

    protected static $metadataCache = [];

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

    public function getContentIdByKey($type)
    {
        return static::withoutGlobalScopes()->where('key', $type)->first()->id;
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
            ->join('content_relations AS relations', 'contents.id', '=', 'relations.relation_id')
            ->join('contents AS r', 'r.id', '=', 'relations.content_id')
            ->where('relations.relation_type_id', $this->getContentIdByKey('parent-id'))
            ->where('contents.'.$column, $key);
    }




    // select * from pretzel_contents c
    // inner join pretzel_content_relations r on r.relation_id = c.id and r.relation_type_id = 4
    // inner join pretzel_contents c2 on c2.id = r.content_id
    // inner join pretzel_content_relations t on t.relation_type_id = 3  and t.relation_id = 9 and r.content_id = t.content_id
    // where c.key = 'Animals';

    public function scopeChildrenOfType($query, $key, $type, $depth = 1)
    {
        return $query
            ->select('r.id', 'r.status', 'r.revision', 'r.language', 'r.title', 'r.key')
            ->join('content_relations AS relations', function ($join) {
                $join->on('contents.id', '=', 'relations.relation_id')
                     ->where('relations.relation_type_id', $this->getContentIdByKey('parent-id'));
            })
            ->join('contents AS r', 'r.id', '=', 'relations.content_id')
            ->join('content_relations AS type', function ($join) use ($type) {
                $join->on('type.content_id', '=', 'relations.content_id')
                     ->where('type.relation_type_id', $this->getContentIdByKey('content-type'))
                     ->where('type.relation_id', $this->getContentIdByKey($type));
            })

            ->where('contents.key', $key);
    }

    public function scopeSortAlphabetical($query)
    {
        return $query->orderBy('r.title', 'desc');
    }

    public function scopeSortNewest($query)
    {
        return $query->orderBy('r.created_at', 'desc');
    }

    public function scopeSortPopular($query)
    {
        return $query;
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
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->attributesToArray(), $this->relationsToArray(), $this->metaDataToArray());
    }


    /**
     * Get the model's relationships in array form.
     *
     * @return array
     */
    public function metaDataToArray()
    {
        $attributes = []; //parent::relationsToArray();

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

        foreach($this->getMetadataAttributes() as $key)
        {
            $attributes['metadata'][$key] = $this->populateMetadataAttribute($key);
        }

        return $attributes;
    }


    /**
     * Get the value of an attribute using its mutator.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function populateMetadataAttribute($key, $value = null)
    {
        return $this->{'set'.Str::studly($key).'Metadata'}($value);
    }

    /**
     * Get the mutated attributes for a given instance.
     *
     * @return array
     */
    public function getMetadataAttributes()
    {
        $class = static::class;

        if (! isset(static::$metadataCache[$class])) {
            static::cacheMetadataAttributes($class);
        }

        return static::$metadataCache[$class];
    }

    /**
     * Extract and cache all the mutated attributes of a class.
     *
     * @param  string  $class
     * @return void
     */
    public static function cacheMetadataAttributes($class)
    {
        static::$metadataCache[$class] = collect(static::getMetadataMethods($class))->map(function ($match) {
            return lcfirst(static::$snakeAttributes ? Str::snake($match) : $match);
        })->all();
    }

    /**
     * Get all of the attribute mutator methods.
     *
     * @param  mixed  $class
     * @return array
     */
    public static function getMetadataMethods($class)
    {
        preg_match_all('/(?<=^|;)set([^;]+?)Metadata(;|$)/', implode(';', get_class_methods($class)), $matches);

        return $matches[1];
    }
}
