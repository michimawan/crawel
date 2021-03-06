<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Lib\Helper;
use App\Models\Revision;
use App\Models\Story;
use App\Models\Tag;

class HelperTest extends BaseLibTest
{
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
        $greenTags = factory(Tag::class, 2)->make([
            'project' => 'foo'
        ]);
        $greenTags->push(
            factory(Tag::class)->make([
                'project' => 'foo2'
            ])
        );
        $greenTags->push(
            factory(Tag::class)->make([
                'project' => 'foo2'
            ])
        );
        $greenTags->push(
            factory(Tag::class)->make([
                'project' => 'foo'
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
            'foo' => $greenTags->where('project', 'foo'),
                'foo2' => $greenTags->where('project', 'foo2'),
            ];

        $this->assertEquals(collect($expected), (new Helper)->grouping($projects, $greenTags));
    }

    public function test_sanitizeDate()
    {
        $date = '2016-11-13T03:49:01Z';
        $expected = '2016-11-13';
        $this->assertEquals($expected, (new Helper)->sanitizeDate($date, 'T'));
    }

    public function test_getSelectedRevisions_will_return_array_of_projects_and_its_input()
    {
        $request = $this->createMock(Request::class);
        $workspaces = array_keys(Config::get('pivotal.projects'));
        $input = [];

        $expected = [];
        foreach ($workspaces as $idx => $workspace) {
            $lowered = strtolower($workspace);
            $inputName = "{$lowered}_revisions";
            $request->expects($this->at($idx))
                ->method('input')
                ->with($inputName)
                ->will($this->returnValue($input));

            $expected[$workspace] = $input;
        }

        $this->assertEquals($expected, Helper::getSelectedRevisions($request));
    }


    public function test_getSelectedGreenTags_will_return_array_of_projects_and_its_input()
    {
        $request = $this->createMock(Request::class);
        $workspaces = array_keys(Config::get('pivotal.projects'));
        $input = [];

        $expected = [];
        foreach ($workspaces as $idx => $workspace) {
            $lowered = strtolower($workspace);
            $inputName = "{$lowered}_tags";
            $request->expects($this->at($idx))
                ->method('input')
                ->with($inputName)
                ->will($this->returnValue($input));

            $expected[$workspace] = $input;
        }

        $this->assertEquals($expected, Helper::getSelectedGreenTags($request));
    }

    public function datas()
    {
        return [
            ['#1 (Des 24, 2015 10:20:46 AM)', 'HIJAU-2015-12-24_10-20-46'],
            ['#2 (Jan 01, 2013 01:10:00 PM)', 'HIJAU-2013-01-01_13-10-00'],
            ['#3 (Feb 28, 2018 12:20:46 PM)', 'HIJAU-2018-02-28_12-20-46'],
            ['#4 (Mar 24, 2015 10:20:46 AM)', 'HIJAU-2015-03-24_10-20-46'],
        ];
    }

    /**
     * @dataProvider datas
     */
    public function test_jenkinsToGitTagging($tag, $expected)
    {
        $this->assertEquals($expected, Helper::jenkinsToGitTagging('foo', $tag));
    }
}
