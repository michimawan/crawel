<?php

use App\Lib\CrawlerRepository;
use App\Lib\ParseStories;
use App\Crawler;

class CrawlerRepositoryTest extends BaseLibTest
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
		$crawlerCount = Crawler::count();

		$crawler = new CrawlerRepository();
		$crawler->store($this->responses);
		$this->assertEquals(4, Crawler::count());
	}

	public function test_store_same_data_should_update_lastUpdateAt_column()
	{
		$crawlerCount = Crawler::count();

		$crawler = new CrawlerRepository();
		$crawler->store($this->responses);

		$response = $this->responses;
		unset($response[3333]);
		unset($response[1234]);

		$lastUpdatedAt = Crawler::where('pivotal_id', 1301)->first()->last_updated_at;

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
		$crawler->store($response);
		$newUpdatedAt = Crawler::where('pivotal_id', 1301)->first()->last_updated_at;
		$this->assertEquals(4, Crawler::count());
		$this->assertNotEquals($lastUpdatedAt, $newUpdatedAt);
	}

	public function test_store_when_failed_to_fetch()
	{
		$crawlerCount = Crawler::count();

		$crawler = new CrawlerRepository();
		$crawler->store([]);
		$this->assertEquals(0, Crawler::count());
	}
}