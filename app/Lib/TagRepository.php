<?php
namespace App\lib;

use Illuminate\Database\QueryException;
use Carbon\Carbon;
use Config;
use Log;

use App\Models\Story;
use App\Lib\Helper;
use App\Models\Tag;

class TagRepository
{
	public function store($project, $tags)
	{
        foreach ($tags as $greenTag) {
            $tag = new Tag;
            $tag->code = $greenTag['greenTagId'];
            $tag->timing = $greenTag['greenTagTiming'];
            $tag->project = $project;

            $ids = $this->getStoryIds($project, $greenTag['stories']);
            if (count($ids)) {
                try {
                    $tag->save();
                } catch(QueryException $e) {
                    $tag = Tag::where('code', $greenTag['greenTagId'])->where('project', $project)->first();
                    Log::info($e->getMessage());
                    Log::info($e->getTraceAsString());
                }
                $tag->syncStories($ids);
            }
        }
	}

    private function getStoryIds($project, $pivotalIds = [])
    {
        $projects = Config::get('pivotal.projects');
        $projects = (new Helper)->reverseProjectIds($projects);
        $validProjectIds = array_keys($projects[$project]);

        $stories = Story::whereIn('pivotal_id', $pivotalIds)->get();
        $stories = $stories->whereIn('project_id', $validProjectIds);
        return $stories->pluck('id')->all();
    }

    public function getByDate($date = null)
    {
        if ($date == null) {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->addDay();
        } else {
            $date .= ' 00';
            $startDate = Carbon::createFromFormat('Y-m-d H', $date);
            $endDate = clone $startDate;
            $endDate->addDay();
        }
        return Tag::where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->get();
    }
}
