<?php
namespace App\Lib;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Config;

use App\Models\Revision;
use App\Models\Tag;

class Helper
{
    /**
     * @return array of greenTags and array of StoryIDs
     *
     * @param raw text from Jenkins CI
     * example text that been parsed
     * available on directory datas
     */
    public static function parseText(string $text)
    {
        $greenTags = self::parseGreenTag($text);
        $greenTags = self::addStoriesToGreenTag($greenTags, $text);

        $storyIds = self::parse($text);

        return [$greenTags, $storyIds];
    }

    /**
     * @return array of pivotaltracker storyIDs
     *
     * @param string of raw text to be parsed it's storyIDs
     */
    public static function parse(string $text = '')
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

    /**
     * @return array of greenTag
     *
     * @param raw string to be parsed it's greenTag
     * example match pattern
     * #1587 (Oct 20, 2016 5:23:39 PM)
     */
    public static function parseGreenTag(string $text = '')
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

    /**
     * @return array of greenTags that has storyIds as array in it
     *
     * @param array of greenTags that has not have index 'stories'
     * @param string of raw text from jenkins CI
     */
    public static function addStoriesToGreenTag($greenTags = [], $text = '')
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

    /**
     * @return array of reversed projectIds from Config.pivotal.projects
     *
     * @param array of config.pivotal.projects
     */
    public static function reverseProjectIds($groups = [])
    {
        $projectIds = [];
        foreach($groups as $groupName => $group) {
            foreach($group as $projectName => $projectId) {
                $projectIds[$groupName][$projectId] = $projectName;
            }
        }

        return $projectIds;
    }

    /**
     * @return Collection that has been grouped based on workspace
     *
     * @param array of workspace group, based on config.pivotal.projects
     * @param Collection of Revision
     */
    public function grouping($groups, $greenTags)
    {
        $collection = collect();

        foreach($groups as $groupName => $groups) {

            $projectIds = array_keys($groups);
            $filteredStories = $greenTags->where('project', $groupName);

            $collection->put($groupName, $filteredStories);
        }

        return $collection;
    }

    /**
     * @return array of data that will be send to google sheet
     *
     * @param string of workspace name,
     * @param array of rawResponse from curling pivotal API
     */
    public function prepareForSheet($project, $rawResponse)
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

    /**
     * @return date 2016-12-31
     *
     * @param string of date, e.g: 2016-12-01 15:03:55
     * @param char separator that will be used
     */
    public static function sanitizeDate($date, $delimeter)
    {
        return substr($date, 0, strpos($date, $delimeter));
    }

    /**
     * @return array of Revision that been selected on mails/create
     *
     * @param Request object from controller
     */
    public static function getSelectedRevisions($request)
    {
        $workspaces = array_keys(Config::get('pivotal.projects'));

        $selectedRevisions = [];
        foreach ($workspaces as $workspace) {
            $lowercased = strtolower($workspace);
            $inputName = "{$lowercased}_revisions";
            $selectedRevisions[$workspace] = $request->input($inputName);
        }

        return $selectedRevisions;
    }

    /**
     * @return array of Tag that been selected on revisions/create
     *
     * @param Request object from controller
     */
    public static function getSelectedGreenTags($request)
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

    /**
     * @return string of data that will be send to google mail
     *
     * @param array of selected Revision
     */
    public static function prepareForMail($selectedChildTagRevs)
    {
        $workspaces = [];
        foreach ($selectedChildTagRevs as $projectName => $selectedTag) {
            $str = "";
            $str .= ucwords($projectName) . "\n";
            if (is_null($selectedTag) || count($selectedTag) == 0) {
                $str .= "No Child Tag Revs Today\n ";
                $workspaces[] = $str;
                continue;
            }

            // find the revision model
            foreach ($selectedTag as $rev) {
                $strRev = "";
                $revision = Revision::with('tags', 'tags.stories')->find($rev);

                if (! $revision) {
                    continue;
                }

                $date = Carbon::today()->toDateString();
                $strRev .= "Date: {$date}\n";
                $strRev .= "Revisions: {$revision->child_tag_revisions}\n";
                $strRev .= "Stories:\n";

                $stories = collect();
                foreach ($revision->tags as $greenTags) {
                    $stories = $stories->merge($greenTags->stories);
                }
                $strRev .= static::stringifyStory($projectName, $stories);

                $str .= $strRev . "\n ";
                $str .= "End Time To Check Stories: {$revision->end_time_check_story}\n";
                $str .= "End Time To Run Automate Test: {$revision->end_time_run_automate_test}\n";
                $str .= "Time Get Canary: {$revision->time_get_canary}\n";
                $str .= "End Time To Test Canary: {$revision->time_get_canary}\n";
                $str .= "End Time To ELB : {$revision->time_to_elb}\n";
                $str .= "Description: {$revision->description}\n";
            }

            $workspaces[] = $str . ' ';
        }
        return join("\n", $workspaces);
    }

    /**
     * @return string of story that's been formatted
     *
     * @param string of projectName, e.g: foo-1
     * @param array of stories
     */
    private static function stringifyStory($projectName, $stories)
    {
        $projects = Config::get('pivotal.projects');
        $mappedProjectIds = static::reverseProjectIds($projects);

        $stories = $stories->unique(function($item) {
            return $item->pivotal_id;
        });
        $str = "";
        foreach ($stories as $idx => $story) {
            $pivotalName = $mappedProjectIds[$projectName][$story->project_id];
            $type = $story->story_type == 'chore' || $story->story_type == 'bug' ? $story->story_type : "{$story->point} point(s)";

            $str .= "[#{$story->pivotal_id}][{$pivotalName}] {$story->title} ({$type}) \n";
        }

        return $str;
    }
}
