<?php

class WebTest extends BaseRouteTest
{
    public function test_route()
    {
        $response = $this->call('GET', '/', []);
        $this->assertCurrentRouteName('stories.index');    
        $this->assertCurrentRouteAction('StoriesController@index');

        $response = $this->call('GET', '/create', []);
        $this->assertCurrentRouteName('stories.create');    
        $this->assertCurrentRouteAction('StoriesController@create');

        $response = $this->call('POST', '/store', []);
        $this->assertCurrentRouteName('stories.store');   
        $this->assertCurrentRouteAction('StoriesController@store');
    }

}
