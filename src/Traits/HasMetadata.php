<?php

namespace Baytek\Laravel\Content\Traits;

use Illuminate\Support\Str;

trait HasMetadata
{
    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->attributesToArray(), $this->relationsToArray(), $this->metaDataToArray());
    }

    /**
     * Get the model's relationships in array form.
     *
     * @return array
     */
    public function metaDataToArray()
    {
        $attributes = []; //parent::relationsToArray();

        foreach ($this->getArrayableRelations() as $key => $value) {
            if ($key == 'meta') {
                if (!isset($attributes['metadata'])) {
                    $attributes['metadata'] = [];
                }

                $value->each(function ($metadata) use (&$attributes) {
                    $attributes['metadata'][str_replace('-', '_', $metadata->key)] = $metadata->value;
                });
            }

            if ($key == 'relations') {
                if (!isset($attributes['related'])) {
                    $attributes['relationships'] = [];
                }

                foreach ($value as $relation) {
                    $newKey = str_replace('-', '_', $relation->relations['relationType']->key);

                    if (!isset($attributes['relationships'][$newKey])) {
                        $attributes['relationships'][$newKey] = [];
                    }

                    if ($newKey == str_plural($newKey)) {
                        $attributes['relationships'][$newKey][] = $relation->relations['relation']->key;
                    } else {
                        if (!is_array($attributes['relationships'][$newKey])) {
                            // $attributes['relationships'][$newKey] = [$attributes['relationships'][$newKey]];
                            throw new \Exception('Content relationship is not plural, but has many relations.');
                        }

                        $attributes['relationships'][$newKey] = $relation->relations['relation']->key;
                    }
                }
            }
        }

        foreach ($this->getMetadataAttributes() as $key) {
            $attributes['metadata'][$key] = $this->populateMetadataAttribute(
                $key, array_key_exists($key, $attributes['metadata']) ? $attributes['metadata'][$key] : null
            );
        }

        return $attributes;
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
