<?php

use App\Lib\Kraken;
use App\Lib\StoreRevision;
use App\Models\Revision;
use Curl\Curl;

class StoreRevisionTest extends BaseLibTest
{
    public function setUp()
    {
        parent::setUp();
        $this->tagRev = "#117 (Nov 13, 2015 12:17:18 PM)";
        $this->workspace = "foo";
        $this->storeRevision = new StoreRevision($this->workspace, $this->tagRev);
    }

    public function test_process_return_null_when_bottom_limit_is_empty_string()
    {
        $storeRevision = Mockery::mock(StoreRevision::class)->makePartial();
        $storeRevision->shouldReceive('createUpperLimit')
            ->once()
            ->andReturn('foo');
        $storeRevision->shouldReceive('getBottomLimit')
            ->once()
            ->andReturn('');

        $result = $storeRevision->process();
        $this->assertFalse($result);
    }

    public function test_process()
    {
        $revision = factory(Revision::class)->make();
        $upperLimit = 'upperLimit';
        $bottomLimit = 'bottomLimit';

        $storeRevision = Mockery::mock(StoreRevision::class)->makePartial();
        $storeRevision->shouldReceive('createUpperLimit')
            ->once()
            ->andReturn($upperLimit);
        $storeRevision->shouldReceive('getBottomLimit')
            ->once()
            ->andReturn($bottomLimit);
        $storeRevision->shouldReceive('getGitLog')
            ->once()
            ->with($upperLimit, $bottomLimit)
            ->andReturn('some string');
        $storeRevision->shouldReceive('storeRevision')
            ->once()
            ->with($upperLimit)
            ->andReturn([true, $revision]);
        $storeRevision->shouldReceive('storeTagsAndStories')
            ->once()
            ->with('some string')
            ->andReturn(true);

        $result = $storeRevision->process();
        $this->assertTrue($result);
    }

    public function test_createUpperLimit()
    {
        $expected = "HIJAU-2015-11-13_12-17-18";

        $this->assertEquals($expected, $this->storeRevision->createUpperLimit($this->tagRev));
    }

    public function test_createUpperLimit_get_empty_string()
    {
        $expected = "";

        $this->assertEquals($expected, $this->storeRevision->createUpperLimit('tag rev asal'));
    }

    public function test_getBottomLimit_when_status_code_OK()
    {
        $response = 'Branch HIJAU-2013-11-10_11-42-57 (at kd382kslsdsd391idx4i5k31d100ed5908c6712) deployed';
        $curl = Mockery::mock(Curl::class)->makePartial();
        $curl->shouldReceive('get')
            ->with(Config::get('pivotal.source-revision.foo'))
            ->once();
        $curl->response = $response;
        $curl->http_status_code = 200;

        $this->storeRevision->curl = $curl;

        $this->assertEquals(Kraken::parseRevisionsLog($response), $this->storeRevision->getBottomLimit());
    }

    public function test_getBottomLimit_when_status_code_not_OK()
    {
        $response = '';
        $curl = Mockery::mock(Curl::class)->makePartial();
        $curl->shouldReceive('get')
            ->with(Config::get('pivotal.source-revision.foo'))
            ->once();
        $curl->response = $response;
        $curl->http_status_code = 322;

        $this->storeRevision->curl = $curl;

        $this->assertEquals('', $this->storeRevision->getBottomLimit());
    }
}
