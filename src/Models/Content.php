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
        'revision',
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

    public function getMetaRecord($key)
    {
        $meta = $this->meta->where('key', $key);

        if($meta->count()) {
            return $meta->first();
        }

        return null;
    }

    public function getMeta($key, $default = null)
    {
        if($meta = $this->getMetaRecord($key)) {
            return $meta->value;
        }

        return $default;
    }

    public function getRelationship($type)
    {
        foreach($this->relations()->get() as $relation) {
            if($relation->relation_type_id == $this->getContentByKey($type)->id) {
                return Content::find($relation->relation_id);
            }
        }
    }

    public function removeRelationByType($type)
    {
        $relation = ContentRelation::where([
            'content_id' => $this->id,
            'relation_type_id' => $this->getContentByKey($type)->id
        ])->delete();
    }

    // This method saves the content relation
    public function saveRelation($type, $relation_id)
    {
        $relation = ContentRelation::where([
            'content_id' => $this->id,
            'relation_id' => $relation_id,
            'relation_type_id' => $this->getContentByKey($type)->id
        ])->get();

        if($relation->count()) {
            $relation->first()->relation_id = $relation_id;
            $relation->first()->save();
        }
        else {
            // We need to check to see if the relation exists already before creating a new one.
            (new ContentRelation([
                'content_id' => $this->id,
                'relation_id' => $relation_id,
                'relation_type_id' => $this->getContentByKey($type)->id,
            ]))->save();
        }
    }

    public function saveMetadata($key, $value)
    {
        $metadata = ContentMeta::where([
            'content_id' => $this->id,
            'language' => \App::getLocale(),
            'key' => $key
        ])->get();

        if($metadata->count()) {
            $metadata->first()->value = $value;
            $metadata->first()->save();
        }
        else {
            (new ContentMeta([
                'content_id' => $this->id,
                'key' => $key,
                'language' => \App::getLocale(),
                'value' => $value,
            ]))->save();
        }
    }
}
