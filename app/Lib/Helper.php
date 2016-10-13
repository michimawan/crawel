<?php

namespace App\Lib;

use Config;
use Illuminate\Support\Collection;

class Helper
{
	public function parse(string $textToParse = '') : array
	{
		$pattern = '/#(?P<id>\d+)\]/i';
		$matches = [];
		$ids = [];

		$found = preg_match_all($pattern, $textToParse, $matches);
		
		if ($found) {
			foreach($matches['id'] as $match) {
				$ids[] = $match;
			}
		}

		return $ids;
	}

	public function reverseProjectIds($groups = []) : array
	{
		$projectIds = [];
		foreach($groups as $groupName => $group) {
			foreach($group as $projectName => $projectId) {
				$projectIds[$groupName][$projectId] = $projectName;
			}
		}

		return $projectIds;
	}

	public function grouping($groups, $stories) : Collection
	{
		$collection = collect();

		foreach($groups as $groupName => $groups) {

			$projectIds = array_keys($groups);
			$filteredStories = $stories->whereIn('project_id', $projectIds);

			$collection->put($groupName, $filteredStories);
		}

		return $collection;
	}

	public function prepareForSheet($project, $rawResponse) : array
	{
		$projects = Config::get('pivotal.projects');
		$mappedProjectIds = $this->reverseProjectIds($projects);
		$preparedContent = [];
		$index = 1;
		$str = "";
		foreach($rawResponse as $subProject) {
			foreach($subProject as $story) {
				$type = $story->story_type == 'chore' || $story->story_type == 'bug' ? $story->story_type : "{$story->estimate} point";

				$str .= "{$index}. [#{$story->id}][{$mappedProjectIds[$project][$story->project_id]}] {$story->name} ({$type}) \r\n";
				$index++;
			}
		}
		$preparedContent[] = $project;
		$preparedContent[] = $str;
		return [$preparedContent];
	}

	public static function sanitizeDate($date, $delimeter)
	{
		return substr($date, 0, strpos($date, $delimeter));
	}
}