<?php

use App\Models\Revision;
use App\Models\Tag;
use App\Lib\Helper;
use Carbon\Carbon;

class RevisionsControllerTest extends BaseControllerTest
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

        $route = route('revisions.index');
        $response = $this->get($route, [])->response;

        $this->assertResponseOk();
        $this->assertEquals('revisions.index', $response->original->getName());
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

        $route = route('revisions.index');
        $response = $this->get($route, [
            'date' => $yesterdayDate
        ])->response;

        $this->assertResponseOk();
        $this->assertEquals('revisions.index', $response->original->getName());
        $this->assertEquals($rev->pluck('id'), $response->original->rev->pluck('id'));
    }

    public function test_create()
    {
        $route = route('revisions.create');
        $response = $this->get($route, [])->response;
        $this->assertResponseOk();
        $this->assertEquals('revisions.create', $response->original->getName());
        $this->assertViewHas(['options']);
    }
}
