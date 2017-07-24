<?php
namespace Baytek\Laravel\Content\Seeders;

use Baytek\Laravel\Content\Seeder;

class ContentSeeder extends Seeder
{
    protected $data = [
        [
            'key' => 'root',
            'title' => 'Root',
            'content' => '',
        ],
        [
            'key' => 'relation-type',
            'title' => 'Relation Type',
            'content' => '',
            'relations' => [
                ['content-type', 'relation-type'],
            ]
        ],
        [
            'key' => 'content-type',
            'title' => 'Content Type',
            'content' => '',
            'relations' => [
                ['parent-id', 'relation-type'],
                ['content-type', 'relation-type'],
            ]
        ],
        [
            'key' => 'parent-id',
            'title' => 'Parent ID',
            'content' => '',
            'relations' => [
                ['parent-id', 'relation-type'],
                ['content-type', 'relation-type'],
            ]
        ],
        [
            'key' => 'translations',
            'title' => 'Translations',
            'content' => '',
            'relations' => [
                ['parent-id', 'relation-type'],
                ['content-type', 'relation-type'],
            ]
        ],
        /**
         * Category relationship type
         */
        [
            'key' => 'category',
            'title' => 'Category',
            'content' => 'Relationship type for categories',
            'relations' => [
                ['parent-id', 'relation-type'],
                ['content-type', 'relation-type'],
            ]
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedStructure($this->data);
    }
}
