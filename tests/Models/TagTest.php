<?php

use App\Models\Tag;
use App\Models\Story;

class TagTest extends BaseModelTest
{
	public function test_syncStories()
	{
		$stories = factory(Story::class, 2)->create();
		$storyIds = $stories->pluck('id')->all();

		$tag = factory(Tag::class)->create();
		$this->assertEquals(0, $tag->stories()->count());

		$tag->syncStories($storyIds);

		$this->assertEquals(2, $tag->stories()->count());
		$this->assertEquals(2, $tag->cached_stories->count());
	}

	public function test_syncStories_when_story_is_not_persist()
	{
		$storyIds = [100000, 999999];

		$tag = factory(Tag::class)->create();

		$tag->syncStories($storyIds);

		$this->assertEquals(0, $tag->stories->count());
		$this->assertEquals(0, $tag->cached_stories->count());
	}

	public function test_syncStories_when_story_is_not_exist()
	{
		$tag = factory(Tag::class)->create();

		$tag->syncStories([null, null, '']);

		$this->assertEquals(0, $tag->stories->count());
		$this->assertEquals(0, $tag->cached_stories->count());
	}
}