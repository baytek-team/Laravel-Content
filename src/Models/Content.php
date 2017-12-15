<?php

namespace Baytek\Laravel\Content\Models;

use Baytek\Laravel\Content\Models\Scopes\TranslationScope;
use Baytek\Laravel\Content\Models\Scopes\ContentTypeScope;

use Baytek\Laravel\StatusBit\Statusable;
use Baytek\Laravel\StatusBit\Interfaces\StatusInterface;

use Baytek\Laravel\Content\Eloquent\Builder;
use Baytek\Laravel\Content\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use Cache;
use DB;

class Content extends Model implements StatusInterface
{
    use Concerns\HasMetadata,
        Concerns\HasRelationships,
        Scopes\RelationScopes,
        SoftDeletes;

    use Statusable {
        Statusable::__construct as private __statusConstruct;
    }

    /**
     * List of fields which should be cast when rendering JSON
     * @var array
     */
    protected $casts = [
        'status' => 'int',
        'revision' => 'int',
    ];

    /**
     * Defining the table we want to use for all content
     * @var string
     */
    protected $table = 'contents';

    /**
     * Is the table aliased?
     * @var boolean
     */
    protected $aliased = false;

    /**
     * Variable once used to tell the depth of the hierarchy
     * @todo  Remove this if no longer used
     * @var unknown
     */
    // public $depth;

    /**
     * Variable used to store the metadata fields while the content is saving
     * @var array
     */
    protected $metadataAttributes = [];

    /**
     * List of attributes that should be saved when the model is saved.
     * @var array
     */
    protected $attributes = [
        'language' => 'en',
    ];

    /**
     * List of fields that can be mass assigned
     * @var array
     */
    protected $fillable = [
        'revision',
        'status',
        'language',
        'key',
        'title',
        'content',
    ];

    /**
     * List of relationships that should be populated when the model is saved
     * @var array
     */
    public $relationships = [];

    /**
     * Eager loading relationship lists
     * @todo  This may no longer be used, remove if not required.
     * @var array
     */
    public static $eager = [
        'meta',
        'relations',
        'relations.relation',
        'relations.relationType',
    ];

    /**
     * The constructor method of the model.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->__statusConstruct($attributes);

        if (property_exists($this, 'metadata')) {
            $this->fillable(array_merge($this->fillable, $this->metadata));
        }

        if (property_exists($this, 'contentType')) {
            static::addGlobalScope('content_type', function (Builder $builder) {
                $builder->ofType($this->contentType);
            });
        }

        parent::__construct($attributes);
    }

    /**
     * Get route key, this is generic function
     * @return String the value for the key
     */
    public function getRouteKeyName()
    {
        return $this->getTable().'.id';
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (property_exists($model, 'metadata')) {
                $model->metadataAttributes = collect($model->attributes)->only($model->metadata)->all();
                $model->attributes = collect($model->attributes)->except($model->metadata)->all();
            }
        });

        // After the model has been saved.
        self::created(function ($model) {
            // Check if there is any metadata to save.
            if (property_exists($model, 'metadata')) {
                $model->saveMetadata($model->metadataAttributes);
            }

            // Check to see if there are any relationships required to save
            if (property_exists($model, 'relationships')) {
                $model->saveRelations($model->relationships);
            }
        });

        self::updating(function ($model) {
            if (property_exists($model, 'metadata')) {
                $model->metadataAttributes = collect($model->attributes)->only($model->metadata)->all();
                $model->attributes = collect($model->attributes)->except($model->metadata)->all();
            }
        });

        self::updated(function ($model) {
            // Check if there is any metadata to save.
            if (property_exists($model, 'metadata')) {
                $model->saveMetadata($model->metadataAttributes);
            }

            // Check to see if there are any relationships required to save
            if (property_exists($model, 'relationships')) {
                $model->saveRelations($model->relationships);
            }
        });

        static::addGlobalScope('not_restricted', function (Builder $builder) {
            $builder->withStatus(['exclude' => [self::RESTRICTED]]);
        });

        // Order by the ordering field in the database.
        if (config('content.ordering', false)) {
            static::addGlobalScope('ordered', function (Builder $builder) {
                $prefix = DB::getTablePrefix();
                $context = property_exists($builder, 'selectContext') ? $builder->selectContext : $builder->getModel()->table;

                $builder->orderBy(DB::raw("IFNULL(`$prefix$context`.`order`, 4294967295 + 1), id"));
            });
        }

        static::addGlobalScope(new ContentTypeScope);

        if (\App::getLocale() != 'en') {
            static::addGlobalScope(new TranslationScope);
        }
    }

    /**
     * Meta relationship
     * @return   [description]
     */
    public function meta()
    {
        return $this->hasMany(ContentMeta::class, 'content_id');
    }

    public function restrictedMeta()
    {
        return $this->hasMany(ContentMeta::class, 'content_id')->withoutGlobalScope('not_restricted');
    }

    public function relations()
    {
        return $this->hasMany(ContentRelation::class, 'content_id');
    }

    public function revisions()
    {
        return $this->hasMany(ContentHistory::class, 'content_id');
    }

    public function children()
    {
        return $this->hasManyContent(Content::class, [
            // 'depth' => 1,
            'children' => true,
            // 'relation' => 'parent-id'
        ]);
    }

    // public function webpages()
    // {
    //     return $this->association(Content::class, [
    //         // 'relation' => 'webpage',
    //         'children' => true,
    //         // 'metadata' => [
    //         //     ['author_id', '=', 1],
    //         //     // 'author_id' => 1 // This method assumes the operator is '='
    //         // ]
    //     ]);
    // }

    public function scopeRootNodes($builder)
    {
        // $builder
    }


    public function getMetaRecord($key)
    {
        $meta = $this->meta->where('key', $key);

        if ($meta->count()) {
            return $meta->first();
        }

        return null;
    }

    public function getMeta($key, $default = null)
    {
        if ($meta = $this->getMetaRecord($key)) {
            return $meta->value;
        }

        return $default;
    }

    public function getRelationship($type)
    {
        return Content::find($this->relatedBy($type)->pluck('relation_id')->all())->first();

        // foreach($this->relations()->get() as $relation) {
        //     if($relation->relation_type_id == content_id($type)) {
        //         return Content::find($relation->relation_id);
        //     }
        // }
    }

    public function removeRelationByType($type)
    {
        $relation = ContentRelation::where([
            'content_id' => $this->id,
            'relation_type_id' => content_id($type)
        ])->delete();
    }

    //Remove a specific relation by relation_id
    public function removeRelationById($id)
    {
        $relation = ContentRelation::where([
            'content_id' => $this->id,
            'relation_id' => $id,
        ])->delete();
    }

    public function saveRelations($relations)
    {
        foreach ($relations as $key => $value) {
            $this->saveRelation($key, $value);
        }
    }

    // This method saves the content relation
    public function saveRelation($type, $content)
    {
        $relation = ContentRelation::where([
            'content_id' => $this->id,
            'relation_id' => content_id($content),
            'relation_type_id' => content_id($type)
        ])->get();

        if ($relation->count()) {
            $relation->first()->relation_id = content_id($content);
            $relation->first()->save();
        } else {
            // We need to check to see if the relation exists already before creating a new one.
            (new ContentRelation([
                'content_id' => $this->id,
                'relation_id' => content_id($content),
                'relation_type_id' => content_id($type),
            ]))->save();
        }
    }

    public function saveMetadata($key, $value = null)
    {
        if (is_string($key)) {
            $set = collect([$key => $value]);
        } elseif (is_array($key)) {
            $set = collect($key);
        } elseif (is_object($key) && $key instanceof Collection) {
            $set = $key;
        }

        $set->each(function ($value, $key) {
            $metadata = ContentMeta::where([
                'content_id' => $this->id,
                'language' => \App::getLocale(),
                'key' => $key
            ])->get();

            if ($metadata->count()) {
                $metadata->first()->value = $value;
                $metadata->first()->save();
            } else {
                $meta = (new ContentMeta([
                    'content_id' => $this->id,
                    'key' => $key,
                    'language' => \App::getLocale(),
                    'value' => $value,
                ]));

                $meta->save();
                $this->meta()->save($meta);
            }
        });
    }

    public function isAliased()
    {
        return $this->aliased;
    }

    public function setAlias($alias, $aliased = false)
    {
        $this->aliased = $aliased;
        $this->alias = $alias;
    }

    public function getTable()
    {
        return ($this->alias ?: parent::getTable());
    }
}
