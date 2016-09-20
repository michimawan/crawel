<?php

use Curl\Curl;
use App\Crawler;
use Carbon\Carbon;

class CrawlerControllerTest extends BaseControllerTest
{
    public function test_index()
    {
        $yesterdayDatas = factory(Crawler::class, 3)->create([
            'updated_at' => Carbon::today()->subDay(),
        ]);

        $todayDatas = factory(Crawler::class, 3)->create();
        $todayDataIds = $todayDatas->pluck('id')->all();
        $projectInfo = Config::get('pivotal.projects');

        $route = route('crawler.index');
        $response = $this->get($route, [])->response;

        $this->assertResponseOk();
        $this->assertEquals('crawler.index', $response->original->getName());
        $this->assertViewHas(['stories', 'projectInfo']);
        $this->assertEquals($todayDataIds, $response->original->stories->pluck('id')->all());
        $this->assertEquals($projectInfo, $response->original->projectInfo);
    }

    public function test_create()
    {
        $route = route('crawler.create');
        $response = $this->get($route, [])->response;
        $this->assertResponseOk();
        $this->assertEquals('crawler.create', $response->original->getName());
        $this->assertViewHas(['options']);
    }

    public function xtest_store_success()
    {
    	$text = ['stories' => '[#211123] foo'];
        $path = route('crawler.store');
        $response = $this->call('POST', $path, $text);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedToRoute('crawler.index');
    }
}
