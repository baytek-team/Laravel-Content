<?php

namespace Baytek\Laravel\Content\Models\Scopes;

use Baytek\Laravel\Content\Exception\ContentNotFoundException;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use DB;
use Cache;
use Exception;

trait RelationScopes
{
    public function abstractSelect($query, $value)
    {
        if(is_numeric($value)) {
            $query->where('contents.id', $value);
        }
        else if(is_string($value)) {
            $query->where('contents.key', $value);
        }
        else if(is_array($value)) {
            $first = array_first($value);

            if(is_numeric($first)) {
                $query->whereIn('contents.id', $value);
            }
            else if(is_string($first)) {
                $query->whereIn('contents.key', $value);
            }
            else {
                throw new Exception('Passed array but value type is not supported');
            }
        }
        else if(is_object($value) && $value instanceof Collection) {
            $query->whereIn('contents.id', $value->pluck('id'));
        }
        else if(is_object($value) && $value instanceof Model) {
            $query->where('contents.key', $value->key);
        }
        return $query;
    }


    /**
     * [getContent description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function getContent($value)
    {
        $query = parent::withoutGlobalScopes();

        return $this->abstractSelect($query, $value);
    }

    /**
     *
     * @return [type] [description]
     */
    public function relatedBy($type)
    {
        $contentId = $this->id;
        $relations = Cache::get('content.cache.relations', collect([]));
        $parentTypeId = $this->getContentIdByKey($type);

        $result = $relations->filter(function ($relation) use ($contentId, $parentTypeId) {
            return $relation->content_id == $contentId && $relation->relation_type_id == $parentTypeId;
        });

        return $result;
    }

    /**
     *
     * @return [type] [description]
     */
    public function parent()
    {
        $result = $this->relatedBy('parent-id');

        if($result->isEmpty()) {
            return null;
        }

        return $result->pluck('relation_id')->first();
    }


    /**
     *
     * @return [type] [description]
     */
    public function translation()
    {
        $result = $this->relatedBy('translations');

        if($result->isEmpty()) {
            return null;
        }

        return $result->pluck('relation_id')->first();
    }

    public function getContentByKey($type)
    {
        $result = parent::withoutGlobalScopes()->where('key', $type)->get();

        if($result->isEmpty()) {
            throw new ContentNotFoundException($type);
        }

        return $result->first();
    }

    public function getContentIdByKeys($types)
    {
        $result = [];
        foreach($types as $type) {
            array_push($result, $this->getContentIdByKey($type));
        }
        return $result;
    }

    public function getContentIdByKey($type)
    {
        if($cache = Cache::get('content.cache.keys')) {
            if($cached = $cache->flip()->get($type, false)) {
                return $cached;
            }
        }

        return $this->getContentByKey($type)->id;
    }

    public function getParents()
    {
        return $this->getParentsOf($this->id);
    }

    // Alias for getContentIdByKey
    public function getIdWithKey($key)
    {
        return $this->getContentIdByKey($key);
    }

    //
    public function getKeyWithId($id)
    {
        if($cache = Cache::get('content.cache.keys')) {
            if($cached = $cache->get($id, false)) {
                return $cached;
            }
        }
        return false;
    }


    public function countChildrenOfTypeById($id, $type)
    {
        $prefix = env('DB_PREFIX');
        $language = \App::getLocale();

        return DB::select("SELECT COUNT(resource.key) resource_count

            -- get all closures
            FROM ${prefix}content_relations closure1

            -- filter closures to have content type relationship only
            INNER JOIN ${prefix}contents relation ON relation.id = closure1.relation_type_id AND (relation.key = 'content-type' AND relation.language = '${language}')

            -- filter content type to only keep resources content type
            INNER JOIN ${prefix}contents content_type ON content_type.id = closure1.relation_id AND (content_type.key = ? AND content_type.language = '${language}')

            -- get resources from the resource content type closures
            INNER JOIN ${prefix}contents resource ON resource.id = closure1.content_id

            -- get all closures associated with the resource
            INNER JOIN ${prefix}content_relations closure2 ON closure2.content_id = resource.id

            -- filter to keep the parent relations
            INNER JOIN ${prefix}contents relation_type ON relation_type.id = closure2.relation_type_id AND (relation_type.key = 'parent-id' AND relation_type.language = '${language}')

            -- get parent from parent closures
            INNER JOIN ${prefix}contents parent ON parent.id = closure2.relation_id

            -- final filter to keep only resources with the content passed to this query (@r := var) or any of its descendant as its parent
            INNER JOIN
            (
                SELECT GROUP_CONCAT(T1._id) parent_ids
                FROM
                    (SELECT @r := ?, @l := 0) ini_vars,
                    (SELECT
                        @r AS _id,
                            (
                                SELECT @r := GROUP_CONCAT(closure.content_id)
                                FROM ${prefix}content_relations closure, ${prefix}contents relation
                                WHERE relation.id = closure.relation_type_id AND relation.key = 'parent-id' AND relation.language = '${language}' AND FIND_IN_SET(closure.relation_id, _id)
                            ) AS parent,
                            @l := @l + 1 AS lvl
                        FROM
                            ${prefix}contents m
                    ) T1
            ) ids ON FIND_IN_SET(closure2.relation_id, ids.parent_ids);", [$type, $id]);
    }


    public function getWithPath($path)
    {
        $parts = array_reverse(explode('/', $path));

        $prefix = env('DB_PREFIX');
        $query = "SELECT level0c.id, level0c.created_at, level0c.updated_at, level0c.status, level0c.revision, level0c.language, level0c.key, level0c.title

        -- get all content
        FROM ${prefix}contents level0c
        ";

        for($x = 0; $x < count($parts); $x++) {
            $level = $x + 1;
            $query .= "
            inner join
                ${prefix}content_relations as level${level} on
                    level${level}.content_id = level${x}c.id AND
                    level${level}.relation_type_id = 4
            inner join ${prefix}contents as level${level}c on level${level}c.id = level${level}.relation_id
            ";
        }

        $query .= " WHERE 0=0 ";

        for($x = 0; $x < count($parts); $x++) {
            $query .= "AND level${x}c.`key` = '$parts[$x]' ";
        }

        $result = DB::select($query, [$path]);

        $collection = static::hydrate($result);

        if($collection->isEmpty()) {
            throw new ContentNotFoundException($path);
        }

        return $collection;
    }

    public function getParentsOf($id)
    {
        $prefix = env('DB_PREFIX');

        $result = DB::select("SELECT T2.id, T2.created_at, T2.updated_at, T2.status, T2.revision, T2.language, T2.key, T2.title
            FROM (
                SELECT
                    @r AS _id,
                    (
                        SELECT @r := closure.relation_id
                        FROM ${prefix}content_relations closure, ${prefix}contents relation
                        WHERE relation.id = closure.relation_type_id AND relation.key = 'parent-id' AND relation.language = 'en' AND closure.content_id = _id
                     ) AS parent,
                    @l := @l + 1 AS lvl
                FROM
                    (SELECT @r := ?, @l := 0) initialize,
                    ${prefix}contents m
                GROUP BY _id
                ) T1
            JOIN ${prefix}contents T2
            ON T1._id = T2.id
            ORDER BY T1.lvl DESC;


            -- SELECT T2.id, T2.created_at, T2.updated_at, T2.status, T2.revision, T2.language, T2.key, T2.title
            -- FROM (
            --     SELECT
            --         @r AS _id,
            --         (
            --             SELECT @r := rel.relation_id
            --             FROM ${prefix}contents
            --             LEFT JOIN ${prefix}content_relations rel
            --             ON rel.content_id = @r AND rel.relation_type_id = 4
            --             WHERE ${prefix}contents.id = _id
            --         ) AS parent_id,
            --         @l := @l + 1 AS lvl
            --     FROM
            --         (SELECT @r := ?, @l := 0) vars,
            --         ${prefix}contents m
            --     WHERE @r <> 0) T1
            -- JOIN ${prefix}contents T2
            -- ON T1._id = T2.id
            -- ORDER BY T1.lvl DESC;
        ", [$id]);

        return $result;
    }

    public function scopeWhereMetadata($query, $key, $value, $comparison = '=')
    {
        return $query
            ->join('content_meta AS metadata', function($join) use ($key, $value, $comparison) {
                $join->on('contents.id', '=', 'metadata.content_id')
                    ->where('metadata.key', $key)
                    ->where('metadata.value', $comparison, $value);
            });
    }

    public function scopeChildrenOf($query, $key, $column = 'key', $depth = 1)
    {
        $query->selectContext = 'r';

        $query
            ->select('r.id', 'r.created_at', 'r.updated_at', 'r.status', 'r.revision', 'r.language', 'r.title', 'r.key')
            ->distinct()
            ->join('content_relations AS children_of', 'contents.id', '=', 'children_of.relation_id')
            ->join('contents AS r', 'r.id', '=', 'children_of.content_id')
            ->where('children_of.relation_type_id', $this->getContentIdByKey('parent-id'));
            // ->where('contents.'.$column, $key);

            $this->abstractSelect($query, $key);

            // if(is_string($key)) {
            //     $query->where('contents.key', $key);
            // }
            // else if(is_integer($key)) {
            //     $query->where('contents.id', $key);
            // }
            // else if(is_object($key) && $key instanceof Collection) {
            //     $query->whereIn('contents.id', $key->pluck('id'));
            // }
            // else if(is_object($key) && $key instanceof Model) {
            //     $query->where('contents.key', $key->key);
            // }
    }

    public function scopeOfType($query, $type)
    {
        return $this->scopeOfRelation($query, 'content-type', $type);
    }

    public function scopeInTypes($query, $types)
    {
        $query->selectContext = 'contents';

        $typeIds = [];
        foreach( $types as $type ) {
            $typeIds[] = $this->getContentIdByKey($type);
        }

        return $query
            ->select('contents.id', 'contents.created_at', 'contents.updated_at', 'contents.status', 'contents.revision', 'contents.language', 'contents.title', 'contents.key')
            ->join('content_relations AS of_relation_type', function ($join) use ($typeIds) {
                $join->on('contents.id', '=', 'of_relation_type.content_id')
                    ->where('of_relation_type.relation_type_id', $this->getContentIdByKey('content-type'))
                    ->whereIn('of_relation_type.relation_id', $typeIds);
            });
    }

    public function scopeOfRelation($query, $relation, $type)
    {
        $query->selectContext = 'contents';

        return $query
            ->select('contents.id', 'contents.created_at', 'contents.updated_at', 'contents.status', 'contents.revision', 'contents.language', 'contents.title', 'contents.key')
            ->join('content_relations AS of_relation_type', function ($join) use ($type, $relation) {
                $join->on('contents.id', '=', 'of_relation_type.content_id')
                    ->where('of_relation_type.relation_type_id', $this->getContentIdByKey($relation))
                    ->where('of_relation_type.relation_id', $this->getContentIdByKey($type));
            });
            // ->join('contents AS r', 'r.id', '=', 'type.content_id')
            // ->where('contents.key', $key);
    }

    // select * from pretzel_contents c
    // inner join pretzel_content_relations r on r.relation_id = c.id and r.relation_type_id = 4
    // inner join pretzel_contents c2 on c2.id = r.content_id
    // inner join pretzel_content_relations t on t.relation_type_id = 3  and t.relation_id = 9 and r.content_id = t.content_id
    // where c.key = 'Animals';
    // public function scopeDescendentsOfType($query, $key, $type, $depth = 1, $deep = 0)
    // {
    //     return $query;
    // }

    //Must use ID since we don't have unique keys
    public function scopeDescendentsOfType($query, $id, $type)
    {
        $prefix = env('DB_PREFIX');
        $language = \App::getLocale();

        $result = DB::select("SELECT resource.id, resource.created_at, resource.updated_at, resource.status, resource.revision, resource.language, resource.title, resource.key

            -- get all closures
            FROM ${prefix}content_relations closure1

            -- filter closures to have content type relationship only
            INNER JOIN ${prefix}contents relation ON relation.id = closure1.relation_type_id AND (relation.key = 'content-type' AND relation.language = '${language}')

            -- filter content type to only keep resources content type
            INNER JOIN ${prefix}contents content_type ON content_type.id = closure1.relation_id AND (content_type.key = ? AND content_type.language = '${language}')

            -- get resources from the resource content type closures
            INNER JOIN ${prefix}contents resource ON resource.id = closure1.content_id

            -- get all closures associated with the resource
            INNER JOIN ${prefix}content_relations closure2 ON closure2.content_id = resource.id

            -- filter to keep the parent relations
            INNER JOIN ${prefix}contents relation_type ON relation_type.id = closure2.relation_type_id AND (relation_type.key = 'parent-id' AND relation_type.language = '${language}')

            -- get parent from parent closures
            INNER JOIN ${prefix}contents parent ON parent.id = closure2.relation_id

            -- final filter to keep only resources with the content passed to this query (@r := var) or any of its descendant as its parent
            INNER JOIN
            (
                SELECT GROUP_CONCAT(T1._id) parent_ids
                FROM
                    (SELECT @r := ?, @l := 0) ini_vars,
                    (SELECT
                        @r AS _id,
                            (
                                SELECT @r := GROUP_CONCAT(closure.content_id)
                                FROM ${prefix}content_relations closure, ${prefix}contents relation
                                WHERE relation.id = closure.relation_type_id AND relation.key = 'parent-id' AND relation.language = '${language}' AND FIND_IN_SET(closure.relation_id, _id)
                            ) AS parent,
                            @l := @l + 1 AS lvl
                        FROM
                            ${prefix}contents m
                    ) T1
            ) ids ON FIND_IN_SET(closure2.relation_id, ids.parent_ids);", [$type, $id]);

        return static::hydrate($result);
    }

    public function scopeChildrenOfType($query, $key, $type)
    {
        $query->selectContext = 'r';
        $query
            ->select('r.id', 'r.created_at', 'r.updated_at', 'r.status', 'r.revision', 'r.language', 'r.title', 'r.key')
            ->distinct()
            ->join('content_relations AS children_of_type', function ($join) {
                $join->on('contents.id', '=', 'children_of_type.relation_id')
                    ->where('children_of_type.relation_type_id', $this->getContentIdByKey('parent-id'));
            })
            ->join('contents AS r', 'r.id', '=', 'children_of_type.content_id')
            ->join('content_relations AS relation_type', function ($join) use ($type) {
                $join->on('relation_type.content_id', '=', 'children_of_type.content_id')
                    ->where('relation_type.relation_type_id', $this->getContentIdByKey('content-type'));
                    if(is_array($type)) {
                        $join->whereIn('relation_type.relation_id', $this->getContentIdByKeys($type));
                    }
                    else {
                        $join->where('relation_type.relation_id', $this->getContentIdByKey($type));
                    }
            });


        $this->abstractSelect($query, $key);
    }

    public function scopeChildenOfTypeWhereMetadata($query, $key, $type, $metakey, $metavalue, $comparison = '=')
    {
        $query->selectContext = 'r';
        $query
            ->select('r.id', 'r.created_at', 'r.updated_at', 'r.status', 'r.revision', 'r.language', 'r.title', 'r.key')
            ->join('content_relations AS children_of_type', function ($join) {
                $join->on('contents.id', '=', 'children_of_type.relation_id')
                    ->where('children_of_type.relation_type_id', $this->getContentIdByKey('parent-id'));
            })
            ->join('contents AS r', 'r.id', '=', 'children_of_type.content_id')
            ->join('content_relations AS relation_type', function ($join) use ($type) {
                $join->on('relation_type.content_id', '=', 'children_of_type.content_id')
                    ->where('relation_type.relation_type_id', $this->getContentIdByKey('content-type'))
                    ->where('relation_type.relation_id', $this->getContentIdByKey($type));
            })
            ->join('content_meta AS metadata', function($join) use ($metakey, $metavalue, $comparison) {
                $join->on('r.id', '=', 'metadata.content_id')
                    ->where('metadata.key', $metakey)
                    ->where('metadata.value', $comparison, $metavalue);
            });

        $this->abstractSelect($query, $key);
    }


    // select content.* from pretzel_contents c

    // inner join pretzel_content_relations r on r.content_id = c.id and r.relation_type_id = 25
    // inner join pretzel_contents content on r.relation_id = content.id

    // where c.key = 'ten-reasons-why-chocolate-is-better-than-vanilla'

    public function scopeChildrenOfRelation($query, $key, $relation)
    {
        $query->selectContext = 'r';

        return $query
            ->select('r.id', 'r.created_at', 'r.updated_at', 'r.status', 'r.revision', 'r.language', 'r.title', 'r.key')
            ->join('content_relations AS children_of_relation', function ($join) use ($relation) {
                $join->on('children_of_relation.content_id', '=', 'contents.id')
                    ->where('children_of_relation.relation_type_id', $this->getContentIdByKey($relation));
            })
            ->join('contents AS r', 'r.id', '=', 'children_of_relation.relation_id')
            ->where('contents.key', $key);
    }

    public function scopeChildOfType($query, $parent, $type, $key)
    {
        $query->selectContext = 'r';

        return $query
            ->select('r.id', 'r.created_at', 'r.updated_at', 'r.status', 'r.revision', 'r.language', 'r.title', 'r.key')
            ->join('content_relations AS child_of_type', function ($join) {
                $join->on('contents.id', '=', 'child_of_type.relation_id')
                    ->where('child_of_type.relation_type_id', $this->getContentIdByKey('parent-id'));
            })
            ->join('contents AS r', 'r.id', '=', 'child_of_type.content_id')
            ->join('content_relations AS type', function ($join) use ($type) {
                $join->on('type.content_id', '=', 'child_of_type.content_id')
                    ->where('type.relation_type_id', $this->getContentIdByKey('content-type'))
                    ->where('type.relation_id', $this->getContentIdByKey($type));
            })
            ->where('r.key', $key)
            ->whereIn('contents.id', $parent->pluck('id'));
    }

    public function scopeSortAlphabetical($query)
    {
        $prefix = explode('.', $query->getQuery()->columns[0])[0] ?: 'contents';

        return $query->orderBy($prefix.'.title', 'asc');
    }

    public function scopeSortNewest($query)
    {
        $prefix = explode('.', $query->getQuery()->columns[0])[0] ?: 'contents';

        return $query->orderBy($prefix.'.created_at', 'desc');
    }

    public function scopeSortPopular($query)
    {
        return $query;
    }

    public function scopeWithContents($query)
    {
        return $query->select( (explode('.', $query->getQuery()->columns[0])[0] ?: 'contents')  .'.*');
    }

    /**
     * Orders the query by the specified meta key value.
     */
    public function scopeOrderByMeta($query, $key, $direction = 'asc')
    {
        return $query
            ->join('content_meta AS metadata_order', function($join) use ($key) {
                $join->on('contents.id', '=', 'metadata_order.content_id')
                    ->where('metadata_order.key', $key);
            })
            ->orderBy('metadata_order.value', $direction);
    }

    public function scopeOfContentType($query, $key)
    {
        $query->selectContext = 'contents';

        return $query
            ->select('contents.id', 'contents.created_at', 'contents.updated_at', 'contents.status', 'contents.revision', 'contents.language', 'contents.title', 'contents.key', 'contents.content')
            ->leftJoin('content_relations AS relations', 'contents.id', '=', 'relations.content_id')
            ->leftJoin('contents AS types', 'types.id', '=', 'relations.relation_id')
            ->where('types.key', $key);
    }

    public function scopeSearch($query, $search)
    {
        $table = (\App::getLocale() !== 'en') ? 'language' : 'contents';

        return $query->where($table.'.title', 'like', [$search])
            ->orderBy($table.'.title', 'asc');
    }

}
