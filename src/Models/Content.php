<?php

namespace Baytek\LaravelContent\Models;

use Baytek\LaravelContent\Models\ContentMeta;
use Baytek\LaravelContent\Models\ContentRelation;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
	protected $fillable = [
		'status',
		'language',
		'title',
		'content',
	];

    public static $eager = [
        'meta',
        'relations',
        // 'relations.content',
        'relations.relation',
        'relations.relationType'
    ];

    public $types = [
        'content',
        'content-type',
        'relation-type',
    ];


    public function meta()
    {
    	return $this->hasMany(ContentMeta::class);
    }

    public function relations()
    {
    	return $this->hasMany(ContentRelation::class);
        // return $this->hasManyThrough(
        //     ContentRelation::class, Content::class,
        //     'id', 'content_id', 'id'
        // );
    }
}
