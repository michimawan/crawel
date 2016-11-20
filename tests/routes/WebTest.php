<?php

class WebTest extends BaseRouteTest
{
    public function test_route()
    {
        $response = $this->call('GET', '/', []);
        $this->assertCurrentRouteName('stories.index');
        $this->assertCurrentRouteAction('StoriesController@index');
    }

    public function test_storiesCreate()
    {
        $response = $this->call('GET', '/stories/create', []);
        $this->assertCurrentRouteName('stories.create');
        $this->assertCurrentRouteAction('StoriesController@create');
    }

    public function test_storiesStore()
    {
        $response = $this->call('POST', '/stories/store', []);
        $this->assertCurrentRouteName('stories.store');
        $this->assertCurrentRouteAction('StoriesController@store');
    }

    public function test_mailsCreate()
    {
        $response = $this->call('GET', '/mails/create', []);
        $this->assertCurrentRouteName('mails.create');
        $this->assertCurrentRouteAction('MailsController@create');
    }

    public function test_mailsSend()
    {
        $response = $this->call('POST', '/mails/send', []);
        $this->assertCurrentRouteName('mails.send');
        $this->assertCurrentRouteAction('MailsController@send');
    }
}
