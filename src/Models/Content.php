<?php

namespace Baytek\Laravel\Content\Models;

use Baytek\Laravel\Content\Models\Scopes\TranslationScope;
use Baytek\LaravelStatusBit\Statusable;
use Baytek\LaravelStatusBit\Interfaces\StatusInterface;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use Cache;
use DB;

class Content extends Model implements StatusInterface
{
    use Concerns\HasMetadata,
        Concerns\HasAssociations,
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

        static::addGlobalScope('not_restricted', function (Builder $builder) {
            $context = property_exists($builder, 'selectContext') ? $builder->selectContext : $builder->getModel()->table;
            $builder->withStatus($context, ['exclude' => [self::RESTRICTED]]);
        });

        if(config('content.ordering', false)) {
            static::addGlobalScope('ordered', function (Builder $builder) {
                $prefix = DB::getTablePrefix();
                $context = property_exists($builder, 'selectContext') ? $builder->selectContext : $builder->getModel()->table;

                $builder->orderBy(DB::raw("IFNULL(`$prefix$context`.`order`, 4294967295 + 1), id"));
            });
        }

        if(\App::getLocale() != 'en') {
            static::addGlobalScope(new TranslationScope);
        }
    }

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
        return $this->association(Content::class);
    }




    public function scopeWithRestricted($query)
    {
        return $query->withoutGlobalScope('not_restricted');
    }

    public function scopeWithRestrictedMeta($query)
    {
        return $query->withoutGlobalScope('not_restricted_content_meta');
    }

    public function scopeWithMeta($query, $restricted = false)
    {
        return $query->with($restricted ? 'restrictedMeta' : 'meta');
    }

    public function scopeWithRelationships($query)
    {
        return $query->with(['relations', 'relations.relation', 'relations.relationType']);
    }

    public function scopeWithAll($query)
    {
        $query
            ->withContents()
            ->with(['relations', 'relations.relation', 'relations.relationType', 'meta']);
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
        foreach($relations as $key => $value) {
            $this->saveRelation($key, $value);
        }
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

    public function saveMetadata($key, $value = null)
    {
        if(is_string($key)) {
            $set = collect([$key => $value]);
        }
        else if(is_array($key)) {
            $set = collect($key);
        }
        else if(is_object($key) && $key instanceof Collection) {
            $set = $key;
        }

        $set->each(function ($value, $key) {
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
        });
    }


    public static function hierarchy($content, $paginate = true, $perPage = 15)
    {
        $request = request();
        $relations = Cache::get('content.cache.relations')->where('relation_type_id', 4);
        $items = Content::loopying($content, $relations, $content);

        $total = count($items);
        $perPage = $paginate ? $perPage : $total;
        $currentPage = Paginator::resolveCurrentPage();
        $pagination = new LengthAwarePaginator(
            array_slice($items, ($currentPage - 1) * $perPage, $perPage, true),
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        return $pagination;
    }


    public static function loopying(&$contents, $relations, &$all = -1, $depth = 0, &$used = [], &$result = [])
    {
        foreach($contents as $content) {
            if(!in_array($content->id, $used)) {
                // echo str_repeat('&mdash;', $depth) . " {$content->id} {$content->title}<br/>";

                $related = $relations->where('relation_id', $content->id)->pluck('content_id');
                $children = $all->only($related->all())->keyBy('id');
                array_push($used, $content->id);

                $content->depth = $depth;
                array_push($result, $content);

                static::loopying($children, $relations, $all, $depth + 1, $used, $result);
            }
        }

        return $result;
    }

}
