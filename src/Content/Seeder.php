<?php
namespace Baytek\Laravel\Content;

use App;
use DB;

use Illuminate\Database\Seeder as IlluminateSeeder;
use Illuminate\Database\Eloquent\Model;

use Baytek\Laravel\Content;
use Baytek\Laravel\Content\Meta;
use Baytek\Laravel\Content\Relation;

use Carbon\Carbon;

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
            $metaRecord = (new Meta(['language' => App::getLocale(), 'key' => $key, 'value' => $value]));

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

            $relation_type_record = content($relation[0]);//Content::where('key', $relation[0])->first();
            $relation_record = content($relation[1]);

            if (!$relation_type_record || !$relation_record) {
                return false;
            }

            (new Relation([
                'content_id'  => $content_id,
                'relation_type_id' => $relation_type_record->id,
                'relation_id' => $relation_record->id,
            ]))->save();
        });

        return;
    }
}