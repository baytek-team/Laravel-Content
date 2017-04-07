<?php

namespace Baytek\Laravel\Content\Models\Scopes;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use DB;

trait RelationScopes
{

    public function getContent($value)
    {
        $query = parent::withoutGlobalScopes();

        if(is_numeric($value)) {
            $query->find($value);
        }
        else if(is_string($value)) {
            $query->where('contents.key', $value);
        }
        else if(is_object($value) && $value instanceof Collection) {
            $query->whereIn('contents.key', $value->pluck('key'));
        }
        else if(is_object($value) && $value instanceof Model) {
            $query->where('contents.key', $value->key);
        }

        return $query;
    }

    public function getContentByKey($type)
    {
        return parent::withoutGlobalScopes()->where('key', $type)->firstOrFail();
    }

    public function getContentIdByKey($type)
    {
        return $this->getContentByKey($type)->id;
    }

    public function getParents()
    {
        return $this->getParentsOf($this->id);
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
        // $language = \App::getLocale();
        $prefix = env('DB_PREFIX');
        $result = DB::select("SELECT content.id, content.created_at, content.updated_at, content.status, content.revision, content.language, content.key, content.title

        -- get all content
        FROM ${prefix}contents content

        -- get all related closures
        INNER JOIN ${prefix}content_relations closure ON closure.content_id = content.id

        -- filter to keep the parent relations
        INNER JOIN ${prefix}contents relation_type ON relation_type.id = closure.relation_type_id AND (relation_type.key = 'parent-id')

        -- get parent from parent closures
        INNER JOIN ${prefix}contents parent ON parent.id = closure.relation_id

        -- concat the parent key with the content key to set a unique pair to check against the path provided
        WHERE CONCAT(parent.key, '/', content.`key`) = SUBSTRING_INDEX(?, '/', -2);", [$path]);

        return static::hydrate($result);
    }


    // SELECT T2.id, T2.status, T2.revision, T2.language, T2.key, T2.title
    // FROM (
    //     SELECT
    //         @r AS _id,
    //         (
    //             SELECT @r := closure.relation_id
    //             FROM pretzel_content_relations closure, pretzel_contents relation
    //             WHERE relation.id = closure.relation_type_id AND relation.key = 'parent-id' AND relation.language = 'en' AND closure.content_id = _id
    //          ) AS parent,
    //         @l := @l + 1 AS lvl
    //     FROM
    //         (SELECT @r := 41, @l := 0) initialize,
    //         pretzel_contents m
    //     GROUP BY _id
    //     ) T1
    // JOIN pretzel_contents T2
    // ON T1._id = T2.id
    // ORDER BY T1.lvl DESC;
    public function getParentsOf($id)
    {
        $prefix = env('DB_PREFIX');

        return DB::select("SELECT T2.id, T2.created_at, T2.updated_at, T2.status, T2.revision, T2.language, T2.key, T2.title
            FROM (
                SELECT
                    @r AS _id,
                    (
                        SELECT @r := rel.relation_id
                        FROM ${prefix}contents
                        LEFT JOIN ${prefix}content_relations rel
                        ON rel.content_id = @r AND rel.relation_type_id = 4
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

    public function scopeWhereMetadata($query, $key, $value)
    {
        return $query
            ->join('content_meta AS metadata', function($join) use ($key, $value) {
                $join->on('contents.id', '=', 'metadata.content_id')
                    ->where('metadata.key', $key)
                    ->where('metadata.value', $value);
            });
    }

    public function scopeChildrenOf($query, $key, $column = 'key', $depth = 1)
    {
        $query->selectContext = 'r';
        return $query
            ->select('r.id', 'r.created_at', 'r.updated_at', 'r.status', 'r.revision', 'r.language', 'r.title', 'r.key')
            ->join('content_relations AS children_of', 'contents.id', '=', 'children_of.relation_id')
            ->join('contents AS r', 'r.id', '=', 'children_of.content_id')
            ->where('children_of.relation_type_id', $this->getContentIdByKey('parent-id'))
            ->where('contents.'.$column, $key);
    }

    public function scopeOfType($query, $type)
    {
        return $this->scopeOfRelation($query, 'content-type', $type);
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
    public function scopeDescendentsOfType($query, $key, $type, $depth = 1, $deep = 0)
    {
        return $query;
    }

    public function scopeChildrenOfType($query, $key, $type)
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
            });

            if(is_string($key)) {
                $query->where('contents.key', $key);
            }
            else if(is_object($key) && $key instanceof Collection) {
                $query->whereIn('contents.key', $key->pluck('key'));
            }
            else if(is_object($key) && $key instanceof Model) {
                $query->where('contents.key', $key->key);
            }

        return $query;
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

}
