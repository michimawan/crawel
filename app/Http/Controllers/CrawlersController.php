<?php

namespace App\Http\Controllers;

use Curl\Curl;

use App\Crawler;

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
}