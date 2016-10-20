<?php

use Curl\Curl;
use App\Story;
use Carbon\Carbon;
use App\Lib\Helper;

class StoriesControllerTest extends BaseControllerTest
{
    public function test_index_get_todays_stories()
    {
        $projects = (new Helper())->reverseProjectIds(Config::get('pivotal.projects'));

        $yesterdayDatas = factory(Story::class, 3)->create([
            'last_updated_at' => json_encode([Helper::sanitizeDate(Carbon::today()->subDay()->toDateTimeString(), ' ')]),
        ]);
        $todayDatas = factory(Story::class, 3)->create();

        foreach($projects as $projectName => $project) {
            $ids = array_keys($project);
            foreach($ids as $id) {
                $todayDatas->push(factory(Story::class)->create([
                    'project_id' => $id,
                ]));
            }
        }
        $stories = (new Helper)->grouping($projects, $todayDatas);

        $route = route('stories.index');
        $response = $this->get($route, [])->response;

        $this->assertResponseOk();
        $this->assertEquals('stories.index', $response->original->getName());
        $this->assertViewHas(['stories', 'projects']);
        $this->assertEquals($stories->pluck('id'), $response->original->stories->pluck('id'));
        $this->assertEquals($projects, $response->original->projects);
    }

    public function test_index_get_yesterday_stories()
    {
        $projects = (new Helper())->reverseProjectIds(Config::get('pivotal.projects'));

        $yesterdayDate = Helper::sanitizeDate(Carbon::today()->subDay()->toDateTimeString(), ' ');
        $yesterdayDatas = factory(Story::class, 3)->create([
            'last_updated_at' => json_encode([$yesterdayDate]),
        ]);

        foreach($projects as $projectName => $project) {
            $ids = array_keys($project);
            foreach($ids as $id) {
                factory(Story::class)->create([
                    'project_id' => $id,
                ]);
            }
        }
        $stories = (new Helper)->grouping($projects, $yesterdayDatas);

        $route = route('stories.index');
        $response = $this->get($route, [
            'date' => $yesterdayDate
        ])->response;

        $this->assertResponseOk();
        $this->assertEquals('stories.index', $response->original->getName());
        $this->assertEquals($stories->pluck('id'), $response->original->stories->pluck('id'));
    }

    public function test_create()
    {
        $route = route('stories.create');
        $response = $this->get($route, [])->response;
        $this->assertResponseOk();
        $this->assertEquals('stories.create', $response->original->getName());
        $this->assertViewHas(['options']);
    }

    public function test_store_success()
    {
    	$text = ['stories' => '[#211123] foo'];
        $path = route('stories.store');
        $response = $this->call('POST', $path, $text);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedToRoute('stories.index');
    }

    public function test_store_failed()
    {
        $text = [];
        $path = route('stories.store');
        $response = $this->call('POST', $path, $text);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedToRoute('stories.create');
    }

    public function test_store_empty_field()
    {
        $text = [];
        $path = route('stories.store');
        $response = $this->call('POST', $path, $text);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedToRoute('stories.create');
    }
}
