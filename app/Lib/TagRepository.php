<?php
namespace App\lib;

use Illuminate\Database\QueryException;
use Carbon\Carbon;

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
            $date = Carbon::today();
            $storedDate = Helper::sanitizeDate($date, ' ');
            try {
                $tag->save();
            } catch(QueryException $e) {
                $tag = Tag::where('code', $greenTag['greenTagId'])->first();
            }
            $tag->syncStories($greenTag['stories']);
        }
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
