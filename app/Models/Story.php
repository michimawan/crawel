<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
	protected $fillable = [
		'pivotal_id',
		'title',
		'point',
		'state',
		'project_name',
		'story_type'
	];

    /**
     * relation section
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'story_tag');
    }
}
