<?php

namespace Baytek\Laravel\Content\Models\Scopes;

use DB;

trait RelationScopes
{
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
        return $query
            ->select('r.id', 'r.status', 'r.revision', 'r.language', 'r.title', 'r.key')
            ->join('content_relations AS relations', 'contents.id', '=', 'relations.relation_id')
            ->join('contents AS r', 'r.id', '=', 'relations.content_id')
            ->where('relations.relation_type_id', $this->getContentIdByKey('parent-id'))
            ->where('contents.'.$column, $key);
    }

    public function scopeOfType($query, $type)
    {
        return $this->scopeOfRelation($query, 'content-type', $type);
    }

    public function scopeOfRelation($query, $relation, $type)
    {
        return $query
            ->select('contents.id', 'contents.status', 'contents.revision', 'contents.language', 'contents.title', 'contents.key')
            ->join('content_relations AS type', function ($join) use ($type, $relation) {
                $join->on('contents.id', '=', 'type.content_id')
                     ->where('type.relation_type_id', $this->getContentIdByKey($relation))
                     ->where('type.relation_id', $this->getContentIdByKey($type));
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

    public function scopeChildOfType($query, $parent, $type, $key)
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
            ->where('r.key', $key)
            ->where('contents.key', $parent);
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

}