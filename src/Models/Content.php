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

        if (property_exists($this, 'contentType')) {
            static::addGlobalScope('content_type', function (Builder $builder) {
                $builder->ofType($this->contentType);
            });
        }

        parent::__construct($attributes);
    }


    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // After the model has been saved.
        // self::created(function ($model) {
        //     // Check if there is any metadata to save.
        //     if (property_exists($model, 'metadata')) {
        //         $model->saveMetadata($model->metadataAttributes);
        //     }

        //     // Check to see if there are any relationships required to save
        //     if (property_exists($model, 'relationships')) {
        //         $model->saveRelations($model->relationships);
        //     }
        // });

        // self::updated(function ($model) {
        //     // Check if there is any metadata to save.
        //     if (property_exists($model, 'metadata')) {
        //         $model->saveMetadata($model->metadataAttributes);
        //     }

        //     // Check to see if there are any relationships required to save
        //     if (property_exists($model, 'relationships')) {
        //         $model->saveRelations($model->relationships);
        //     }
        // });

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

        // Do a left join to get the content type of the the content
        static::addGlobalScope(new ContentTypeScope);

        if (\App::getLocale() != 'en') {
            static::addGlobalScope(new TranslationScope);
        }
    }

    /**
     * Get route key name, this is generic function
     * 
     * @return String the value for the key
     */
    public function getRouteKeyName()
    {
        return $this->getTable() . '.id';
    }

    /**
     * Get route key, this is generic function
     * 
     * @return String the value for the key
     */
    public function getRouteKey()
    {
        return $this->getAttribute('id');
    }

    /***
     *
     * RELATIONS
     *
     */
    
     /**
     * Get the content revisions
     *
     * @return hasMany
     */
    public function relations()
    {
        return $this->hasMany(ContentRelation::class, 'content_id');
    }

    /**
     * Get the content revisions
     *
     * @return hasMany
     */
    public function revisions()
    {
        return $this->hasMany(ContentHistory::class, 'content_id');
    }

    /**
     * Expirmental method for getting children relations
     *
     * @return hasManyContent
     */
    public function children()
    {
        return $this->hasManyContent(Content::class, [
            'direction' => 'up',
            'relation' => ['parent-id' => null]
        ]);
    }

    /**
     * Expirmental method for getting relations
     *
     * @return hasManyContent
     */
    public function related()
    {
        return $this->hasManyContent(Content::class, [
            'order-by' => 'relation_type_id',
            'direction' => 'up',
            'relation' => true
        ]);
    }

    


    /**
     * Get relationship
     *
     * @deprecated 1.2.16 No longer used by internal code and not recommended.
     *
     * @param  string $type Type key
     * @return Content
     */
    public function getRelationship($type)
    {
        return Content::find($this->relatedBy($type)->pluck('relation_id')->all())->first();

        // foreach($this->relations()->get() as $relation) {
        //     if($relation->relation_type_id == content_id($type)) {
        //         return Content::find($relation->relation_id);
        //     }
        // }
    }


    /**
     * Remove a relation
     *
     * @param string $type Type of object relation we want to remove
     * @return void
     */
    public function removeRelation($type)
    {
        $relation = ContentRelation::where([
            'content_id' => $this->id,
            'relation_type_id' => content_id($type)
        ])->delete();
    }

    /**
     * Remove a relation by its type
     *
     * @deprecated 1.3.0 This feature is to be removed as we do not need to specify type to remove
     * @param string $type Type of object relation we want to remove
     * @return void
     */
    public function removeRelationByType(string $type)
    {
        $this->removeRelation($type);
    }

    /**
     * Remove a specific relation by relation_id
     * 
     * @deprecated 1.3.0 This feature is to be removed as we do not need to specify type to remove
     * @param int $type Type of object relation we want to remove
     * @return void
     */
    public function removeRelationById(int $id)
    {
        $this->removeRelation($type);
    }

    /**
     * Save the relations for this model
     *
     * @param array $relations List of relations
     * @return void
     */
    public function saveRelations(array $relations)
    {
        foreach ($relations as $key => $value) {
            $this->saveRelation($key, $value);
        }
    }

    /**
     * Save a single relation
     *
     * @param mixed $type    The content relation type
     * @param mixed $content The content we would like to relate to
     * @return void
     */
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

    /**
     * Check if is aliased
     *
     * @return boolean
     */
    public function isAliased()
    {
        return $this->aliased;
    }

    /**
     * Set the alias of the table selector
     *
     * @param string $alias    Table alias selector
     * @param boolean $aliased Flag indicating if the table should be treated as an alias
     * @return void
     */
    public function setAlias(string $alias, $aliased = false)
    {
        $this->aliased = $aliased;
        $this->alias = $alias;
    }

    /**
     * Get the current table selection
     *
     * @return string
     */
    public function getTable()
    {
        return ($this->alias ?: parent::getTable());
    }
}
