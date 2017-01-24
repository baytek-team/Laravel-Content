<?php

namespace Baytek\LaravelContent\Models;

use Baytek\LaravelContent\Models\ContentMeta;
use Baytek\LaravelContent\Models\ContentRelation;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table = 'contents';
	protected $fillable = [
		'status',
		'language',
		'title',
		'content',
	];

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

    public function scopeChildrenOf($query, $title, $depth = 1)
    {
        $query
            ->select('contents.id', 'contents.status', 'contents.revision', 'contents.language', 'contents.title')
            ->leftJoin('content_relations AS relations', 'contents.id', '=', 'relations.content_id')
            ->leftJoin('contents AS types', 'types.id', '=', 'relations.relation_id')
            ->where('types.title', $title)
            ->get();
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
