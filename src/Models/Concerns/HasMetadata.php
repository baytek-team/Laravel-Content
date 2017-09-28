<?php

namespace Baytek\Laravel\Content\Models\Concerns;

use Illuminate\Support\Str;

trait HasMetadata
{
    protected static $metadataCache = [];
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
        if(empty($this->customCache) || !array_key_exists('metadata', $this->customCache)) {
            $this->customCache = $this->populateMetadata();
        }

        if(!array_key_exists('metadata', $this->customCache)) {
            return;
        }

        if(!is_null($key)) {
            if(!array_key_exists($key, $this->customCache['metadata'])) {
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
        if(empty($this->customCache) || !array_key_exists('relationships', $this->customCache)) {
            $this->customCache = $this->populateCustomRelationships();
        }

        return collect($this->customCache['relationships']);
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
                    if(!array_key_exists(str_replace('-', '_', $metadata->key), $attributes['metadata'])) {
                        $attributes['metadata'][str_replace('-', '_', $metadata->key)] = $metadata->value;
                    }
                });
            }
        }

        if (isset($attributes['metadata'])) {
            foreach ($this->getMetadataAttributes() as $key) {
                $attributes['metadata'][$key] = $this->populateMetadataAttribute(
                    $key, array_key_exists($key, $attributes['metadata']) ? $attributes['metadata'][$key] : null
                );
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
                    if(!$relation->relations['relationType'])
                        continue;

                    $newKey = str_replace('-', '_', $relation->relations['relationType']->key);

                    if (!isset($attributes['relationships'][$newKey])) {
                        $attributes['relationships'][$newKey] = [];
                    }

                    if ($newKey == str_plural($newKey)) {
                        $attributes['relationships'][$newKey][] = $relation->relations['relation']->key;
                    }
                    else {
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
        if(empty($this->customCache) || !array_key_exists('metadata', $this->customCache)) {
            $this->customCache = array_merge($this->customCache, $this->populateMetadata());
        }

        if(empty($this->customCache) || !array_key_exists('relations', $this->customCache)) {
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


}
