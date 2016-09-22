<?php

namespace App\Lib;

use Illuminate\Support\Collection;

class Parser
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
}