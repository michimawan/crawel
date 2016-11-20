<?php
namespace App\Lib;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Tag;
use Carbon\Carbon;
use Config;

class Helper
{
	public static function parseText(string $text) : array
	{
		$greenTags = self::parseGreenTag($text);
		$greenTags = self::addStoriesToGreenTag($greenTags, $text);

		$storyIds = self::parse($text);

		return [$greenTags, $storyIds];
	}

	public static function parse(string $text = '') : array
	{
		$pattern = '/#(?P<story_id>\d+)\]/i';
		$matches = [];
		$ids = [];

		$found = preg_match_all($pattern, $text, $matches);

		if ($found) {
			foreach($matches['story_id'] as $match) {
				$ids[] = (int) $match;
			}
		}

		return $ids;
	}

	// example match pattern
	// #1587 (Oct 20, 2016 5:23:39 PM)
	public static function parseGreenTag(string $text = '') : array
	{
		$pattern = '/(?P<green_tag_timing>(?P<green_tag_id>#(\d+)) \((?P<timing>[A-Za-z0-9,: ]+)\))/i';

		$matches = [];
		$greenTag = [];
		$found = preg_match_all($pattern, $text, $matches);
		if ($found) {
			foreach($matches['green_tag_timing'] as $idx => $match) {
				$greenTag[$match] = [
					'greenTagId' => $matches['green_tag_id'][$idx],
					'greenTagTiming' => $matches['timing'][$idx],
				];
			}
		}

		return $greenTag;
	}

	public static function addStoriesToGreenTag(array $greenTags = [], string $text = '') : array
	{
		$lines = preg_split("/\\r\\n|\\r|\\n/", $text);
		$greenTagsString = array_keys($greenTags);

		$lineCount = count($lines);
		for ($i=0; $i < $lineCount ; $i++) {
			$stories = [];
			if (in_array($lines[$i], $greenTagsString)) {
				$j = $i + 1;
				while( $j < $lineCount && ! in_array($lines[$j], $greenTagsString)) {
					$ids = static::parse($lines[$j]);
					if (count($ids)) {
						$stories = array_merge($stories, $ids);
					}
					$j++;
				}
				$greenTags[$lines[$i]]['stories'] = $stories;
				$i = $j - 1;
			}
		}

		return $greenTags;
	}

	public static function reverseProjectIds($groups = []) : array
	{
		$projectIds = [];
		foreach($groups as $groupName => $group) {
			foreach($group as $projectName => $projectId) {
				$projectIds[$groupName][$projectId] = $projectName;
			}
		}

		return $projectIds;
	}

	public function grouping($groups, $greenTags) : Collection
	{
		$collection = collect();

		foreach($groups as $groupName => $groups) {

			$projectIds = array_keys($groups);
			$filteredStories = $greenTags->where('project', $groupName);

			$collection->put($groupName, $filteredStories);
		}

		return $collection;
	}

	public function prepareForSheet($project, $rawResponse) : array
	{
		$projects = Config::get('pivotal.projects');
		$mappedProjectIds = static::reverseProjectIds($projects);
		$preparedContent = [];
		$index = 1;
		$str = "";

		$tmpContent = [];
		foreach($rawResponse as $subProject) {
			foreach($subProject as $story) {
				$type = $story->story_type == 'chore' || $story->story_type == 'bug' ? $story->story_type : "{$story->estimate} point(s)";

				$tmpContent[] = "{$index}. [#{$story->id}][{$mappedProjectIds[$project][$story->project_id]}] {$story->name} ({$type}, {$story->current_state})";
				$index++;
			}
		}
		$preparedContent[] = Carbon::now()->toDateTimeString();
		$preparedContent[] = $project;
		$preparedContent[] = join(" \r\n", $tmpContent);
		return [$preparedContent];
	}

	public static function sanitizeDate($date, $delimeter)
	{
		return substr($date, 0, strpos($date, $delimeter));
	}

	public static function getSelectedGreenTags(Request $request)
	{
		$workspaces = array_keys(Config::get('pivotal.projects'));

		$selectedGreenTags = [];
		foreach ($workspaces as $workspace) {
			$lowercased = strtolower($workspace);
			$inputName = "{$lowercased}_tags";
			$selectedGreenTags[$workspace] = $request->input($inputName);
		}

		return $selectedGreenTags;
	}

	public static function prepareForMail(array $selectedGreenTags)
	{
		$workspaces = [];
		foreach ($selectedGreenTags as $projectName => $selectedTag) {
			$strTag = ucfirst($projectName) . "\r\n";
			if (is_null($selectedTag)) {
				$strTag .= "No Green Tag Today\r\n";
				$workspaces[] = $strTag;
				continue;
			}

			foreach ($selectedTag as $greenTag) {
				$tag = Tag::with('stories')->find($greenTag);
				if (! $tag) {
					continue;
				}
				$strTag .= "{$tag->code} ({$tag->timing})\r\n";

				$stories = $tag->stories;
				$strTag .= static::stringifyStory($projectName, $stories);
			}
			$workspaces[] = $strTag;
		}
		return join("\r\n", $workspaces);
	}

	private static function stringifyStory($projectName, $stories)
	{
		$projects = Config::get('pivotal.projects');
		$mappedProjectIds = static::reverseProjectIds($projects);

		$str = "";
		foreach ($stories as $idx => $story) {
            $pivotalName = $mappedProjectIds[$projectName][$story->project_id];
            $type = $story->story_type == 'chore' || $story->story_type == 'bug' ? $story->story_type : "{$story->point} point(s)";
            $number = $idx + 1;

            $str .= "{$number}. [#{$story->pivotal_id}][{$pivotalName}] {$story->title} ({$type}) \r\n";
		}

		return $str;
	}
}
