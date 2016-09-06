<?php

class CrawlerControllerTest extends TestCase
{
    public function test_index()
    {
        $route = route('crawler.index');
        $response = $this->get($route, [])->response;

        $this->assertResponseOk();
        $this->assertEquals('crawler.index', $response->original->getName());
        $this->assertViewHas(['stories']);
    }
}
