<?php
namespace App\Lib;

use Illuminate\Database\QueryException;
use Carbon\Carbon;

use App\Lib\Helper;
use App\Models\Story;

class StoryRepository
{
	public function store($projects)
	{
		foreach($projects as $project) {
			foreach($project as $task) {
				$story = new Story();
				$story->pivotal_id = $task->id;
				$story->title = $task->name;
				$story->point = isset($task->estimate) ? $task->estimate : 0;
				$story->project_id = $task->project_id;
				$story->story_type = $task->story_type;
				$story->status = $task->current_state;

				$date = Helper::sanitizeDate($this->todayDate(), ' ');
				try {
					$story->save();
				} catch(QueryException $e) {
					$oldData = Story::where('pivotal_id', $task->id)->first();
					$oldData->status = $task->current_state;
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
		return Story::whereRaw("JSON_SEARCH(last_updated_at, 'one', '{$date}') IS NOT NULL")->get();
	}
}