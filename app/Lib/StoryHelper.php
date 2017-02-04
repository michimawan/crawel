<?php
namespace App\Lib;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Config;

use App\Lib\Helper;
use App\Models\Revision;
use App\Models\Tag;

class StoryHelper
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
        $greenTags = self::parseGitTags($text);
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
    public static function parseJenkinsTag(string $text = '')
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

    public static function parseGitTags(string $text = '')
    {
        $pattern = '/(?P<green_tag_timing>tag: [a-zA-Z-]+(?P<timing>[a-zA-Z0-9_-]+))/i';

        $matches = [];
        $greenTag = [];
        $found = preg_match_all($pattern, $text, $matches);
        if ($found) {
            foreach($matches['green_tag_timing'] as $idx => $match) {
                $greenTag[$match] = [
                    'greenTagId' => $matches['green_tag_timing'][$idx],
                    'greenTagTiming' => $matches['timing'][$idx],
                ];
            }
        }

        return $greenTag;
    }

    public static function parseGitTag($text = '')
    {
        $pattern = '/(?P<green_tag_timing>tag: [a-zA-Z-]+(?P<timing>[a-zA-Z0-9_-]+))/i';
        $matches = [];
        $found = preg_match_all($pattern, $text, $matches);
        return [$found, $matches];
    }

    /**
     * @return array of greenTags that has storyIds as array in it
     *
     * @param array of greenTags that has not have index 'stories'
     * @param string of raw text from git log
     */
    public static function addStoriesToGreenTag($greenTags = [], $text = '')
    {
        $pattern = '/\\r\\n|\\r|\\n/i';
        $regexLines = preg_split($pattern, $text);
        $explodeLines = explode('\n', str_replace(['\r\n', '\r'], '\n', $text));

        if (count($regexLines) >= count($explodeLines)) {
            $lines = $regexLines;
        } elseif(count($regexLines) < count($explodeLines)) {
            $lines = $explodeLines;
        }
        $greenTagsString = array_keys($greenTags);

        $lineCount = count($lines);
        for ($i=0; $i < $lineCount ; $i++) {
            $stories = [];
            list($foundI, $matchesI) = static::parseGitTag($lines[$i]);
            if ($foundI) {
                $j = $i;
                $foundJ = true;
                do {
                    $ids = static::parse($lines[$j]);
                    if (count($ids)) {
                        $stories = array_merge($stories, $ids);
                    }
                    $j++;
                    if ($j < $lineCount) {
                        list($foundJ, $matchesJ) = static::parseGitTag($lines[$j]);
                    }
                } while( $j < $lineCount && !$foundJ);

                if (count($stories) == 0) {
                    unset($greenTags[$matchesI['green_tag_timing'][0]]);
                } else {
                    $greenTags[$matchesI['green_tag_timing'][0]]['stories'] = $stories;
                }
                $i = $j - 1;
            }
        }

        return $greenTags;
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
                $str .= "Time To Check Stories: {$revision->time_to_check_story}\n";
                $str .= "End Time To Check Stories: {$revision->end_time_check_story}\n";
                $str .= "End Time To Run Automate Test: {$revision->end_time_run_automate_test}\n";
                $str .= "Time Get Canary: {$revision->time_get_canary}\n";
                $str .= "End Time To Test Canary: {$revision->time_to_finish_test_canary}\n";
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
        $mappedProjectIds = Helper::reverseProjectIds($projects);

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