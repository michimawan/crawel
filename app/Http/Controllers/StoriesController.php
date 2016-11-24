<?php

namespace App\Http\Controllers;

use Exception;
use Log;
use Curl\Curl;
use Illuminate\Http\Request;

use Google_Client as GoogleClient;
use Google_Service_Sheets as GoogleSpreadSheets;
use App\Lib\StoryRepository;
use App\Lib\TagRepository;
use App\Lib\GoogleSheet;
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
        $tag = (new TagRepository)->getByDate($date);

        $tag = (new Helper)->grouping($projects, $tag);

        return view('stories.index', [
            'tag' => $tag,
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        $project = Config::get('pivotal.projects');
        $option = [];
        foreach($project as $key => $p) {
            $option[$key] = $key;
        }
        return view('stories.create', [
            'options' => $option
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

        list($greenTags, $ids) = Helper::parseText($stories);
        $responses = (new Curler())->curl($project, $ids, $curl);
        (new StoryRepository())->store($responses);
        (new TagRepository())->store($project, $greenTags);

        return redirect()->route('stories.index');
    }

    public function edit(Request $request)
    {
        return view('stories.edit');
    }
}
