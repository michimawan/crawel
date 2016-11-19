<?php

use App\Models\Tag;
use App\Lib\Helper;

class MailsControllerTest extends BaseControllerTest
{
    public function test_create()
    {
        $projects = (new Helper())->reverseProjectIds(Config::get('pivotal.projects'));

        $tag = factory(Tag::class, 4)->create();
        $route = route('mails.create');
        $response = $this->get($route, [])->response;

        $this->assertResponseOk();
        $this->assertEquals('mails.create', $response->original->getName());
        $this->assertViewHas(['tag', 'projects']);
        $this->assertEquals($projects, $response->original->projects);
    }
}
