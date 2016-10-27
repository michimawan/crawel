<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
	protected $fillable = [
		'code',
		'timing',
	];

    public $cached_stories;

    /**
     * relation section
     */
    public function stories()
    {
        return $this->belongsToMany(Story::class, 'story_tag', 'tag_id', 'story_id');
    }

    /**
     * method section
     */
    public function syncStories($storyId = []) {
        $ids = [];
        $storyId = array_filter($storyId, function($c){
            return !empty($c);
        });

        foreach ($storyId as $story) {
            $tag = Story::where('pivotal_id', $story)->first();

            if ($tag) {
                $ids[] = $tag->id;
            }
        }

        $this->stories()->sync($ids);
        $this->cached_stories = $this->stories()->get();
        $this->save();
    }
}
