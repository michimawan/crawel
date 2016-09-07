<?php

use Curl\Curl;

class CrawlerControllerTest extends TestCase
{
    public function test_index()
    {
        $route = route('crawler.index');
        $response = $this->get($route, [])->response;

        // $curl = $this->getMockBuilder(Curl::class)
        // 	->setMethods(['setHeader'])
        // 	->getMock();

        // $curl->expects($this->once())
        // 	->method('setHeader')
        // 	->with('X-TrackerToken', 'foo');

        $this->assertResponseOk();
        $this->assertEquals('crawler.index', $response->original->getName());
        $this->assertViewHas(['stories']);
    }
}
