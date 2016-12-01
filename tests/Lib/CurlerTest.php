<?php

use App\Lib\Curler;
use Curl\Curl;

class CurlerTest extends BaseLibTest
{
    public function test_curl()
    {
        $mockCurler = $this->getMockBuilder(Curler::class)
            ->setMethods(['getProjectIds', 'fetchData'])
            ->getMock();

        $mockCurler->expects($this->once())
            ->method('getProjectIds')
            ->with('foo')
            ->will($this->returnValue([1, 2]));

        $mockCurler->expects($this->exactly(2))
            ->method('fetchData')
            ->will($this->returnValue([]));

        $curl = new Curl;
        $mockCurler->curl('foo', [], $curl);
    }

    public function test_fetchData_when_projectId_is_not_suplied()
    {
        $mockCurl = $this->getMockBuilder(Curl::class)
            ->setMethods(['get'])
            ->getMock();

        $ids = [1, 2];
        $projectId = null;
        $url = "https://www.pivotaltracker.com/services/v5/projects/{$projectId}/stories/bulk?ids=" . urlencode(join(',', $ids));
        $mockCurl->expects($this->never())
            ->method('get');

        $curler = new Curler();

        $this->assertEquals([], $curler->fetchData($projectId, $ids, $mockCurl));
    }

    public function test_fetchData_when_ids_is_not_suplied()
    {
        $mockCurl = $this->getMockBuilder(Curl::class)
            ->setMethods(['get'])
            ->getMock();

        $ids = [];
        $projectId = 123;
        $url = "https://www.pivotaltracker.com/services/v5/projects/{$projectId}/stories/bulk?ids=" . urlencode(join(',', $ids));
        $mockCurl->expects($this->never())
            ->method('get');

        $curler = new Curler();

        $this->assertEquals([], $curler->fetchData($projectId, $ids, $mockCurl));
    }

    public function test_fetchData_when_ids_is_suplied()
    {
        $mockCurl = $this->getMockBuilder(Curl::class)
            ->setMethods(['get'])
            ->getMock();

        $ids = [1, 2];
        $projectId = 123;
        $url = "https://www.pivotaltracker.com/services/v5/projects/{$projectId}/stories/bulk?ids=" . urlencode(join(',', $ids));
        $mockCurl->expects($this->once())
            ->method('get')
            ->with($url);
        $mockCurl->response = json_encode([]);

        $curler = new Curler();

        $this->assertEquals([], $curler->fetchData($projectId, $ids, $mockCurl));
    }



    public function test_fetchData_response_OK()
    {
        $curlResponse = [
            (object) [
                'kind' => 'story',
                'id' => 1302,
                'created_at' => '2016-09-09T07:54:22Z',
                'updated_at' => '2016-10-13T03:49:01Z',
                'accepted_at' => '2016-09-09T08:58:26Z',
                'story_type' => 'chore',
                'name' => 'refactoring 2',
                'current_state' => 'accepted',
                'url' => 'https://www.pivotaltracker.com/story/show/1301',
                'project_id' => 2222,
            ],
            (object) [
                'kind' => 'story',
                'id' => 1301,
                'created_at' => '2016-09-09T07:54:22Z',
                'updated_at' => '2016-10-13T03:49:01Z',
                'accepted_at' => '2016-09-09T08:58:26Z',
                'story_type' => 'chore',
                'name' => 'refactoring',
                'current_state' => 'accepted',
                'url' => 'https://www.pivotaltracker.com/story/show/1301',
                'project_id' => 2222,
            ],
        ];
        $encodedResponse = json_encode($curlResponse);

        $mockCurl = $this->getMockBuilder(Curl::class)
            ->setMethods(['get'])
            ->getMock();

        $ids = [1, 2];
        $projectId = 123;
        $url = "https://www.pivotaltracker.com/services/v5/projects/{$projectId}/stories/bulk?ids=" . urlencode(join(',', $ids));
        $mockCurl->expects($this->once())
            ->method('get')
            ->with($url);
        $mockCurl->response = $encodedResponse;

        $curler = new Curler();

        $this->assertEquals($curlResponse, $curler->fetchData($projectId, $ids, $mockCurl));
    }

    public function test_fetchData_response_invalid_authentication()
    {
        $curlResponse = (object) [
            'code' => 'invalid_authentication',
            'kind' => 'error',
            'error' => 'Invalid authentication credentials were presented.',
            'possible_fix' => 'Verify that your token value matches what\'s shown on your Profile page in Tracker.',
        ];
        $encodedResponse = json_encode($curlResponse);

        $mockCurl = $this->getMockBuilder(Curl::class)
            ->setMethods(['get'])
            ->getMock();

        $ids = [1, 2];
        $projectId = 123;
        $url = "https://www.pivotaltracker.com/services/v5/projects/{$projectId}/stories/bulk?ids=" . urlencode(join(',', $ids));
        $mockCurl->expects($this->once())
            ->method('get')
            ->with($url);
        $mockCurl->response = $encodedResponse;

        $curler = new Curler();

        $this->assertEquals([], $curler->fetchData($projectId, $ids, $mockCurl));
    }

    public function test_fetchData_response_unauthorized_operation()
    {
        $curlResponse = (object) [
            'code' => 'unauthorized_operation',
            'kind' => 'error',
            'error' => 'Authorization failure.',
            'general_problem' => 'You aren\'t authorized to access the requested resource.',
            'possible_fix' => 'Your project permissions are determined on the Project Membership page. If you are receiving this error you may be trying to access the wrong project, or the project API access is disabled, or someone listed as the project\'s Owner needs to change your membership type.',
        ];
        $encodedResponse = json_encode($curlResponse);

        $mockCurl = $this->getMockBuilder(Curl::class)
            ->setMethods(['get'])
            ->getMock();

        $ids = [1, 2];
        $projectId = 123;
        $url = "https://www.pivotaltracker.com/services/v5/projects/{$projectId}/stories/bulk?ids=" . urlencode(join(',', $ids));
        $mockCurl->expects($this->once())
            ->method('get')
            ->with($url);
        $mockCurl->response = $encodedResponse;

        $curler = new Curler();

        $this->assertEquals([], $curler->fetchData($projectId, $ids, $mockCurl));
    }

    public function test_fetchData_response_null()
    {
        $curlResponse = null;
        $encodedResponse = json_encode($curlResponse);

        $mockCurl = $this->getMockBuilder(Curl::class)
            ->setMethods(['get'])
            ->getMock();

        $ids = [1, 2];
        $projectId = 123;
        $url = "https://www.pivotaltracker.com/services/v5/projects/{$projectId}/stories/bulk?ids=" . urlencode(join(',', $ids));
        $mockCurl->expects($this->once())
            ->method('get')
            ->with($url);
        $mockCurl->response = $encodedResponse;

        $curler = new Curler();

        $this->assertEquals([], $curler->fetchData($projectId, $ids, $mockCurl));
    }

    public function test_getProjectIds()
    {
        $expected = array_values(Config::get('pivotal.projects.foo'));

        $this->assertEquals($expected, (new Curler)->getProjectIds('foo'));
    }

    public function test_getProjectIds_return_empty_array_when_project_not_exist()
    {
        $this->assertEquals([], (new Curler)->getProjectIds('lolols'));
    }

    public function test_getProjectIds_return_empty_array_when_project_empty_string()
    {
        $this->assertEquals([], (new Curler)->getProjectIds());
    }
}
