<?php

namespace App\Http\Controllers;

use Curl\Curl;
use Illuminate\Http\Request;

use App\Lib\CrawlerRepository;
use App\Lib\ParseStories;
use App\Lib\Curler;
use Carbon\Carbon;
use App\Crawler;
use Config;

class CrawlersController extends Controller
{
	public function index()
	{
		$projectInfo = Config::get('pivotal.projects');
		$stories = Crawler::where('updated_at', '>', Carbon::today())->get();

		return view('crawler.index', [
			'stories' => $stories,
			'projectInfo' => $projectInfo
		]);
	}

	public function create()
	{
		$project = Config::get('pivotal.projects');
		$option = [];
		foreach($project as $key => $p) {
			$option[$key] = $key;
		}
        return view('crawler.create', [
        	'options' => $option
        ]);
	}

	public function store(Request $request)
	{
		$stories = $request->input('stories');
		$project = $request->input('project');
		$curl = new Curl;
		$curl->setHeader('X-TrackerToken', Config::get('pivotal.apiToken'));

		$ids = (new ParseStories())->parse($stories);
		$responses = (new Curler())->curl($project, $ids, $curl);
		(new CrawlerRepository())->store($responses);
		// hit excel
		// redirect ke index
	}
}