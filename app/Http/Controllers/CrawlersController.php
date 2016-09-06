<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Curl\Curl;

use App\Http\Requests;
use App\Crawler;
use Log;

class CrawlersController extends Controller
{
	public function index()
	{
		// $apiToken = '908cac5d19393bb1cf91fa8ea6c0335f';
		// $projectId = '828039';
		// $baseUrl = "https://www.pivotaltracker.com/services/v5/projects/{$projectId}/stories?";

		// $filter = "filter=type%3Achore";
		// $limit = "&limit=10";

		// $curl = new Curl;
		// $curl->setHeader('X-TrackerToken', $apiToken);
		// $result = $curl->get($baseUrl . $filter . $limit);
		// print_r($curl->response);
		// die();

		$crawler = Crawler::all();

		return view('crawler.index', ['crawler' => $crawler]);
	}
}