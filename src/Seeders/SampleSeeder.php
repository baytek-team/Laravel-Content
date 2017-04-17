<?php
{{namespace}}

use Baytek\Laravel\Content\Seeder;

class {{seederName}} extends Seeder
{
    protected $data = [
        [
            // Key of content
            'key' => '',
            // The title of the content
            'title' => '',
            // The actual content
            'content' => '',
            // Key value pairs
            'meta' => [
                'sample-key' => 'sample-value',
            ],
            // Arrays with content keys containing two values per array
            'relations' => [
                ['sample-key', 'sample-key-type'],
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
