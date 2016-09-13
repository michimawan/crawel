<?php

class WebTest extends BaseRouteTest
{
    public function test_route()
    {
    	// don't execute it now, since it really calls the API
        // $response = $this->call('GET', '/', []);
        // $this->assertCurrentRouteName('crawler.index');    
        // $this->assertCurrentRouteAction('CrawlersController@index');

        $response = $this->call('GET', '/create', []);
        $this->assertCurrentRouteName('crawler.create');    
        $this->assertCurrentRouteAction('CrawlersController@create');

        $response = $this->call('POST', '/store', []);
        $this->assertCurrentRouteName('crawler.store');   
        $this->assertCurrentRouteAction('CrawlersController@store');
    }

}
