<?php
namespace Baytek\Laravel\Content\Seeds;

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
        ],
        [
            'key' => 'content-type',
            'title' => 'Content Type',
            'content' => '',
            'relations' => [
                ['parent-id', 'relation-type']
            ]
        ],
        [
            'key' => 'parent-id',
            'title' => 'Parent ID',
            'content' => '',
            'relations' => [
                ['parent-id', 'relation-type']
            ]
        ]
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
