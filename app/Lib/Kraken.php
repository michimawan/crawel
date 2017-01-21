<?php
namespace App\Lib;

use App\Lib\StoryHelper;
use App\Lib\Curler;
use Carbon\Carbon;
use Curl\Curl;
use Config;

class Kraken
{
    /**
     * Helper Section
     */
    public static function parseRevisionsLog($text = '')
    {
        $pattern = '/Branch (?P<baseRevision>[a-zA-Z0-9\-_]+)/i';
        $matches = [];

        $found = preg_match_all($pattern, $text, $matches);
        if ($found) {
            return $matches['baseRevision'][0];
        }

        return '';
    }

    public static function parseGitTag($text = '')
    {
        $pattern = '/\(tag: (?P<tagRev>[a-zA-Z0-9\-_]+)\)/i';
        $matches = [];

        $found = preg_match_all($pattern, $text, $matches);
        return $matches['tagRev'];
    }

    public static function getDeployedStoryIds($baseRevision = '', $text = '')
    {
        $lines = preg_split("/\\r\\n|\\r|\\n/", $text);

        $storyIds = [];
        foreach ($lines as $key => $line) {
            $greenTags = static::parseGitTag($line);
            if (count($greenTags) && $baseRevision == $greenTags[0]) {
                break;
            } else {
                $storyIds = array_merge($storyIds, StoryHelper::parse($line));
            }
        }

        return array_unique($storyIds);
    }

    /**
     * Main Section
     */
    public function __construct($workspace, $rawBaseRevision, $gitCommits)
    {
        $this->workspace = $workspace;
        $this->rawBaseRevision = $rawBaseRevision;
        $this->gitCommits = $gitCommits;
    }

    public function execute()
    {
        $baseRevision = static::parseRevisionsLog($this->rawBaseRevision);
        $storyIds = static::getDeployedStoryIds($baseRevision, $this->gitCommits);

        $curler = new Curler;
        $response = $this->getStories($curler, $this->workspace, $storyIds);
        return $this->print($baseRevision, $response);
    }

    public function getStories($curler, $workspace, $storyIds)
    {
        $curl = new Curl;
        $curl->setHeader('X-TrackerToken', Config::get('pivotal.apiToken'));

        return $curler->curl($workspace, $storyIds, $curl);
    }

    public function print($baseRevision, $response)
    {
        $time = Helper::sanitizeDate(Carbon::today()->toDateTimeString(), ' ');

        $str = strtoupper($this->workspace) . "\n";
        $str .= "Date: {$time}\n";
        $str .= "Revisions: {$baseRevision}\n";
        $str .= "Get Greentag Time:\n";
        $str .= "Stories:\n";

        $projects = Config::get('pivotal.projects');
        $mappedProjectIds = Helper::reverseProjectIds($projects);
        $counter = 1;
        foreach ($response as $projectId => $stories) {
            foreach ($stories as $story) {
                $strStory = "";
                $type = $story->story_type == 'chore' || $story->story_type == 'bug' ? $story->story_type : "{$story->estimate} point(s)";

                $strStory .= "{$counter}. [#{$story->id}][{$mappedProjectIds[$this->workspace][$projectId]}] {$story->name} ({$type})";

                $counter++;
                $str .= $strStory . "\n";
            }
        }

        $str .= "End Time To Check Stories:\n";
        $str .= "End Time To Run Automate Test:\n";
        $str .= "Time Get Canary:\n";
        $str .= "End Time To Test Canary:\n";
        $str .= "End Time To ELB :\n";
        $str .= "Description: Deploy";

        return $str;
    }
}