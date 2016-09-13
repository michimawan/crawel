<?php

namespace App\Http\Controllers;

use Curl\Curl;
use Illuminate\Http\Request;

use App\Crawler;
use App\Lib\ParseStories;
use App\Lib\Curler;
use Config;

class CrawlersController extends Controller
{
	public function index()
	{
		$stories = $this->getStories();
		$projectData = $this->getProjectData();

		return view('crawler.index', [
			'stories' => $stories,
			'project' => $projectData
		]);
	}

	private function getStories()
	{
		$apiToken = '';
		$projectId = '';
		$baseUrl = "https://www.pivotaltracker.com/services/v5/projects/{$projectId}/stories?";

		$limit = "&limit=10";
		$state = "&with_state=unstarted";
		$buildedFilter = $limit . $state;

		$curl = new Curl;
		$curl->setHeader('X-TrackerToken', $apiToken);
		$curl->get($baseUrl . $buildedFilter);

		return json_decode($curl->response);
	}

	private function getProjectData()
	{
		$apiToken = '';
		$projectId = '';
		$url = "https://www.pivotaltracker.com/services/v5/projects/{$projectId}";

		$curl = new Curl;
		$curl->setHeader('X-TrackerToken', $apiToken);
		$curl->get($url);

		return json_decode($curl->response);

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
		
	}
}