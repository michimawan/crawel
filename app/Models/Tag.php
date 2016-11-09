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
    public function syncStories($storyIds = []) {
        $storyIds = array_filter($storyIds, function($c){
            return !empty($c);
        });

        $this->stories()->sync($storyIds);
        $this->cached_stories = $this->stories()->get();
        $this->save();
    }
}
