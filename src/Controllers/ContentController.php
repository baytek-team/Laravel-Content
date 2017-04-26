<?php

namespace Baytek\Laravel\Content\Controllers;

use Baytek\Laravel\Content\Events\ContentEvent;
use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Models\ContentMeta;
use Baytek\Laravel\Content\Models\ContentRelation;
use Baytek\Laravel\Settings\SettingsProvider;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App;
use Auth;
use DB;
use ReflectionClass;
use Route;
use View;

/**
 * The Content Controller is suppose to act as an abstract class that facilitates
 * rendering and saving of common resource tables.
 *
 * There are three primary models used for all content types:
 *     Content
 *     ContentMeta
 *     ContentRelations
 *
 * Due to this commonality, it makes sense to have a super class which can handle all
 * data storage and relegate all content specific stuff to the sub classes.
 */
class ContentController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * The data model that will be used to load resources
     * @var Baytek\Laravel\Content\Models\Content
     */
    protected $model = Content::class;

    /**
     * Table where history will be dumped upon update
     * @var string
     */
    protected $historyTable = 'content_history';

    protected $viewPrefix = '';

    // Reference to the current model instance
    protected $instance;

    // Reference to the current model instance
    protected $isTranslation = false;

    // I have no clue what this is for, This should be removed if not required
    protected $type;

    /**
     * Flag that defines whether we should redirect after saving
     *
     * @var boolean
     */
    public $redirects = true;

    /**
     * List of names that the class needs to use
     * PS, I don't really like this, perhaps add a bit of abstraction here to clean this up
     *
     * @var [type]
     */
    protected $names = [
        'singular' => '',
        'plural' => '',
        'class' => '',
    ];

    /**
     * List of view data variables that will be passed along to the views at render time
     *
     * @var Array
     */
    protected $viewData = [
        'index' => [],
        'create' => [],
        'edit' => [],
        'show' => [],
    ];

    /**
     * List of views used to display the content
     *
     * @var Array
     */
    protected $views = [
        'index' => 'content.index',
        'create' => 'content.create',
        'edit' => 'content.edit',
        'show' => 'content.show',
    ];

    protected $redirectsKey;

    /**
     * Controller instantiation:
     * Injection of the SettingsProvider this will load all required settings
     *
     * We also set locally the list of names used within the controller
     *
     * @param SettingsProvider $settings Automatically provides the required settings
     */
    public function __construct(/*SettingsProvider $settings*/)
    {
        $current = Route::current();
        $action = $current->getAction();

        if(!is_null($current) && (collect($current->parameterNames)->first() == 'translation' || (isset($action['as']) && stripos($action['as'], 'translation') === 0))) {
            $this->isTranslation = true;

            $this->views = [
                'index' => 'translate.index',
                'create' => 'translate.create',
                'edit' => 'translate.edit',
                'show' => 'translate.show',
            ];

            $this->redirectsKey = 'admin';
        }

        $this->instance = new $this->model;
        $this->names['class'] = (new ReflectionClass($this->instance))->getShortName();
        $this->names['singular'] = strtolower(str_singular($this->names['class']));
        $this->names['plural'] = strtolower(str_plural($this->names['class']));

        // $settings->resolve(strtolower($this->names['class']));
    }

    /**
     * Set the controllers default model
     * @param Mixed $model model to return
     */
    public function setmodel($model)
    {
        $this->model = $model;

        return $this->model;
    }

    /**
     * Register a view file namespace.
     *
     * Illuminate\Support\ServiceProvider
     *
     * @param  string  $path
     * @param  string  $namespace
     * @return void
     */
    protected function loadViewsFrom($path, $namespace)
    {
        if(is_dir($appPath = app()->resourcePath().'/views/vendor/'.$namespace)) {
            app()['view']->addNamespace($namespace, $appPath);
        }

        app()['view']->addNamespace($namespace, $path);
    }

    /**
     * Internal method to output the view name to render
     *
     * @param  String $name The method type key to lookup the view
     * @return String       The result of the current class and the view
     */
    protected function view($name)
    {
        $view = false;
        if(View::exists($view = implode('::', [($this->names['class']), $this->views[$name]]))) {
            $view = $view;
        }
        else if(View::exists($view = implode('/', array_filter([$this->viewPrefix, strtolower($this->names['class']), $this->views[$name]]) ) ) )  {
            $view = $view;
        }
        else if(View::exists($view = implode('/', [strtolower($this->names['class']), $this->views[$name]]))) {
            $view = $view;
        }

        return $view;
    }

    /**
     * Internal method used to return an instance of the loaded model class
     *
     * @param  Mixed $id Either the ID or Model
     * @return Model     Returns a laravel model instance
     */
    protected function bound($contentID)
    {
        if (!is_string($contentID) && get_class($contentID) == $this->model) {
            return $contentID;
        }

        return $this->instance->withoutGlobalScopes()->find($contentID);
    }

    /**
     * Internal method used to concatenate parameters being passed to the views
     *
     * @param  Array  $params     List of default parameters
     * @param  Array  $additional List of additional parameters
     * @return Collection         Concatenated list of parameters
     */
    protected function params(Array $params, Array $additional)
    {
        return collect($params)->merge($additional)->all();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function contentIndex()
    {
        $this->authorize('view', $this->model);

        $model = $this->instance;

        if (!$view = $this->view('index')) {
            return 'Content view should be used.';
            // return $model->with($model::$eager)->get();
        }

        return View::make($view, $this->params([
            $this->names['plural'] => $model->with($model::$eager)->paginate(20)
        ], $this->viewData['index']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function contentCreate()
    {
        $this->authorize('create', $this->model);

        $model = $this->instance;

        return View::make($this->view('create'), $this->params([
            // This needs to be updated as it returns everything in the content table, this should return the list of objects of that type
            $this->names['plural'] => $model::select('id', 'status', 'revision', 'language', 'title')->get(),
            // Create a blank instance of our model used for the view
            $this->names['singular'] => $model,
            // Get the relationship types
            'relationTypes' => Content::childrenOf('relation-type')->get(),
        ], $this->viewData['create']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function contentStore(Request $request)
    {
        $this->authorize('create', $this->model);

        $request->merge(['language' => App::getLocale()]);

        $content = new $this->model($request->all());
        $content->save();

        $content->saveMetadata('author_id', Auth::user()->id);

        $this->saveMetaData($content, $request);
        $this->saveRelationships($content, $request);

        foreach ($content->relationships as $contentType => $type) {
            // $typeID = (is_object($t) && ($t instanceof Closure)) ? $t($request) : $t;
            // Lookup the type id
            $typeID = $content::withoutGlobalScopes()->where('contents.key', $type)->first()->id;

            // Save the actual relationship ID
            $content->saveRelation($contentType, $typeID);
        }

        event(new ContentEvent($content));

        if($this->redirects) {
            return redirect(route(($this->redirectsKey ?: $this->names['singular']).'.index', $content));
        }

        return $content;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $contentID
     * @return \Illuminate\Http\Response
     */
    public function contentShow($id)
    {
        if(is_null($id)) {
            abort(404);
        }

        $model = $this->instance;
        $view = $this->view('show');

        // Eager load the subset models, meta data and relationships
        $content = $this->bound($id);

        if(is_null($content)) {
            abort(404);
        }

        $content->load($model::$eager);

        $this->authorize('view', $content);

        if (!View::exists($view)) {
            return $content;
        }

        return View::make($view, $this->params([
            // return an instance of content that should have been route model binded
            $this->names['singular'] => $content
        ], $this->viewData['show']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $contentID
     * @return \Illuminate\Http\Response
     */
    public function contentEdit($contentID)
    {
        $content = $this->bound($contentID);

        $this->authorize('update', $content);

        $model = $this->instance;

        return View::make($this->view('edit'), $this->params([
            // This needs to be updated as it returns everything in the content table, this should return the list of objects of that type
            $this->names['plural'] => $model::select('id', 'status', 'revision', 'language', 'title')->get(),
            // Get the current content model object
            $this->names['singular'] => $content,
            // Get the relationship types
            'relationTypes' => Content::ofRelation('content-type', 'relation-type')->get(),
        ], $this->viewData['edit']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $contentID
     * @return \Illuminate\Http\Response
     */
    public function contentUpdate(Request $request, $contentID)
    {
        $content = $this->bound($contentID);

        $this->authorize('update', $content);

        DB::table($this->historyTable)->insert([
            'content_id' => $content->id,
            'user_id' => Auth::id(),
            'content' => serialize($content->load(Content::$eager)),
        ]);

        $request->merge([
            'language' => App::getLocale(),
            'revision' => (int)$content->revision + 1
        ]);

        // Update the content
        $content->update($request->all());

        // Save the content
        $content->save();

        $this->saveMetaData($content, $request);
        $this->saveRelationships($content, $request);

        event(new ContentEvent($content));

        if ($this->redirects) {
            return redirect(route(($this->redirectsKey ?: $this->names['singular']).'.index', $content));
        }

        return $content;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $contentID
     * @return \Illuminate\Http\Response
     */
    public function translate(Request $request, $contentID)
    {
        // Check to see if the translation exists if it does, we cannot save, this is not the way to translate stuff
        $orignal = $this->bound($contentID);

        $request->merge(['key' => str_slug($request->title)]);
        // $request->merge(['key' => $orignal->key]);

        $content = (new $this->model($request->all()));
        $content->save();

        foreach($orignal->meta as $meta) {

            $metaRecord = (new ContentMeta([
                'key' => $meta->key,
                'value' => $request->meta_value[$meta->id]
            ]));

            $content->meta()->save($metaRecord);
            $metaRecord->save();
        }

        $content->saveRelation('translations', $orignal->id);
        $orignal->saveRelation('translations', $content->id);

        if ($this->redirects) {
            return redirect(route(($this->redirectsKey ?: $this->names['singular']).'.index', $content));
        }

        return $content;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $contentID
     * @return \Illuminate\Http\Response
     */
    public function contentDestroy($contentID)
    {
        $content = $this->bound($contentID);

        $this->authorize('delete', $content);

        $content->delete();

        event(new ContentEvent($content));

        if ($this->redirects) {
            return redirect(route(($this->redirectsKey ?: $this->names['singular']).'.index', $content));
        }
    }

    /**
     * Internal method to save all of the requests meta data
     * @param  Content $content Content model of which to save meta data for
     * @param  Request $request Request content posted
     * @return void             We don't return anything, perhaps we should..
     */
    private function saveMetaData(Content $content, Request $request)
    {
        if (!$request->meta_key) {
            return;
        }

        // Get the ids of the meta that was present on the page when the form was loaded
        $metaIds = json_decode($request->meta_ids) ?: [];

        foreach ($request->meta_key as $id => $key) {

            if (!empty($key) && !empty($request->meta_value[$id])) {

                if (in_array($id, $metaIds) && $metaRecord = ContentMeta::where('id', $id)) {
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

    /**
     * Internal method to save all of the requests relationships
     * @param  Content $content Content model of which to save relationships for
     * @param  Request $request Request content posted
     * @return void             We don't return anything, perhaps we should..
     */
    private function saveRelationships(Content $content, Request $request)
    {
        if (!$request->relation_ids) {
            return;
        }

        // Get the ids of the meta that was present on the page when the form was loaded
        $resourceIds = json_decode($request->relation_ids) ?: [];

        foreach ($request->content_id as $id => $content_id) {

            if (in_array($id, $resourceIds) && $relationRecord = ContentRelation::where('id', $id)) {
                $relationRecord
                    ->update([
                        'relation_id' => $request->relation_id[$id],
                        'relation_type_id' => $request->relation_type_id[$id],
                    ]);

                unset($resourceIds[array_search($id, $resourceIds)]);
            }
            else {
                if (!empty($content_id) && !empty($request->relation_id[$id]) && !empty($request->relation_type_id[$id])) {
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
