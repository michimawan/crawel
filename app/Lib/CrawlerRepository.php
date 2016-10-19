<?php
namespace App\Lib;

use Illuminate\Database\QueryException;
use Carbon\Carbon;

use App\Lib\Helper;
use App\Crawler;

class CrawlerRepository
{
	public function store($projects)
	{
		foreach($projects as $project) {
			foreach($project as $story) {
				$crawler = new Crawler();
				$crawler->pivotal_id = $story->id;
				$crawler->title = $story->name;
				$crawler->point = isset($story->estimate) ? $story->estimate : 0;
				$crawler->project_id = $story->project_id;
				$crawler->story_type = $story->story_type;

				$date = Helper::sanitizeDate($this->todayDate(), ' ');
				$crawler->last_updated_at = json_encode([$date]);
				try {
					$crawler->save();
				} catch(QueryException $e) {
					$oldData = Crawler::where('pivotal_id', $story->id)->first();

					$lastUpdatedAt = json_decode($oldData->last_updated_at);
					if (! in_array($date, $lastUpdatedAt)) {
						$lastUpdatedAt[] = $date;
					}

					$oldData->last_updated_at = json_encode($lastUpdatedAt);
					$oldData->save();
				}
			}
		}
	}

	private function todayDate()
	{
		return Carbon::now()->toDateTimeString();
	}

	public function getByDate($date = '')
	{
		if (! strlen($date)) {
			$date = Helper::sanitizeDate($this->todayDate(), ' ');
		}
		return Crawler::whereRaw("JSON_SEARCH(last_updated_at, 'one', '{$date}') IS NOT NULL")->get();
	}
}