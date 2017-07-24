<?php

use Baytek\Laravel\Content\Models\Content;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

// use Illuminate\Support\Arr;
// use Illuminate\Support\Str;
// use Illuminate\Support\Collection;
// use Illuminate\Support\Debug\Dumper;
// use Illuminate\Contracts\Support\Htmlable;
// use Illuminate\Support\HigherOrderTapProxy;

if (! function_exists('content')) {
    /**
     * Assign high numeric IDs to a config item to force appending.
     *
     * @param  string  $key String we want to get the key for
     * @return int
     */
    function content($value, $hydrate = true, $model = Baytek\Laravel\Content\Models\Content::class)
    {
        if(is_numeric($value)) {
            $id = $value;
        }
        else if(is_string($value)) {
            if(stripos($value, '/') === false) {
                $id = content_id($value);
            }
            else {
                if($hydrate) {
                    return (new $model)->getWithPath($value)->first();
                }
                else {
                    return content_id($value);
                }
            }
        }
        // else if(is_array($value)) {
        //     $first = array_first($value);

        //     if(is_numeric($first)) {
        //         $query->whereIn('contents.id', $value);
        //     }
        //     else if(is_string($first)) {
        //         $query->whereIn('contents.key', $value);
        //     }
        //     else {
        //         throw new Exception('Passed array but value type is not supported');
        //     }
        // }
        // else if(is_object($value) && $value instanceof Collection) {
        //     //$query->whereIn('contents.id', $value->pluck('id'));
        // }
        else if(is_object($value) && $value instanceof Model) {
            return ($hydrate) ? $value : $value->id;
        }
        else {
            $id = null;
        }


        return ($hydrate) ? Content::find($id) : $id;
    }
}


if (! function_exists('contents')) {
    /**
     * Assign high numeric IDs to a config item to force appending.
     *
     * @param  string  $key String we want to get the key for
     * @return int
     */
    function contents($value, $model = Baytek\Laravel\Content\Models\Content::class)
    {
        $result = null;

        if(is_numeric($value)) {
            $result = $model::find($id);
        }
        else if(is_string($value)) {
            if(stripos($value, '/') === false) {
                $result = $model::find(content_id($value));
            }
            else {
                return (new $model)->childrenOfType((new $model)->getWithPath($value))->get();
            }
        }
        // else if(is_array($value)) {
        //     $first = array_first($value);

        //     if(is_numeric($first)) {
        //         result = (new $model)->whereIn('contents.id', $value);
        //     }
        //     else if(is_string($first)) {
        //         result = (new $model)->whereIn('contents.key', $value);
        //     }
        //     else {
        //         throw new Exception('Passed array but value type is not supported');
        //     }
        // }
        // else if(is_object($value) && $value instanceof Collection) {
        //     result = (new $model)->whereIn('contents.id', $value->pluck('id'));
        // }
        // else if(is_object($value) && $value instanceof Model) {
        //     result = $value;
        // }
        // else {
        //     throw new Exception('Passed arguments but got confused');
        // }


        return $result;
    }
}

if (! function_exists('content_id')) {
    /**
     * Assign high numeric IDs to a config item to force appending.
     *
     * @param  string  $key String we want to get the key for
     * @return int
     */
    function content_id($key)
    {
        if(stripos($key, '/') === false) {
            return (new Content)->getContentIdByKey($key);
        }
        else {
            return (new Content)->getWithPath($key)->first()->id;
        }
    }
}

if (! function_exists('getChildrenAndDelete')) {
    /**
     * Recursively delete a piece of content and its descendants
     *
     * @param  mixed  $item  Any content model, but usually a Webpage, Folder or File
     */
    function getChildrenAndDelete($item)
    {
        //Delete items and their contents, but status bit other content types
        if ($item->relationships()->get('content_type') == 'file') {
            //Set the status to deleted, even though we are also doing a laravel delete
            $item->offBit(Content::APPROVED)->onBit(Content::DELETED)->update();

            //\Storage::delete($file->content);
            \Storage::delete($item->getMeta('file'));
            $item->delete();
        }
        else {
            $children = Content::childrenOf($item->id)->withoutGlobalScopes()->withRelationships()->get();

            if ($children->isNotEmpty()) {
                foreach ($children as $child) {
                    getChildrenAndDelete($child);
                }
            }

            $item->offBit(Content::APPROVED)->onBit(Content::DELETED)->update();
        }
    }
}