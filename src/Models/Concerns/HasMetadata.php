<?php

namespace Baytek\Laravel\Content\Models\Concerns;

use Baytek\Laravel\Content\Models\ContentMeta;

use Illuminate\Support\Str;

trait HasMetadata
{
    protected $metadata = [];

    /**
     * Metadata Cache
     *
     * @var array
     * @static
     * @access protected
     */
    protected static $metadataCache = [];

    /**
     * Custom cache
     *
     * @var array
     * @access protected
     */
    protected $customCache = [];

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->attributesToArray(), $this->metaDataToArray(), $this->relationsToArray());
    }

    /**
     * Get and cache metadata
     *
     * @param  array|null $key Optional key used to return a specific metadata
     * @return Illuminate\Database\Eloquent\Collection|null
     */
    public function metadata($key = null)
    {
        if (empty($this->customCache) || !array_key_exists('metadata', $this->customCache)) {
            $this->customCache = array_merge($this->customCache, $this->populateMetadata());
        }

        if (!array_key_exists('metadata', $this->customCache)) {
            return;
        }

        if (!is_null($key)) {
            if (!array_key_exists($key, $this->customCache['metadata'])) {
                return;
            }

            return $this->customCache['metadata'][$key];
        }

        return collect($this->customCache['metadata']);
    }

    /**
     * Get and cache relationships
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function relationships()
    {
        if (empty($this->customCache) || !array_key_exists('relationships', $this->customCache)) {
            $this->customCache = array_merge($this->customCache, $this->populateCustomRelationships());
        }

        return collect(
            array_key_exists('relationships', $this->customCache)
            ? $this->customCache['relationships']
            : []
        );
    }

    /**
     * Return metadata relations in array form.
     *
     * @return array
     */
    public function populateMetadata()
    {
        $attributes = [];

        foreach ($this->getArrayableRelations() as $key => $value) {
            if ($key == 'meta' || $key == 'restrictedMeta') {
                if (!isset($attributes['metadata'])) {
                    $attributes['metadata'] = [];
                }

                $value->each(function ($metadata) use (&$attributes) {
                    if (!array_key_exists(str_replace('-', '_', $metadata->key), $attributes['metadata'])) {
                        $attributes['metadata'][str_replace('-', '_', $metadata->key)] = $metadata->value;
                    }
                });
            }
        }

        if (isset($attributes['metadata'])) {
            foreach ($this->getMetadataAttributes() as $key) {
                $attributes['metadata'][$key] = $this->populateMetadataAttribute(
                    $key,
                    array_key_exists(
                        $key,
                        $attributes['metadata']
                    ) ? $attributes['metadata'][$key] : null
                );
            }
        }

        if (empty($attributes) && !empty($this->metadataAttributes)) {
            foreach ($this->metadataAttributes as $key => $value) {
                $attributes['metadata'][$key] = $value;
            }
        }

        unset($this->relations['meta']);
        unset($this->relations['restrictedMeta']);

        return $attributes;
    }

    /**
     * Return relationship relations in array form.
     *
     * @return array
     */
    public function populateCustomRelationships()
    {
        $attributes = [];

        foreach ($this->getArrayableRelations() as $key => $value) {

            if ($key == 'relations') {
                if (!isset($attributes['related'])) {
                    $attributes['relationships'] = [];
                }

                foreach ($value as $relation) {
                    if (!$relation->relations['relationType']) {
                        continue;
                    }

                    $newKey = str_replace('-', '_', $relation->relations['relationType']->key);

                    if (!isset($attributes['relationships'][$newKey])) {
                        $attributes['relationships'][$newKey] = [];
                    }

                    if ($newKey == str_plural($newKey)) {
                        $attributes['relationships'][$newKey][] = $relation->relations['relation']->key;
                    } else {
                        if (!is_array($attributes['relationships'][$newKey])) {
                            $attributes['relationships'][$newKey] = [$attributes['relationships'][$newKey]];
                            // throw new \Exception('Content relationship is not plural, but has many relations.');
                        }

                        $attributes['relationships'][$newKey] = $relation->relations['relation']->key;
                    }
                }
            }
        }

        return $attributes;
    }


    /**
     * Get the model's relationships in array form.
     *
     * @return array
     */
    public function metaDataToArray()
    {
        if (empty($this->customCache) || !array_key_exists('metadata', $this->customCache)) {
            $this->customCache = array_merge($this->customCache, $this->populateMetadata());
        }

        if (empty($this->customCache) || !array_key_exists('relations', $this->customCache)) {
            $this->customCache = array_merge($this->customCache, $this->populateCustomRelationships());
        }

        return $this->customCache;
    }

    /**
     * Get the value of an attribute using its mutator.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function populateMetadataAttribute($key, $value = null)
    {
        return $this->{'set'.Str::studly($key).'Metadata'}($value);
    }

    /**
     * Get the mutated attributes for a given instance.
     *
     * @return array
     */
    public function getMetadataAttributes()
    {
        $class = static::class;

        if (!isset(static::$metadataCache[$class])) {
            static::cacheMetadataAttributes($class);
        }

        return static::$metadataCache[$class];
    }

    /**
     * Extract and cache all the mutated attributes of a class.
     *
     * @param string $class
     */
    public static function cacheMetadataAttributes($class)
    {
        static::$metadataCache[$class] = collect(static::getMetadataMethods($class))->map(function ($match) {
            return lcfirst(static::$snakeAttributes ? Str::snake($match) : $match);
        })->all();
    }

    /**
     * Get all of the attribute mutator methods.
     *
     * @param mixed $class
     *
     * @return array
     */
    public static function getMetadataMethods($class)
    {
        preg_match_all('/(?<=^|;)set([^;]+?)Metadata(;|$)/', implode(';', get_class_methods($class)), $matches);

        return $matches[1];
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array  $attributes
     * @return $this
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes)
    {
        $totallyGuarded = $this->totallyGuarded();

        foreach ($attributes as $key => $value) {
            $key = $this->removeTableFromKey($key);

            // The developers may choose to place some attributes in the "fillable" array
            // which means only those attributes may be set through mass assignment to
            // the model, and all others will just get ignored for security reasons.
            if (in_array($key, $this->metadata)) {
                $this->metadataAttributes[$key] = $value;
            } elseif ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            } elseif ($totallyGuarded) {
                throw new MassAssignmentException($key);
            }
        }

        return $this;
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $result = parent::save($options);

        if (count($this->metadataAttributes)) {
            $this->saveMetadata($this->metadataAttributes);
        }

        // Check to see if there are any relationships required to save
        if (property_exists($this, 'relationships')) {
            $this->saveRelations($this->relationships);
        }

        return $result;
    }

    /**
     * Update the model in the database.
     *
     * @param  array  $attributes
     * @param  array  $options
     * @return bool
     */
    public function update(array $attributes = [], array $options = [])
    {
        if (! $this->exists) {
            return false;
        }

        $this->fill($attributes);

        if (count($this->metadataAttributes)) {
            $this->saveMetadata($this->metadataAttributes);
        }

        // Check to see if there are any relationships required to save
        if (property_exists($this, 'relationships')) {
            $this->saveRelations($this->relationships);
        }

        return $this->save($options);
    }

     /**
     * Meta relationship
     * @return   Meta relationship
     */
    public function meta()
    {
        return $this->hasMany(ContentMeta::class, 'content_id', 'id');
    }

    /**
     * Meta relationship
     * @return   Meta relationship
     */
    public function restrictedMeta()
    {
        return $this->hasMany(ContentMeta::class, 'content_id')->withoutGlobalScope('not_restricted');
    }


    public function getMetadataKeys()
    {
        return property_exists($this, 'metadata') && isset($this->metadata) ? $this->metadata : [];
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
}
