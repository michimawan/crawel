<?php

namespace App\Http\Controllers;

use Exception;
use Log;
use Curl\Curl;
use Illuminate\Http\Request;

use Google_Client as GoogleClient;
use App\Lib\StoryRepository;
use App\Lib\RevisionRepository;
use App\Lib\TagRepository;
use App\Lib\StoryHelper;
use App\Lib\Helper;
use App\Lib\Curler;
use Carbon\Carbon;
use Config;

class StoriesController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date') ? $request->input('date') : null;

        $projects = Config::get('pivotal.projects');
        $projects = (new Helper)->reverseProjectIds($projects);
        $rev = (new RevisionRepository)->getByDate($date);

        $rev = (new Helper)->grouping($projects, $rev);

        return view('stories.index', [
            'rev' => $rev,
            'projects' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $stories = $request->input('stories');
        $project = $request->input('project');

        if (is_null($stories)) {
            return redirect()->route('stories.create');
        }

        $curl = new Curl;
        $curl->setHeader('X-TrackerToken', Config::get('pivotal.apiToken'));

        list($greenTags, $ids) = StoryHelper::parseText($stories);
        $responses = (new Curler())->curl($project, $ids, $curl);
        (new StoryRepository())->store($responses);
        (new TagRepository())->store($project, $greenTags);

        return redirect()->route('stories.index');
    }
}
