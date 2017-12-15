<?php

namespace Baytek\Laravel\Content\Controllers;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Models\ContentMeta;
use Baytek\Laravel\Content\Models\ContentRelation;
use Baytek\Laravel\Settings\SettingsProvider;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

use Diff;
use View;

class ContentManagementController extends ContentController
{
    /**
     * The model the Content Controller super class will use to access the resource
     *
     * @var Baytek\Laravel\Content\Models\Content
     */
    protected $model = Content::class;

    /**
     * Show the index of all content with content type 'webpage'
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id = 1)
    {
        return parent::contentIndex([
            'contents' => content($id)->children
        ]);
    }

    public function children($id)
    {
        return content($id)->children;
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return parent::contentCreate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return parent::contentStore($request);
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return parent::contentEdit($id);
    }

    /**
     * Show the webpage
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // overriding the view if we have an inline view namespace
        // FIXME: This needs to use setters, fancier as well.
        if (isset(\Route::getCurrentRoute()->action['as']) && \Route::getCurrentRoute()->action['as'] == 'content.inline') {
            $this->views['show'] = 'content.inline';
        }

        $renderer = new \Diff_Renderer_Html_Inline;

        $content = Content::find($id);

        $this->viewData['show'] = [
            'actualRevisions' => $content->revision,
            'revision' => $content->revision,
            'diff' => false,
        ];

        if ($content->revisions->count()) {
            $previous = unserialize($content->revisions->last()->content)->content;
            $a = explode("\n", $previous);
            $b = explode("\n", $content->content);
            $diff = new \Diff($a, $b, []);
            $this->viewData['show']['diff'] = $diff->render($renderer);
        }

        return parent::contentShow($id);
    }

    /**
     * Show the webpage
     *
     * @return \Illuminate\Http\Response
     */
    public function revision($id, $revision = 0)
    {
        $renderer = new \Diff_Renderer_Html_Inline;

        // Initialize the diff class
        $content = Content::find($id);

        $latestRevision = $content->revision;

        $this->viewData['show'] = [
            'actualRevisions' => $content->revision,
            'revision' => $revision,
            'diff' => false
        ];

        if ($content->revision === $revision) {
            // This is an exception, but content is already set where we want it
        } elseif ($content->revision - 1 < $revision) {
            throw new \Exception('Content revision does not exist');
        } elseif ($content->revision - 1 >= $revision) {
            $content = unserialize($content->revisions->get($revision)->content);
        }

        if ($revision -1 >= 0 || ($latestRevision === $revision && $revision != 0)) {
            $previous = unserialize($content->revisions->get($revision-1)->content)->content;
            $a = explode("\n", $previous);
            $b = explode("\n", $content->content);
            $diff = new \Diff($a, $b, []);
            // $diff = \Baytek\Laravel\Libraries\Diff::toHTML(\Baytek\Laravel\Libraries\Diff::compare($previous, $content->content));
            $this->viewData['show']['diff'] = $diff->render($renderer);
        }

        return parent::contentShow($content);
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return parent::contentUpdate($request, $id);
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return parent::contentDestroy($id);
    }
}
