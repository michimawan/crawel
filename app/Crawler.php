<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Crawler extends Model
{
	protected $fillable = [
		'pivotal_id',
		'title',
		'point',
		'state',
		'project_name',
		'story_type'
	];
}
