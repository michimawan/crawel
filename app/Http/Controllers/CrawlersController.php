<?php

namespace App\Http\Controllers;

use Curl\Curl;
use Illuminate\Http\Request;

use App\Crawler;
use App\Lib\ParseStories;

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
        return view('crawler.create');
	}

	public function store(Request $request)
	{
		$stories = $request->input('stories');
		$parseStories = new ParseStories();
		$ids = $parseStories->parse($stories);
		print_r($ids);
		die();
		
	}
}