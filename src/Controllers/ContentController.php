<?php

namespace Baytek\LaravelContent\Controllers;

use Baytek\LaravelContent\Models\Content;
use Baytek\LaravelContent\Models\ContentMeta;
use Baytek\LaravelContent\Models\ContentRelation;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Faker\Generator;

class ContentController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $views = [
    	__DIR__.'/Views' => [
    		'index' => 'index',
    		'create' => 'create',
    		'store' => 'store',
    	]
    ];

    public function __construct() {
        $this->authorizeResource(Content::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Content::with(['meta', 'relations'])->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Generator $faker)
    {
        // //https://support.google.com/webmasters/answer/189077?hl=en
        // $content = new Content;
        // // LANG + ISO 3166-1 code
        // $content->language = 'en-CA'; //https://tools.ietf.org/html/rfc5646
        // $content->title = $faker->sentence();
        // $content->content = $faker->paragraph();
        // $content->save();

        return view('Pretzel::content.create', [
            'content' => (new Content)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $content = new Content($request->all());

        $content->save();

        return redirect(action('\Baytek\LaravelContent\Controllers\ContentController@index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Content $content)
    {

        $content->load('meta', 'relations', 'relations.content', 'relations.relation', 'relations.relationType');

        return $content;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Content $content)
    {
        return view('Pretzel::content.edit', [
            'content' => $content
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Content $content)
    {
        // dd($request->all());

        $meta = $request->only(['id', 'key', 'value']);

        foreach($meta['id'] as $i => $id) {
            ContentMeta::where('id', $id)
                ->update([
                    'key' => $meta['key'][$i],
                    'value' => $meta['value'][$i]
                ]);
        }

        // collect($meta['key'])->combine($meta['value'])->each(function($item){
        //     dump($item);
        // });

        $content->update($request->all());

        $content->save();

        return redirect(action('\Baytek\LaravelContent\Controllers\ContentController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Content $content)
    {
        //
    }
}