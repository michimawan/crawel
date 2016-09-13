<?php

use Curl\Curl;

class CrawlerControllerTest extends BaseControllerTest
{
    public function xtest_index()
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

    public function test_create()
    {
        $route = route('crawler.create');
        $response = $this->get($route, [])->response;
        $this->assertResponseOk();
        $this->assertEquals('crawler.create', $response->original->getName());
        $this->assertViewHas(['options']);
    }

    public function xtest_store_success()
    {
    	$text = ['stories' => '[#211123] foo'];
        $path = route('crawler.store');
        $response = $this->call('POST', $path, $text);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertRedirectedToRoute('crawler.index');
    }
}
