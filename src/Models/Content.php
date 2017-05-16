<?php

namespace Baytek\Laravel\Content\Models;

use Baytek\Laravel\Content\Models\Scopes\TranslationScope;
use Baytek\LaravelStatusBit\Statusable;
use Baytek\LaravelStatusBit\StatusInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model implements StatusInterface
{
    use Concerns\HasMetadata,
        Scopes\RelationScopes,
        SoftDeletes,
        Statusable;

    // Defining the table we want to use for all content
    protected $table = 'contents';

    public $depth;

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

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        if(\App::getLocale() != 'en') {
            static::addGlobalScope(new TranslationScope);
        }
    }

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
        return Content::find($this->relatedBy($type)->pluck('relation_id')->all())->first();

        // foreach($this->relations()->get() as $relation) {
        //     if($relation->relation_type_id == $this->getContentIdByKey($type)) {
        //         return Content::find($relation->relation_id);
        //     }
        // }
    }

    public function removeRelationByType($type)
    {
        $relation = ContentRelation::where([
            'content_id' => $this->id,
            'relation_type_id' => $this->getContentIdByKey($type)
        ])->delete();
    }

    // This method saves the content relation
    public function saveRelation($type, $relation_id)
    {
        $relation = ContentRelation::where([
            'content_id' => $this->id,
            'relation_id' => $relation_id,
            'relation_type_id' => $this->getContentIdByKey($type)
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
                'relation_type_id' => $this->getContentIdByKey($type),
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
            $meta = (new ContentMeta([
                'content_id' => $this->id,
                'key' => $key,
                'language' => \App::getLocale(),
                'value' => $value,
            ]));

            $meta->save();
            $this->meta()->save($meta);
        }
    }



    public static function loopying(&$contents, $relations, &$all, $depth = 0, &$used = [], &$result = [])
    {
        foreach($contents as $content) {
            if(!in_array($content->id, $used)) {
                // echo str_repeat('&mdash;', $depth) . " {$content->title}<br/>";

                $related = $relations->where('relation_id', $content->id)->pluck('content_id');
                $children = $all->only($related->all())->keyBy('id');

                array_push($used, $content->id);

                $all->forget($content->id);
                $content->depth = $depth;
                array_push($result, $content);

                static::loopying($children, $relations, $all, $depth + 1, $used, $result);

            }
        }

        return $result;
    }

}
