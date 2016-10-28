<?php

use Carbon\Carbon;

use App\Lib\StoryRepository;
use App\Lib\Helper;
use App\Models\Story;

class StoryRepositoryTest extends BaseLibTest
{
	public function setUp()
	{
		parent::setUp();

		$this->responses = [
			1234 => [
				(object) [
					'kind' => 'story',
			        'id' => 1200,
			      	'created_at' => '2016-09-09T07:54:22Z',
			      	'updated_at' => '2016-09-13T03:49:01Z',
			      	'accepted_at' => '2016-09-09T08:58:26Z',
			      	'story_type' => 'chore',
			      	'name' => 'refactoring',
			      	'current_state' => 'accepted',
			      	'url' => 'https://www.pivotaltracker.com/story/show/1200',
			      	'project_id' => 1234,
				],
				(object) [
					'kind' => 'story',
			        'id' => 1300,
			      	'created_at' => '2016-09-09T07:54:22Z',
			      	'updated_at' => '2016-09-13T03:49:01Z',
			      	'accepted_at' => '2016-09-09T08:58:26Z',
			      	'story_type' => 'chore',
			      	'name' => 'refactoring 2',
			      	'current_state' => 'accepted',
			      	'url' => 'https://www.pivotaltracker.com/story/show/1300',
			      	'project_id' => 1234,
				],
			],
			2222 => [
				(object) [
					'kind' => 'story',
			        'id' => 1201,
			      	'created_at' => '2016-09-09T07:54:22Z',
			      	'updated_at' => '2016-09-13T03:49:01Z',
			      	'accepted_at' => '2016-09-09T08:58:26Z',
			      	'story_type' => 'chore',
			      	'name' => 'refactoring',
			      	'current_state' => 'accepted',
			      	'url' => 'https://www.pivotaltracker.com/story/show/1201',
			      	'project_id' => 2222,
				],
				(object) [
					'kind' => 'story',
			        'id' => 1301,
			      	'created_at' => '2016-09-09T07:54:22Z',
			      	'updated_at' => '2016-09-13T03:49:01Z',
			      	'accepted_at' => '2016-09-09T08:58:26Z',
			      	'story_type' => 'chore',
			      	'name' => 'refactoring 2',
			      	'current_state' => 'accepted',
			      	'url' => 'https://www.pivotaltracker.com/story/show/1301',
			      	'project_id' => 2222,
				],
			],
			3333 => [],
		];
	}

	public function test_store()
	{
		$storyCount = Story::count();

		$story = new StoryRepository();
		$story->store($this->responses);
		$this->assertEquals(4, Story::count());
	}


	public function test_store_when_failed_to_fetch()
	{
		$storyCount = Story::count();

		$story = new StoryRepository();
		$story->store([]);
		$this->assertEquals(0, Story::count());
	}
}
