<?php
namespace Baytek\Laravel\Content;

use Illuminate\Database\Seeder as IlluminateSeeder;
use Illuminate\Database\Eloquent\Model;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Models\ContentMeta;
use Baytek\Laravel\Content\Models\ContentRelation;

use Carbon\Carbon;
use DB;

abstract class Seeder extends IlluminateSeeder
{
    /**
     * Method used to seed database structure array.
     * @param  Array $databaseStructure Structure of the database
     * @return Null
     */
    protected function seedStructure($databaseStructure)
    {
        $relations = [];

        collect($databaseStructure)->each(function ($databaseItem) use (&$relations) {

            $databaseItemProperties = collect($databaseItem);

            $recordData = $databaseItemProperties->except(['meta', 'relations'])->all();

            // Create the new record
            $record = (new Content)->create($recordData);

            // Save the relationship data
            $this->seedMeta($record, $databaseItemProperties->get('meta'));

            $relations[$record->id] = $databaseItemProperties->get('relations');
        });

        foreach ($relations as $id => $relation) {
            $this->seedRelations($id, $relation);
        }

        return;
    }

    /**
     * Seed the metadata table with structured data array
     * @param  Content $content Content Model
     * @param  Array  $meta    Keyed array of metadata key value pairs
     * @return Null
     */
    protected function seedMeta($content, $meta = [])
    {
        if(is_null($meta)) {
            $meta = [];
        }

        if(!array_key_exists('author_id', $meta)) {
            $meta['author_id'] = 1;
        }

        foreach ($meta as $key => $value) {
            $metaRecord = (new ContentMeta(['language' => \App::getLocale(), 'key' => $key, 'value' => $value]));

            $content->meta()->save($metaRecord);

            $metaRecord->save();
        }

        return;
    }

    /**
     * Seed the content relations of a content
     * @param  Int $content_id Content ID that we wish to save relations for
     * @param  Array $relations  Relations array containing the relation ID and relation type ID
     * @return Null
     */
    protected function seedRelations($content_id, $relations)
    {
        // Loop through the sets of relations, first index is the relation type, the second index is the relation value
        collect($relations)->each(function ($relation) use ($content_id) {

            $relation_type_record = content_id($relation[0]);//Content::where('key', $relation[0])->first();
            $relation_record = content_id($relation[1]);

            if (!$relation_type_record || !$relation_record) {
                return false;
            }

            (new ContentRelation([
                'content_id'  => $content_id,
                'relation_type_id' => $relation_type_record,
                'relation_id' => $relation_record,
            ]))->save();
        });

        return;
    }
}
