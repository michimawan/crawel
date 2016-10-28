<?php
namespace App\lib;

use Illuminate\Database\QueryException;
use Carbon\Carbon;

use App\Lib\Helper;
use App\Models\Tag;

class TagRepository
{
	public function store($tags)
	{
        foreach ($tags as $greenTag) {
            $tag = new Tag;
            $tag->code = $greenTag['greenTagId'];
            $tag->timing = $greenTag['greenTagTiming'];
            $date = Carbon::today();
            $storedDate = Helper::sanitizeDate($date, ' ');
            $tag->last_updated_at = json_encode([$storedDate]);
            try {
                $tag->save();
            } catch(QueryException $e) {
                $tag = Tag::where('code', $greenTag['greenTagId'])->first();
            }
            $tag->syncStories($greenTag['stories']);
        }
	}

    public function getByDate($date = '')
    {
    }
}
