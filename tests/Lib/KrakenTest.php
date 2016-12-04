<?php

use App\Lib\Kraken;
use App\Lib\Helper;
use App\Lib\Curler;
use Carbon\Carbon;

class KrakenTest extends BaseLibTest
{
    public function test_parseRevisionsLog()
    {
        $text = 'Branch HIJAU-2015-06-02_10-47-05 (at 283fff83f983920kl1923bns8339a7ac7e941) deployed at 2015-06-02T07:23:59 by zen';

        $expected = 'HIJAU-2015-06-02_10-47-05';

        $this->assertEquals($expected, Kraken::parseRevisionsLog($text));

        $text = 'Branch CPICK_HIJAU-2015-06-02_10-47-05 (at 283fff83f983920kl1923bns8339a7ac7e941) deployed at 2015-06-02T07:23:59 by zen';

        $expected = 'CPICK_HIJAU-2015-06-02_10-47-05';

        $this->assertEquals($expected, Kraken::parseRevisionsLog($text));

        $text = '283fff83f983920kl1923bns8339a7ac7e941) deployed at 2015-06-02T07:23:59 by zen';

        $expected = '';

        $this->assertEquals($expected, Kraken::parseRevisionsLog($text));
    }

    public function test_parseGreenTag()
    {
        $text = <<<STR
lalalaa (tag: HIJAU-2014-03-02_14-59-00) [finished #123460] foo was here
bababab (tag: HIJAU-2014-03-02_14-23-00) [finished #123459] foo was here
hohohoo [finished #123458] foo was here
uauauau [finished #123457] foo was here
STR;
        $expected = [
            'HIJAU-2014-03-02_14-59-00',
            'HIJAU-2014-03-02_14-23-00',
        ];

        $this->assertEquals($expected, Kraken::parseGreenTag($text));

        $text = <<<STR
hohohoo [finished #123458] foo was here
uauauau [finished #123457] foo was here
STR;
        $expected = [];

        $this->assertEquals($expected, Kraken::parseGreenTag($text));
    }

    public function test_getDeployedStoryIds()
    {
        $text = <<<STR
lalalaa (tag: HIJAU-2014-03-02_14-59-00) [finished #123460] foo was here
bababab (tag: HIJAU-2014-03-02_14-23-00) [finished #123459] foo was here
hohohoo [finished #123458] foo was here
uauauau [finished #123457] foo was here
eeeeeee [finished #123451] foo was here
lalalaa [#123452]  foo was here
bababab [#123453]  foo was here
hohohoo [#123454] foo was here
uauauau [#123455]  foo was here
eeeeeee [#123456]foo was here
bababab (tag: HIJAU-2014-02-02_14-23-00) [finished #123449] foo was here
hohohoo (tag: HIJAU-2014-02-01_13-23-00) [finished #123440] foo was here
STR;

        $baseRev = 'HIJAU-2014-02-02_14-23-00';
        $expected = [
            123460,
            123459,
            123458,
            123457,
            123456,
            123455,
            123454,
            123453,
            123452,
            123451,
        ];

        $results = Kraken::getDeployedStoryIds($baseRev, $text);
        sort($expected);
        sort($results);

        $this->assertTrue($expected == $results);
    }

    public function test_getStories()
    {
        $workspace = 'foo';
        $storyIds = [];

        $curler = $this->getMockBuilder(Curler::class)
            ->setMethods(['curl'])
            ->getMock();

        $curler->expects($this->once())
            ->method('curl');

        (new Kraken($workspace, '', ''))->getStories($curler, $workspace, $storyIds);
    }

    public function test_print()
    {
        $response = [
            1234 => [
                (object) [
                    'kind' => 'story',
                    'id' => 1200,
                    'created_at' => '2015-09-09T07:54:22Z',
                    'updated_at' => '2015-09-13T03:49:01Z',
                    'accepted_at' => '2015-09-09T08:58:26Z',
                    'story_type' => 'chore',
                    'name' => 'refactoring',
                    'current_state' => 'accepted',
                    'url' => 'https://www.pivotaltracker.com/story/show/1200',
                    'project_id' => 1234,
                ],
                (object) [
                    'kind' => 'story',
                    'id' => 1300,
                    'created_at' => '2015-09-09T07:54:22Z',
                    'updated_at' => '2015-09-13T03:49:01Z',
                    'accepted_at' => '2015-09-09T08:58:26Z',
                    'story_type' => 'feature',
                    'estimate' => 2,
                    'name' => 'refactoring 2',
                    'current_state' => 'accepted',
                    'url' => 'https://www.pivotaltracker.com/story/show/1300',
                    'project_id' => 1234,
                ],
            ],
            2222 => [
                (object) [
                    'kind' => 'story',
                    'id' => 1201,
                    'created_at' => '2015-09-09T07:54:22Z',
                    'updated_at' => '2015-09-13T03:49:01Z',
                    'accepted_at' => '2015-09-09T08:58:26Z',
                    'story_type' => 'bug',
                    'name' => 'refactoring',
                    'current_state' => 'accepted',
                    'url' => 'https://www.pivotaltracker.com/story/show/1201',
                    'project_id' => 2222,
                ],
            ],
            3333 => [],
        ];

        $workspace = 'foo';
        $time = Helper::sanitizeDate(Carbon::today()->toDateTimeString(), ' ');
        $revisions = 'HIJAU-2016-12-02_10-47-05';
        $expected = <<<STR
FOO
Date: {$time}
Revisions: {$revisions}
Get Greentag Time:
Stories:
1. [#1200][foo-4] refactoring (chore)
2. [#1300][foo-4] refactoring 2 (2 point(s))
3. [#1201][foo-5] refactoring (bug)
End Time To Check Stories:
End Time To Run Automate Test:
Time Get Canary:
End Time To Test Canary:
End Time To ELB :
Description: Deploy
STR;

        $result = (new Kraken($workspace, '', ''))->print($revisions, $response);

        $this->assertEquals($expected, $result);
    }
}