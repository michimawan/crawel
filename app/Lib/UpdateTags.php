<?php
namespace App\Lib;

use Curl\Curl;
use App\Models\Tag;
use Carbon\Carbon;
use Config;
use App;

class UpdateTags
{
    private $rev;
    private $text;
    public $curler;

    public function __construct($rev, $text)
    {
        $this->rev = $rev;
        $this->text = $text;

        $this->curl = App::make(Curl::class);
        $this->curl->setHeader('X-TrackerToken', Config::get('pivotal.apiToken'));
    }

    public function run()
    {
        $newIds = StoryHelper::parse($this->text);
        $existsIds = $this->existingPivotalIds();

        $unregisteredIds = array_diff($newIds, $existsIds);
        $tags = $this->storeNewStories($unregisteredIds);

        $oldTags = $this->rev->tags->pluck('id')->all();
        $newTags = $tags->pluck('id')->all();

        $this->rev->syncTags(array_merge($oldTags, $newTags));
    }

    public function existingPivotalIds()
    {
        $tags = $this->rev->tags;
        $pivotalIds = [];
        foreach ($tags as $tag) {
            $stories = $tag->stories;
            $pIds = $stories->pluck('pivotal_id')->all();
            $pivotalIds = array_merge($pIds, $pivotalIds);
        }

        return $pivotalIds;
    }

    public function storeNewStories($newIds)
    {
        $responses = App::make(Curler::class)->curl($this->rev->project, $newIds, $this->curl);
        (new StoryRepository())->store($responses);

        $now = Carbon::now()->toDateTimeString();
        $greenTags = [
            [
                'greenTagId' => rand(100, 1000) . '-' . $now,
                'greenTagTiming' => $now,
                'stories' => $newIds,
            ]
        ];

        return (new TagRepository())->store($this->rev->project, $greenTags);
    }
}