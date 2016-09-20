<?php
namespace App\Lib;

use App\Crawler;
use Illuminate\Database\QueryException;

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
				$crawler->last_updated_at = $story->updated_at;
				try {
					$crawler->save();
				} catch(QueryException $e) {
					$oldData = Crawler::where('pivotal_id', $story->id)->first();
					$oldData->last_updated_at = $story->updated_at;
					$oldData->save();
				}
			}
		}
	}
}