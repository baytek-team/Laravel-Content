<?php
{{namespace}}

use Baytek\Laravel\Content\Seeder;

class {{seederName}} extends Seeder
{
    // Live Seeder
    protected $live = [
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

    // Dev Seeder
    protected $dev = [

    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedStructure($this->live);

        // Seed the dev data if env not set to production
        if(!in_array(config('app.env'), ['prod', 'production', 'live'])) {
            $this->seedStructure($this->dev);
        }
    }
}
