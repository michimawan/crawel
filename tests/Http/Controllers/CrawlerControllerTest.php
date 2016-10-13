<?php

use Curl\Curl;
use App\Crawler;
use Carbon\Carbon;
use App\Lib\Helper;

class CrawlerControllerTest extends BaseControllerTest
{
    public function test_index_get_todays_stories()
    {
        $projects = (new Helper())->reverseProjectIds(Config::get('pivotal.projects'));

        $yesterdayDatas = factory(Crawler::class, 3)->create([
            'last_updated_at' => json_encode([Helper::sanitizeDate(Carbon::today()->subDay()->toDateTimeString(), ' ')]),
        ]);
        $todayDatas = factory(Crawler::class, 3)->create();

        foreach($projects as $projectName => $project) {
            $ids = array_keys($project);
            foreach($ids as $id) {
                $todayDatas->push(factory(Crawler::class)->create([
                    'project_id' => $id,
                ]));
            }
        }
        $stories = (new Helper)->grouping($projects, $todayDatas);

        $route = route('crawler.index');
        $response = $this->get($route, [])->response;

        $this->assertResponseOk();
        $this->assertEquals('crawler.index', $response->original->getName());
        $this->assertViewHas(['stories', 'projects']);
        $this->assertEquals($stories->pluck('id'), $response->original->stories->pluck('id'));
        $this->assertEquals($projects, $response->original->projects);
    }

    public function test_index_get_yesterday_stories()
    {
        $projects = (new Helper())->reverseProjectIds(Config::get('pivotal.projects'));

        $yesterdayDate = Helper::sanitizeDate(Carbon::today()->subDay()->toDateTimeString(), ' ');
        $yesterdayDatas = factory(Crawler::class, 3)->create([
            'last_updated_at' => json_encode([$yesterdayDate]),
        ]);

        foreach($projects as $projectName => $project) {
            $ids = array_keys($project);
            foreach($ids as $id) {
                factory(Crawler::class)->create([
                    'project_id' => $id,
                ]);
            }
        }
        $stories = (new Helper)->grouping($projects, $yesterdayDatas);

        $route = route('crawler.index');
        $response = $this->get($route, [
            'date' => $yesterdayDate
        ])->response;

        $this->assertResponseOk();
        $this->assertEquals('crawler.index', $response->original->getName());
        $this->assertEquals($stories->pluck('id'), $response->original->stories->pluck('id'));
    }

    public function test_create()
    {
        $route = route('crawler.create');
        $response = $this->get($route, [])->response;
        $this->assertResponseOk();
        $this->assertEquals('crawler.create', $response->original->getName());
        $this->assertViewHas(['options']);
    }

    public function test_store_success()
    {
    	$text = ['stories' => '[#211123] foo'];
        $path = route('crawler.store');
        $response = $this->call('POST', $path, $text);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedToRoute('crawler.index');
    }
}
