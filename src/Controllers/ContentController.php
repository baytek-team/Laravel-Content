<?php

namespace Baytek\Laravel\Content\Controllers;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Models\ContentMeta;
use Baytek\Laravel\Content\Models\ContentRelation;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use ReflectionClass;
use View;

class ContentController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $model = Content::class;
    protected $instance;
    protected $type;
    protected $redirects = true;
    protected $names = [
        'singular' => '',
        'plural' => '',
        'class' => '',
    ];

    protected $viewData = [
        'index' => [],
        'create' => [],
        'edit' => [],
        'show' => [],
    ];

    protected $views = [
        'index' => 'content.index',
        'create' => 'content.create',
        'edit' => 'content.edit',
        'show' => 'content.show',
    ];

    public function __construct()
    {
        $this->instance = new $this->model;
        $this->names['class'] = (new ReflectionClass($this->instance))->getShortName();
        $this->names['singular'] = strtolower(str_singular($this->names['class']));
        $this->names['plural'] = strtolower(str_plural($this->names['class']));
    }

    protected function view($name)
    {
        return implode('::', [$this->names['class'], $this->views[$name]]);
    }

    protected function bound($id)
    {
        if(!is_string($id) && get_class($id) == $this->model) {
            return $id;
        }

        return $this->instance->find($id);
    }

    protected function params(Array $params, Array $additional)
    {
        return collect($params)->merge($additional)->all();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = $this->instance;
        $view = $this->view('index');
        $content = $model->with($model::$eager)->get();

        if(!View::exists($view)) {
            return $content;
        }

        return View::make($view, $this->params([
            $this->names['plural'] => $content
        ], $this->viewData[__FUNCTION__]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $model = $this->instance;

        return View::make($this->view('create'), $this->params([
            $this->names['plural'] => $model::select('id', 'status', 'revision', 'language', 'title')->get(),
            $this->names['singular'] => $model,
            'relationTypes' => $model::childrenOf('relation-type')->get(),
        ], $this->viewData[__FUNCTION__]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $content = new $this->model($request->all());

        $content->save();

        $this->saveMetaData($content, $request);
        $this->saveRelationships($content, $request);

        foreach($content->relationships as $contentType => $type) {
            // $typeID = (is_object($t) && ($t instanceof Closure)) ? $t($request) : $t;
            // Lookup the type id
            $typeID = $content::where('key', $type)->first()->id;

            // Save the actual relationship ID
            $content->saveRelation($contentType, $typeID);
        }

        if($this->redirects) {
            return redirect(route($this->names['singular'].'.show', $content));
        }

        return $content;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = $this->instance;
        $view = $this->view('show');
        $content = $this->bound($id)->load($model::$eager);

        if(!View::exists($view)) {
            return $content;
        }

        return View::make($view, $this->params([
            $this->names['singular'] => $content
        ], $this->viewData[__FUNCTION__]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = $this->instance;

        return View::make($this->view('edit'), $this->params([
            $this->names['plural'] => $model::select('id', 'status', 'revision', 'language', 'title')->get(),
            $this->names['singular'] => $this->bound($id),
            'relationTypes' => $model::childrenOf('relation-type')->get(),
        ], $this->viewData[__FUNCTION__]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $content = $this->bound($id);

        // Update the content
        $content->update($request->all());

        // Save the content
        $content->save();

        $this->saveMetaData($content, $request);
        $this->saveRelationships($content, $request);

        if($this->redirects) {
            return redirect(route($this->names['singular'].'.show', $content));
        }

        return $content;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->bound($id)->delete();

        return redirect(route($this->names['singular'].'.index', $content));
    }


    private function saveMetaData(Content $content, Request $request)
    {
        if(!$request->meta_key) return;

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


    private function saveRelationships(Content $content, Request $request)
    {
        if(!$request->relation_ids) return;

        // Get the ids of the meta that was present on the page when the form was loaded
        $resourceIds = json_decode($request->relation_ids) ?: [];

        foreach($request->content_id as $id => $content_id) {

            if(in_array($id, $resourceIds) && $relationRecord = ContentRelation::where('id', $id)) {
                $relationRecord
                    ->update([
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