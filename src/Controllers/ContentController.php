<?php

namespace Baytek\LaravelContent\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


use Baytek\LaravelContent\Models\Content;
use Baytek\LaravelContent\Models\ContentMeta;
use Baytek\LaravelContent\Models\ContentRelation;

use Illuminate\Http\Request;

class ContentController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Content::with(Content::$eager)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Pretzel::content.create', [
            'contents' => Content::select('id', 'status', 'revision', 'language', 'title')->get(),
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

        $this->saveMetaData($content, $request);
        $this->saveResources($content, $request);

        return redirect(action('\\'.self::class.'@show', $content));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Content $content)
    {
        return $content->load(Content::$eager);
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
            'contents' => Content::select('id', 'status', 'revision', 'language', 'title')->get(),
            'relationTypes' => Content::childrenOf('relation-type')->get(),
            'content' => $content,
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
        // Update the content
        $content->update($request->all());

        // Save the content
        $content->save();

        $this->saveMetaData($content, $request);
        $this->saveResources($content, $request);

        return redirect(action('\\'.self::class.'@show', $content));
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


    private function saveMetaData(Content $content, Request $request)
    {
        // Get the ids of the meta that was present on the page when the form was loaded
        $metaIds = json_decode($request->meta_ids) ?: [];

        foreach($request->meta_key as $id => $key) {

            if(!empty($key) && !empty($request->meta_value[$id])) {

                if(in_array($id, $metaIds) && $metaRecord = ContentMeta::where('id', $id)) {
                    $metaRecord
                        ->update([
                            'key'   => $key,
                            'value' => $request->meta_value[$id]
                        ]);

                    unset($metaIds[array_search($id, $metaIds)]);
                }
                else {
                    $metaRecord = (new ContentMeta([
                        // 'content_id' => $content->id,
                        'key' => $key,
                        'value' => $request->meta_value[$id]
                    ]));
                    $content->meta()->save($metaRecord);
                    $metaRecord->save();
                }
            }
        }

        ContentMeta::destroy($metaIds);
    }


    private function saveResources(Content $content, Request $request)
    {
        // Get the ids of the meta that was present on the page when the form was loaded
        $resourceIds = json_decode($request->relation_ids) ?: [];

        foreach($request->content_id as $id => $content_id) {

            if(in_array($id, $resourceIds) && $relationRecord = ContentRelation::where('id', $id)) {
                $relationRecord
                    ->update([
                        // 'content_id'  => $content_id,
                        'relation_id' => $request->relation_id[$id],
                        'relation_type_id' => $request->relation_type_id[$id],
                    ]);

                unset($resourceIds[array_search($id, $resourceIds)]);
            }
            else {
                if(!empty($content_id) && !empty($request->relation_id[$id]) && !empty($request->relation_type_id[$id])) {
                    (new ContentRelation([
                        'content_id'  => $content->id,
                        'relation_id' => $request->relation_id[$id],
                        'relation_type_id' => $request->relation_type_id[$id],
                    ]))->save();
                }
            }
        }

        ContentRelation::destroy($resourceIds);
    }
}