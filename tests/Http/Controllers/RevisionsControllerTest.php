<?php

use Illuminate\Http\JsonResponse;

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

    public function test_update()
    {
        $revision = factory(Revision::class)->create([
            'end_time_check_story' => '',
            'end_time_run_automate_test' => '',
            'time_get_canary' => '',
            'time_to_elb' => '',
            'description' => '',
        ]);
        $url = route('revisions.update', ['id' => $revision->id]);
        $params = [
            'green_tags' => '',
            'time_to_check_story' => 'dummy text',
            'end_time_check_story' => 'dummy text',
            'end_time_run_automate_test' => 'dummy text',
            'time_get_canary' => 'dummy text',
            'time_to_finish_test_canary' => 'dummy text',
            'time_to_elb' => 'dummy text',
            'description' => 'dummy text',
            'project' => 'dummy text',
        ];
        $response = $this->post($url, $params, [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ])->response;
        $this->assertResponseOk();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $data = $response->getData();
        $this->assertEquals(true, $data->status);
    }

    public function test_updateFailedRevisionNotFound()
    {
        $url = route('revisions.update', ['id' => rand(100, 1000)]);
        $params = [
            'green_tags' => '',
            'end_time_check_story' => 'dummy text',
            'end_time_run_automate_test' => 'dummy text',
            'time_get_canary' => 'dummy text',
            'time_to_elb' => 'dummy text',
            'description' => 'dummy text',
            'project' => 'dummy text',
        ];
        $response = $this->post($url, $params, [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ])->response;
        $this->assertResponseOk();
        $data = $response->getData();
        $this->assertEquals(false, $data->status);
    }

    public function test_updateFailedCausedEmptyField()
    {
        $url = route('revisions.update', ['id' => rand(100, 1000)]);
        $params = [
            'green_tags' => '',
            'end_time_check_story' => 'dummy text',
            'end_time_run_automate_test' => '',
            'time_get_canary' => '',
            'time_to_elb' => 'dummy text',
            'description' => 'dummy text',
            'project' => 'dummy text',
        ];
        $response = $this->post($url, $params, [
            'X-Requested-With' => 'XMLHttpRequest'
        ])->response;
        $this->assertResponseOk();
        $data = $response->getData();
        $this->assertEquals(false, $data->status);
    }

    public function test_updateFailedCausedNonAjaxRequest()
    {
        $url = route('revisions.update', ['id' => rand(100, 1000)]);
        $params = [
            'green_tags' => '',
            'end_time_check_story' => 'dummy text',
            'end_time_run_automate_test' => 'dummy text',
            'time_get_canary' => 'dummy text',
            'time_to_elb' => 'dummy text',
            'description' => 'dummy text',
            'project' => 'dummy text',
        ];
        $response = $this->post($url, $params)->response;
        $this->assertResponseStatus(401);
    }
}