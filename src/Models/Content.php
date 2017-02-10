<?php

namespace Baytek\Laravel\Content\Models;

use Baytek\Laravel\Content\Models\ContentMeta;
use Baytek\Laravel\Content\Models\ContentRelation;
use Illuminate\Database\Eloquent\Model;

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
}
