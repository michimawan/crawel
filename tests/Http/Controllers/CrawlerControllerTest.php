<?php

use Curl\Curl;
use App\Crawler;
use Carbon\Carbon;
use App\Lib\Parser;

class CrawlerControllerTest extends BaseControllerTest
{
    public function test_index()
    {
        $projects = (new Parser())->reverseProjectIds(Config::get('pivotal.projects'));

        $yesterdayDatas = factory(Crawler::class, 3)->create([
            'updated_at' => Carbon::today()->subDay(),
        ]);

        foreach($projects as $projectName => $project) {
            $ids = array_keys($project);
            foreach($ids as $id) {
                factory(Crawler::class)->create([
                    'project_id' => $id,
                ]);
            }
        }
        $stories = Crawler::where('updated_at', '>=', Carbon::today())->get();
        $stories = (new Parser)->grouping($projects, $stories);

        $route = route('crawler.index');
        $response = $this->get($route, [])->response;

        $this->assertResponseOk();
        $this->assertEquals('crawler.index', $response->original->getName());
        $this->assertViewHas(['stories', 'projects']);
        $this->assertEquals($stories, $response->original->stories);
        $this->assertEquals($projects, $response->original->projects);
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
