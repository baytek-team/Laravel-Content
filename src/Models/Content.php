<?php

namespace Baytek\Laravel\Content\Models;

use Baytek\Laravel\Content\Models\ContentMeta;
use Baytek\Laravel\Content\Models\ContentRelation;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table = 'contents';
	protected $fillable = [
		'status',
		'language',
        'key',
		'title',
		'content',
	];

    public $relationships = [];

    public static $eager = [
        'meta',
        'relations',
        'relations.relation',
        'relations.relationType'
    ];

    public $types = [
        'content',
        'content-type',
        'relation-type',
    ];

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
        return static::where('key', $type)->first();
    }

    public function scopeChildrenOf($query, $key, $depth = 1)
    {
        return $query
            ->select('contents.id', 'contents.status', 'contents.revision', 'contents.language', 'contents.title', 'contents.key')
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
}
