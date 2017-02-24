<?php
namespace Baytek\Laravel\Content\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use DB;

class ContentSeeder extends Seeder
{
    private $data = [
        [
            'key' => 'root',
            'title' => 'Root',
            'content' => '',
        ],
        [
            'key' => 'content-type',
            'title' => 'Content Type',
            'content' => '',
        ],
        [
            'key' => 'relation-type',
            'title' => 'Relation Type',
            'content' => '',
        ],
        [
            'key' => 'parent-id',
            'title' => 'Parent ID',
            'content' => '',
        ],
        [
            'key' => 'webpage',
            'title' => 'Webpage',
            'content' => 'The webpage content type',
        ],
        [
            'key' => 'homepage',
            'title' => 'Homepage',
            'content' => 'First page, some content should be added here.',
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check to see if the content has already been inserted, do not run if content exists.
        if(false) {
            collect($this->data)->each(function($data) {
                DB::table('content')->insert([
                    'key' => $data['key'],
                    'title' => $data['title'],
                    'content' => $data['content'],
                ]);
            });
        }
    }
}