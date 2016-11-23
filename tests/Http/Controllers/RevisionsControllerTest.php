<?php

use App\Models\Revision;
use App\Models\Tag;
use Carbon\Carbon;

class RevisionsControllerTest extends BaseControllerTest
{
    public function xtest_index()
    {
        $projects = (new Helper())->reverseProjectIds(Config::get('pivotal.projects'));

        $yesterdayDatas = factory(Tag::class, 3)->create([
            'project' => 'foo',
            'created_at' => Carbon::now()->subDay()
        ]);

        $todayDatas = factory(Tag::class, 3)->create([
            'project' => 'foo'
        ]);
        $tag = (new Helper)->grouping($projects, $todayDatas);

        $route = route('revisions.index');
        $response = $this->get($route, [])->response;

        $this->assertResponseOk();
        $this->assertEquals('revisions.index', $response->original->getName());
        $this->assertViewHas(['tag', 'projects']);
        $this->assertEquals($tag->pluck('id'), $response->original->tag->pluck('id'));
        $this->assertEquals($projects, $response->original->projects);
    }

    public function test_create()
    {
        $route = route('revisions.create');
        $response = $this->get($route, [])->response;
        $this->assertResponseOk();
        $this->assertEquals('revisions.create', $response->original->getName());
        $this->assertViewHas(['greenTags', 'projects']);
    }

    public function test_store_success()
    {
        $text = ['revisions' => '[#211123] foo', 'project' => 'foo'];
        $path = route('revisions.store');
        $response = $this->call('POST', $path, $text);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedToRoute('stories.index');
    }

    public function test_store_failed()
    {
        $tag = factory(Tag::class)->create([
            'project' => 'foo'
        ]);
        $text = [
            'foo_tags' => $tag->id,
        ];
        $path = route('revisions.store');
        $response = $this->call('POST', $path, $text);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedToRoute('revisions.create');
    }

    public function test_store_empty_field()
    {
        $text = [];
        $path = route('revisions.store');
        $revisionCount = Revision::count();
        $response = $this->call('POST', $path, $text);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedToRoute('stories.index');

        // this part make sure that empty field won't add data on DB
        $this->assertEquals($revisionCount, Revision::count());
    }
}
