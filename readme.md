# Baytek Laravel Content
[![Laravel](https://img.shields.io/badge/Laravel-~5.3-orange.svg?style=flat-square)](http://laravel.com)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)
[![PHP](https://img.shields.io/badge/PHP-%3E=5.6.4-green.svg)](http://www.php.net/ChangeLog-5.php#5.6.4)

## Installation

#### Composer

Currently this project is not publicly available. You must add a repository object in your composer.json file. You must also have SSH keys setup.

```javascript
"repositories": [
    {
        "type": "git",
        "url": "ssh://sls@slsapp.com:1234/baytek/laravel-content.git"
    }
],
```

Add the following `require` to your composer.json file:

```php
"baytek/laravel-content": "dev-master"
```

Lastly run:

`composer update`

## Seeding

`php artisan db:seed --class=Baytek\Laravel\Content\Seeds\ContentSeeder`

### Content Seeders
To generate a new content seeder that will be placed into the `database/seeds` directory. Simply run:

`php artisan make:content-seeder SeederName`

### Sample data seeder structure
There are five pieces of content that can be seeded using the content seeder method. They are `key`, `title`, `content`, `meta` and `relations`.

`key`, `title` and `content` expect strings whereas `meta` and `relations` expect arrays.

`meta` is a key value pair array single dimension.

`relations` is a two dimensional array with no keys.

```php
protected $data = [
    [
        'key' => '', // Key of content
        'title' => '', // The title of the content
        'content' => '', // The actual content
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
```

## Artisan Commands
To generate content cache run:

`php artisan content:cache`

To seed the database with random data (default 1000 records)

`php artisan content:random-seed 1000`

## Configuration

Currently there is no real configuration other than configurations that are exposed using the `laravel-settings` package.

Soon views and generic configurations will be added. Details will be added here.


## Content Types

## Issues

## Licence

Copyright (c) 2017 Yvon Viger <yvon@baytek.ca>, Baytek

MIT License

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.