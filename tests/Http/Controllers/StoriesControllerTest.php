<?php

use Curl\Curl;
use App\Models\Revision;
use Carbon\Carbon;
use App\Lib\Helper;

class StoriesControllerTest extends BaseControllerTest
{
    public function test_index_get_todays_tag()
    {
        $projects = (new Helper())->reverseProjectIds(Config::get('pivotal.projects'));

        $yesterdayDatas = factory(Revision::class, 3)->create([
            'project' => 'foo',
            'created_at' => Carbon::now()->subDay()
        ]);

        $todayDatas = factory(Revision::class, 3)->create([
            'project' => 'foo'
        ]);
        $rev = (new Helper)->grouping($projects, $todayDatas);

        $route = route('stories.index');
        $response = $this->get($route, [])->response;

        $this->assertResponseOk();
        $this->assertEquals('stories.index', $response->original->getName());
        $this->assertViewHas(['rev', 'projects']);
        $this->assertEquals($rev->pluck('id'), $response->original->rev->pluck('id'));
        $this->assertEquals($projects, $response->original->projects);
    }

    public function test_index_get_yesterday_rev()
    {
        $projects = (new Helper())->reverseProjectIds(Config::get('pivotal.projects'));

        $yesterdayDate = Helper::sanitizeDate(Carbon::today()->subDay()->toDateTimeString(), ' ');
        $yesterdayDatas = factory(Revision::class, 3)->create([
            'project' => 'foo'
        ]);

        $rev = (new Helper)->grouping($projects, $yesterdayDatas);

        $route = route('stories.index');
        $response = $this->get($route, [
            'date' => $yesterdayDate
        ])->response;

        $this->assertResponseOk();
        $this->assertEquals('stories.index', $response->original->getName());
        $this->assertEquals($rev->pluck('id'), $response->original->rev->pluck('id'));
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
        $text = ['stories' => '[#211123] foo', 'project' => 'foo'];
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
