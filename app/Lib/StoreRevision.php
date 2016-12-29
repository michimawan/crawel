<?php
namespace App\Lib;

use App\Lib\RevisionRepository;
use App\Lib\StoryHelper;
use App\Lib\Helper;
use App\Lib\Kraken;
use Curl\Curl;
use Config;

class StoreRevision
{
    public $curl;
    protected $childTagRev;
    protected $workspace;

    public function __construct($workspace, $tagRev)
    {
        $this->workspace = $workspace;
        $this->childTagRev = $tagRev;
        $this->curl = new Curl;
        $this->curl->setHeader('X-TrackerToken', Config::get('pivotal.apiToken'));
    }

    /**
     * @return true if process is succeed
     * false if no bottom limit or no commit(s) found in git command
     */
    public function process()
    {
        $upperLimit = $this->createUpperLimit($this->childTagRev);
        $bottomLimit = $this->getBottomLimit();

        if (empty($bottomLimit) || empty($upperLimit)) {
            return false;
        }

        $gitLog = $this->getGitLog($upperLimit, $bottomLimit);
        $gitLog = str_replace('"', "'", $gitLog);
        if (empty($gitLog)) {
            return false;
        }

        list ($status, $rev) = $this->storeRevision($upperLimit);
        if ($status) {
            $tags = $this->storeTagsAndStories($gitLog);
            $rev->syncTags($tags->pluck('id')->all());
            return true;
        }

        return false;
    }

    public function storeRevision($upperLimit)
    {
        $revRepo = new RevisionRepository;
        return $revRepo->store($upperLimit, $this->workspace);
    }

    public function storeTagsAndStories($gitLog = '')
    {
        list($greenTags, $ids) = StoryHelper::parseText($gitLog);

        $responses = (new Curler())->curl($this->workspace, $ids, $this->curl);
        (new StoryRepository())->store($responses);
        $tags = (new TagRepository())->store($this->workspace, $greenTags);

        return $tags;
    }

    public function createUpperLimit($childTagRev)
    {
        return Helper::jenkinsToGitTagging($this->workspace, $childTagRev);
    }

    public function getBottomLimit()
    {
        $sourceRevision = Config::get('pivotal.source-revision')[$this->workspace];
        $this->curl->get($sourceRevision);

        if ($this->curl->http_status_code == 200) {
            return Kraken::parseRevisionsLog($this->curl->response);
        }

        return '';
    }

    public function getGitLog($upperLimit, $bottomLimit)
    {
        $baseCommand = Config::get('git.command.base');
        $gitCommand = "{$baseCommand} {$upperLimit}...{$bottomLimit}";
        $gitDirectory = Config::get('pivotal.repo_path')[$this->workspace];
        $gitLog = "";
        exec("cd {$gitDirectory}; git pull --rebase origin master; {$gitCommand}", $gitLog);
        return str_replace('"', "'", join('\n', $gitLog));
    }
}