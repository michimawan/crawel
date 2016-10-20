<?php

use Carbon\Carbon;

use App\Lib\StoryRepository;
use App\Lib\Helper;
use App\Story;

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

	public function test_store_same_data_should_not_add_lastUpdateAt_column_if_on_the_same_day()
	{
		$storyCount = Story::count();

		$story = new StoryRepository();
		$story->store($this->responses);

		$response = $this->responses;
		unset($response[3333]);
		unset($response[1234]);

		$lastUpdatedAt = json_decode(Story::where('pivotal_id', 1301)->first()->last_updated_at);

		$response[2222] = [
			(object) [
				'kind' => 'story',
		        'id' => 1301,
		      	'created_at' => '2016-09-09T07:54:22Z',
		      	'updated_at' => '2016-10-13T03:49:01Z',
		      	'accepted_at' => '2016-09-09T08:58:26Z',
		      	'story_type' => 'chore',
		      	'name' => 'refactoring 2',
		      	'current_state' => 'accepted',
		      	'url' => 'https://www.pivotaltracker.com/story/show/1301',
		      	'project_id' => 2222,
			],
		];
		$story->store($response);

		$date = Helper::sanitizeDate(Carbon::now()->toDateTimeString(), ' ');
		$newUpdatedAt = json_decode(Story::where('pivotal_id', 1301)->first()->last_updated_at);
		$this->assertEquals(4, Story::count());
		$this->assertEquals(count($lastUpdatedAt), count($newUpdatedAt));
		$this->assertContains($date, $newUpdatedAt);
		$this->assertEquals(1, count($newUpdatedAt));
	}

	public function test_store_same_data_should_add_lastUpdateAt_column_if_on_the_different_day()
	{
		$storyCount = Story::count();

		$story = new StoryRepository();
		$story->store($this->responses);

		$response = $this->responses;
		unset($response[3333]);
		unset($response[1234]);

		$lastUpdatedAt = json_decode(Story::where('pivotal_id', 1301)->first()->last_updated_at);
		Carbon::setTestNow(Carbon::now()->subDay());

		$response[2222] = [
			(object) [
				'kind' => 'story',
		        'id' => 1301,
		      	'created_at' => '2016-09-09T07:54:22Z',
		      	'updated_at' => '2016-10-13T03:49:01Z',
		      	'accepted_at' => '2016-09-09T08:58:26Z',
		      	'story_type' => 'chore',
		      	'name' => 'refactoring 2',
		      	'current_state' => 'accepted',
		      	'url' => 'https://www.pivotaltracker.com/story/show/1301',
		      	'project_id' => 2222,
			],
		];
		$story->store($response);

		$date = Helper::sanitizeDate(Carbon::now()->toDateTimeString(), ' ');
		$newUpdatedAt = json_decode(Story::where('pivotal_id', 1301)->first()->last_updated_at);
		$this->assertEquals(4, Story::count());
		$this->assertNotEquals(count($lastUpdatedAt), count($newUpdatedAt));
		$this->assertContains($date, $newUpdatedAt);
		$this->assertEquals(2, count($newUpdatedAt));

		// store again for other diff day
		Carbon::setTestNow(Carbon::now()->subDays(2));
		$story->store($response);

		$date = Helper::sanitizeDate(Carbon::now()->toDateTimeString(), ' ');
		$newUpdatedAt = json_decode(Story::where('pivotal_id', 1301)->first()->last_updated_at);
		$this->assertEquals(4, Story::count());
		$this->assertContains($date, $newUpdatedAt);
		$this->assertEquals(3, count($newUpdatedAt));
		Carbon::setTestNow();
	}

	public function test_store_when_failed_to_fetch()
	{
		$storyCount = Story::count();

		$story = new StoryRepository();
		$story->store([]);
		$this->assertEquals(0, Story::count());
	}

	public function test_getByDate_return_expected_stories()
	{
		$lastTwoDayDate = Helper::sanitizeDate(Carbon::today()->subDays(2)->toDateTimeString(), ' ');
		$lastTwoDay = factory(Story::class)->create([
			'last_updated_at' => json_encode([$lastTwoDayDate]),
		]);

		$yesterdayDate = Helper::sanitizeDate(Carbon::today()->subDay()->toDateTimeString(), ' ');
		$yesterday = factory(Story::class)->create([
			'last_updated_at' => json_encode([$yesterdayDate]),
		]);

		$todayDate = Helper::sanitizeDate(Carbon::today()->toDateTimeString(), ' ');
		$today = factory(Story::class)->create([
			'last_updated_at' => json_encode([$todayDate]),
		]);

		// 2016-10-13
		$story = new StoryRepository();
		$result = $story->getByDate($yesterdayDate);
		$this->assertEquals(1, $result->count());
		$this->assertEquals($yesterday->id, $result->first()->id);

		$result = $story->getByDate($todayDate);
		$this->assertEquals($today->id, $result->first()->id);
	}

	public function test_getByDate_when_no_date_send_return_today()
	{
		$lastTwoDayDate = Helper::sanitizeDate(Carbon::today()->subDays(2)->toDateTimeString(), ' ');
		$lastTwoDay = factory(Story::class)->create([
			'last_updated_at' => json_encode([$lastTwoDayDate]),
		]);

		$yesterdayDate = Helper::sanitizeDate(Carbon::today()->subDay()->toDateTimeString(), ' ');
		$yesterday = factory(Story::class)->create([
			'last_updated_at' => json_encode([$yesterdayDate]),
		]);

		$todayDate = Helper::sanitizeDate(Carbon::today()->toDateTimeString(), ' ');
		$today1 = factory(Story::class)->create([
			'last_updated_at' => json_encode([$todayDate]),
		]);
		$today2 = factory(Story::class)->create([
			'last_updated_at' => json_encode([$todayDate]),
		]);
		$today3 = factory(Story::class)->create([
			'last_updated_at' => json_encode([$todayDate]),
		]);

		$story = new StoryRepository();
		$result = $story->getByDate();
		$this->assertEquals(3, $result->count());
		$this->assertEquals($today1->id, $result->first()->id);
		$this->assertEquals($today3->id, $result->last()->id);
	}
}