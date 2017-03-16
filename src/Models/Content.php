<?php

namespace Baytek\Laravel\Content\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use Concerns\HasMetadata,
        Scopes\RelationScopes;

    // Defining the table we want to use for all content
    protected $table = 'contents';

    protected $attributes = [
        'language' => 'en',
    ];

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
        'relations.relationType',
    ];

    // Default list of content types
    public $types = [
        'content',
        'content-type',
        'relation-type',
    ];

    public function meta()
    {
        return $this->hasMany(ContentMeta::class, 'content_id');
    }

    public function relations()
    {
        return $this->hasMany(ContentRelation::class, 'content_id');
    }

    // This method saves the content relation
    public function saveRelation($type, $relation_id)
    {
        (new ContentRelation([
            'content_id' => $this->id,
            'relation_id' => $relation_id,
            'relation_type_id' => $this->getContentByKey($type)->id,
        ]))->save();
    }
}
