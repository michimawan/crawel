<?php

use App\Lib\Parser;
use App\Crawler;

class ParseStoriesTest extends BaseLibTest
{
    public function test_parse_found_match()
    {
        $stories = '[#212] foo';
        $expected = [212];

        $parser = new Parser();
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

        $parser = new Parser();
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
        $this->assertEquals($expected, (new Parser())->reverseProjectIds($config));
    }

    public function test_grouping()
    {
        $stories = factory(Crawler::class, 2)->make([
            'project_id' => 2
        ]);
        $stories->push(
            factory(Crawler::class)->make([
                'project_id' => 3
            ])
        );
        $stories->push(
            factory(Crawler::class)->make([
                'project_id' => 4
            ])
        );
        $stories->push(
            factory(Crawler::class)->make([
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

        $this->assertEquals(collect($expected), (new Parser)->grouping($projects, $stories));
    }
}
