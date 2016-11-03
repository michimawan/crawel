<?php

namespace App\Http\Controllers;

use Exception;
use Log;
use Curl\Curl;
use Illuminate\Http\Request;

use Google_Client as GoogleClient;
use Google_Service_Sheets as GoogleSpreadSheets;
use App\Lib\StoryRepository;
use App\Lib\GoogleSheet;
use App\Lib\Helper;
use App\Lib\Curler;
use Carbon\Carbon;
use App\Story;
use Config;

class StoriesController extends Controller
{
	public function index(Request $request)
	{
		$date = $request->input('date') ? $request->input('date') : '';

		$projects = Config::get('pivotal.projects');
		$projects = (new Helper)->reverseProjectIds($projects);
		$stories = (new StoryRepository)->getByDate($date);

		$stories = (new Helper)->grouping($projects, $stories);

		return view('stories.index', [
			'stories' => $stories,
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

		$ids = (new Helper())->parse($stories);
		$responses = (new Curler())->curl($project, $ids, $curl);
		(new StoryRepository())->store($responses);
		try {
			$newRow = (new Helper())->prepareForSheet($project, $responses);
			$client = new GoogleClient;
			$googleSheet = new GoogleSheet();
			$client = $googleSheet->setClient($client, Config::get('google.credentials'));
			$googleSheet->sendToSpreadSheet($client, $newRow);
		} catch(Exception $e) {
			Log::info('Failed send to spreadsheet caused by: ' . $e->getMessage());
			Log::info($e->getTraceAsString());
		}
		// try {
		// 	$newRow = (new Helper())->prepareForSheet($project, $responses);
		// 	$client = new GoogleClient;
		// 	$googleSheet = new GoogleSheet();
		// 	$client = $googleSheet->setClient($client, Config::get('google.credentials'));
		// 	$googleSheet->sendToSpreadSheet($client, $newRow);
		// } catch(Exception $e) {
		// 	Log::info('Failed send to spreadsheet caused by: ' . $e->getMessage());
		// 	Log::info($e->getTraceAsString());
		// }

		return redirect()->route('stories.index');
	}
}
