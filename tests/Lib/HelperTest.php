<?php

use Carbon\Carbon;
use App\Lib\Helper;
use App\Story;

class HelperTest extends BaseLibTest
{
    public function test_parse_found_match()
    {
        $stories = '[#212] foo';
        $expected = [212];

        $parser = new Helper();
        $this->assertEquals($expected, $parser->parse($stories));

        $stories = <<<STRING
[#123] foo
[ref #1234] foo
[finished #12345] foo
[#123456] foo
[#123456adfa] foo
STRING;
        $expected = [
            123, 1234, 12345, 123456
        ];
        $this->assertEquals($expected, $parser->parse($stories));
    }

    public function test_parse_not_found_matches()
    {
        $stories = '[212]';
        $expected = [];

        $parser = new Helper();
        $this->assertEquals($expected, $parser->parse($stories));
    }

    public function test_reverseProjectIds()
    {
        $config = [
            'foo' => [
                'bar-1' => 2,
                'bar-2' => 4,
            ],
            'foo2' => [
                'bar-1' => 3,
                'bar-2' => 5,
            ]
        ];

        $expected = [
            'foo' => [
                2 => 'bar-1',
                4 => 'bar-2',
            ],
            'foo2' => [
                3 => 'bar-1',
                5 => 'bar-2',
            ]
        ];
        $this->assertEquals($expected, (new Helper())->reverseProjectIds($config));
    }

    public function test_grouping()
    {
        $stories = factory(Story::class, 2)->make([
            'project_id' => 2
        ]);
        $stories->push(
            factory(Story::class)->make([
                'project_id' => 3
            ])
        );
        $stories->push(
            factory(Story::class)->make([
                'project_id' => 4
            ])
        );
        $stories->push(
            factory(Story::class)->make([
                'project_id' => 5
            ])
        );

        $projects = [
            'foo' => [
                2 => 'bar-1',
                4 => 'bar-2',
            ],
            'foo2' => [
                3 => 'bar-1',
                5 => 'bar-2',
            ]
        ];

        $expected = [
            'foo' => $stories->whereIn('project_id', [2, 4]),
            'foo2' => $stories->whereIn('project_id', [3, 5]),
        ];

        $this->assertEquals(collect($expected), (new Helper)->grouping($projects, $stories));
    }

    public function test_prepareForSheet_return_correct_array()
    {
        $responses = [
            1234 => [
                (object) [
                    'kind' => 'story',
                    'id' => 1200,
                    'created_at' => '2016-09-09T07:54:22Z',
                    'updated_at' => '2016-09-13T03:49:01Z',
                    'accepted_at' => '2016-09-09T08:58:26Z',
                    'story_type' => 'chore',
                    'name' => 'refactoring',
                    'current_state' => 'accepted',
                    'url' => 'https://www.pivotaltracker.com/story/show/1200',
                    'project_id' => 1234,
                ],
                (object) [
                    'kind' => 'story',
                    'id' => 1300,
                    'created_at' => '2016-09-09T07:54:22Z',
                    'updated_at' => '2016-09-13T03:49:01Z',
                    'accepted_at' => '2016-09-09T08:58:26Z',
                    'story_type' => 'bug',
                    'name' => 'refactoring 2',
                    'current_state' => 'delivered',
                    'url' => 'https://www.pivotaltracker.com/story/show/1300',
                    'project_id' => 1234,
                ],
            ],
            2222 => [
                (object) [
                    'kind' => 'story',
                    'id' => 1301,
                    'created_at' => '2016-09-09T07:54:22Z',
                    'updated_at' => '2016-09-13T03:49:01Z',
                    'accepted_at' => '2016-09-09T08:58:26Z',
                    'story_type' => 'feature',
                    'estimate' => 2,
                    'name' => 'refactoring 2',
                    'current_state' => 'rejected',
                    'url' => 'https://www.pivotaltracker.com/story/show/1301',
                    'project_id' => 2222,
                ],
            ],
            3333 => [],
        ];

        $date = Carbon::now();
        $project = 'foo';
        $expected = [[
            $date->toDateTimeString(),
            $project,
            "1. [#1200][foo-4] refactoring (chore, accepted) \r\n2. [#1300][foo-4] refactoring 2 (bug, delivered) \r\n3. [#1301][foo-5] refactoring 2 (2 point(s), rejected)"
        ]];

        $this->assertEquals($expected, (new Helper())->prepareForSheet($project, $responses));
    }

    public function test_sanitizeDate()
    {
        $date = '2016-11-13T03:49:01Z';
        $expected = '2016-11-13';
        $this->assertEquals($expected, (new Helper)->sanitizeDate($date, 'T'));
    }
}
