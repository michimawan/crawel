<?php

class WebTest extends BaseRouteTest
{
    public function test_index()
    {
        $response = $this->call('GET', '/', []);
        $this->assertCurrentRouteName('crawler.index');    
        $this->assertCurrentRouteAction('CrawlersController@index');
    }
}
