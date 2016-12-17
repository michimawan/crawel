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

    public function test_revisionsIndex()
    {
        $response = $this->call('GET', '/revisions', []);
        $this->assertCurrentRouteName('revisions.index');
        $this->assertCurrentRouteAction('RevisionsController@index');
    }

    public function test_revisionsCreate()
    {
        $response = $this->call('GET', '/revisions/create', []);
        $this->assertCurrentRouteName('revisions.create');
        $this->assertCurrentRouteAction('RevisionsController@create');
    }

    public function test_revisionsStore()
    {
        $response = $this->call('POST', '/revisions', []);
        $this->assertCurrentRouteName('revisions.store');
        $this->assertCurrentRouteAction('RevisionsController@store');
    }

    public function test_revisionsEdit()
    {
        $response = $this->call('GET', '/revisions/1/edit', []);
        $this->assertCurrentRouteName('revisions.edit');
        $this->assertCurrentRouteAction('RevisionsController@edit');
    }

    public function test_revisionsUpdate()
    {
        $response = $this->call('PUT', '/revisions/1', []);
        $this->assertCurrentRouteName('revisions.update');
        $this->assertCurrentRouteAction('RevisionsController@update');
    }

    public function test_revisionsDelete()
    {
        $response = $this->call('DELETE', '/revisions/1', []);
        $this->assertCurrentRouteName('revisions.destroy');
        $this->assertCurrentRouteAction('RevisionsController@destroy');
    }

    public function test_createManualRevisions()
    {
        $response = $this->call('GET', '/revisions/manual-create', []);
        $this->assertCurrentRouteName('revisions.manual_create');
        $this->assertCurrentRouteAction('RevisionsController@manual');
    }

    public function test_storeManualRevisions()
    {
        $response = $this->call('POST', '/revisions/manual-store', []);
        $this->assertCurrentRouteName('revisions.manual_store');
        $this->assertCurrentRouteAction('RevisionsController@storeManual');
    }
}
