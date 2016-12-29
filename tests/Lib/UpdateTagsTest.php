<?php

use Curl\Curl;
use App\Models\Revision;
use App\Models\Story;
use App\Models\Tag;
use App\Lib\UpdateTags;
use App\Lib\Curler;

class UpdateTagsTest extends BaseLibTest
{
    public function setUp()
    {
        parent::setUp();
        $this->rev = factory(Revision::class)->create([
            'project' => 'foo'
        ]);
        $this->stories = factory(Story::class, 2)->create();
        $this->tag = factory(Tag::class)->create();

        $this->tag->syncStories($this->stories->pluck('id')->all());

        $this->rev->syncTags([$this->tag->id]);
    }

    public function test_existingPivotalIds()
    {
        $text = "";
        $ids = (new UpdateTags($this->rev, $text))->existingPivotalIds();
        $this->assertEquals($this->stories->pluck('pivotal_id')->all(), $ids);
    }

    public function test_storeNewStories()
    {
        $text = "";
        $ids = [
            rand(1000, 10000),
            rand(1000, 10000),
        ];
        $response = [
            1234 => [
                (object) [
                    'kind' => 'story',
                    'id' => $ids[0],
                    'created_at' => '2016-09-09T07:54:22Z',
                    'updated_at' => '2016-09-13T03:49:01Z',
                    'accepted_at' => '2016-09-09T08:58:26Z',
                    'story_type' => 'chore',
                    'name' => 'refactoring',
                    'current_state' => 'accepted',
                    'url' => 'https://www.pivotaltracker.com/story/show/1200',
                    'project_id' => 1234,
                ],
            ],
            2222 => [
                (object) [
                    'kind' => 'story',
                    'id' => $ids[1],
                    'created_at' => '2016-09-09T07:54:22Z',
                    'updated_at' => '2016-09-13T03:49:01Z',
                    'accepted_at' => '2016-09-09T08:58:26Z',
                    'story_type' => 'chore',
                    'name' => 'refactoring',
                    'current_state' => 'accepted',
                    'url' => 'https://www.pivotaltracker.com/story/show/1201',
                    'project_id' => 2222,
                ],
            ],
        ];
        $tagCount = Tag::count();
        $storiesCount = Story::count();

        $curl = Mockery::mock(Curl::class)->makePartial();
        $curl->shouldReceive('setHeader')
            ->once()
            ->with('X-TrackerToken', Config::get('pivotal.apiToken'));
        App::instance(Curl::class, $curl);
        $curler = Mockery::mock(Curler::class)->makePartial();
        $curler->shouldReceive('curl')
            ->once()
            ->with($this->rev->project, $ids, $curl)
            ->andReturn($response);
        App::instance(Curler::class, $curler);

        $result = (new UpdateTags($this->rev, $text))->storeNewStories($ids);

        $savedIds = $result->first()->stories->pluck('pivotal_id')->all();
        sort($ids);
        sort($savedIds);
        $this->assertEquals($ids, $savedIds);
        $this->assertEquals($tagCount + 1, Tag::count());
        $this->assertEquals($storiesCount + 2, Story::count());
    }
}