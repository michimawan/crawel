<?php

use Carbon\Carbon;
use App\Lib\Helper;
use App\Models\Story;
use App\Models\Tag;

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

    public function test_parseGreenTag()
    {
        $text = <<<TEXT
#1587 (Oct 20, 2016 5:23:39 PM)

[finished #132548327][klikdokter] list of sub-channels from Rubrik — pair+himawan+pinto / githubweb
#1586 (Oct 20, 2016 4:42:04 PM)

[ref #130087583] change <?php to <?hh for files containing xhp — pair+ata+fadhil+nofriandi / githubweb
[FIX TEST] being freed from the seeder — pair+himawan+pinto / githubweb
#1583 (Oct 19, 2016 6:41:41 PM)

[ref #131616147] change title: trending topik — pair+himawan+pinto / githubweb
#1582 (Oct 18, 2016 7:36:11 PM)

[finished #132651549] Disable responsive feature on content promotion — pair+enang / githubweb
TEXT;

        $expected = [
            '#1587 (Oct 20, 2016 5:23:39 PM)' => [
                'greenTagId' => '#1587',
                'greenTagTiming' => 'Oct 20, 2016 5:23:39 PM',
            ],
            '#1586 (Oct 20, 2016 4:42:04 PM)' => [
                'greenTagId' => '#1586',
                'greenTagTiming' => 'Oct 20, 2016 4:42:04 PM',
            ],
            '#1583 (Oct 19, 2016 6:41:41 PM)' => [
                'greenTagId' => '#1583',
                'greenTagTiming' => 'Oct 19, 2016 6:41:41 PM',
            ],
            '#1582 (Oct 18, 2016 7:36:11 PM)' => [
                'greenTagId' => '#1582',
                'greenTagTiming' => 'Oct 18, 2016 7:36:11 PM',
            ],
        ];
        $this->assertEquals($expected, Helper::parseGreenTag($text));
    }

    public function test_add_stories_to_green_tag_data()
    {
        $text = <<<TEXT
#1587 (Oct 20, 2016 5:23:39 PM)

[finished #111][klikdokter] list of sub-channels from Rubrik — pair+himawan+pinto / githubweb
#1586 (Oct 20, 2016 4:42:04 PM)

[ref #211] change <?php to <?hh for files containing xhp — pair+ata+fadhil+nofriandi / githubweb
[ref #212] remove all type hints in properties — pair+ata+fadhil+nofriandi / githubweb
[ref #213] remove nullable type hints — pair+ata+fadhil+nofriandi / githubweb
[ref #214] temporarily comment out scalar type hints — pair+ata+fadhil+nofriandi / githubweb
[ref #215] add utility scripts — pair+ata+fadhil+nofriandi / githubweb
#1583 (Oct 19, 2016 6:41:41 PM)

[#311][klikdokter] importer data health topics beserta author dan — firodj / githubweb
[#312][klikdokter] add health topcis slide as multi page health — firodj / githubweb
[#313][klikdokter] remove nonsense KADE_DOMAIN env var and — firodj / githubweb
[finished #314][Consumption] bash cache for related article — pair+ardhan+burhan / githubweb
#1582 (Oct 18, 2016 7:36:11 PM)

[finished #411] Implement Feature Toggle Line Tag Fallback — pair+byan+yahya / githubweb
[finished #412] Disable responsive feature on content promotion — pair+enang / githubweb
TEXT;

        
        $greenTags = [
            '#1587 (Oct 20, 2016 5:23:39 PM)' => [
            ],
            '#1586 (Oct 20, 2016 4:42:04 PM)' => [
            ],
            '#1583 (Oct 19, 2016 6:41:41 PM)' => [
            ],
            '#1582 (Oct 18, 2016 7:36:11 PM)' => [
            ],
        ];
        $expected = [
            '#1587 (Oct 20, 2016 5:23:39 PM)' => [
                'stories' => [111]
            ],
            '#1586 (Oct 20, 2016 4:42:04 PM)' => [
                'stories' => [211, 212, 213, 214, 215]
            ],
            '#1583 (Oct 19, 2016 6:41:41 PM)' => [
                'stories' => [311, 312, 313, 314]
            ],
            '#1582 (Oct 18, 2016 7:36:11 PM)' => [
                'stories' => [411, 412]
            ],
        ];
        $this->assertEquals($expected, Helper::addStoriesToGreenTag($greenTags, $text));
    }

    public function test_add_stories_to_green_tag_data_when_greenTags_has_no_stories()
    {
        $text = <<<TEXT
#1587 (Oct 20, 2016 5:23:39 PM)

[finished #111][klikdokter] list of sub-channels from Rubrik — pair+himawan+pinto / githubweb
#1586 (Oct 20, 2016 4:42:04 PM)

[ref #211] change <?php to <?hh for files containing xhp — pair+ata+fadhil+nofriandi / githubweb
[ref #212] remove all type hints in properties — pair+ata+fadhil+nofriandi / githubweb
[ref #213] remove nullable type hints — pair+ata+fadhil+nofriandi / githubweb
[ref #214] temporarily comment out scalar type hints — pair+ata+fadhil+nofriandi / githubweb
[ref #215] add utility scripts — pair+ata+fadhil+nofriandi / githubweb
#1583 (Oct 19, 2016 6:41:41 PM)

[#311][klikdokter] importer data health topics beserta author dan — firodj / githubweb
[#312][klikdokter] add health topcis slide as multi page health — firodj / githubweb
[#313][klikdokter] remove nonsense KADE_DOMAIN env var and — firodj / githubweb
[finished #314][Consumption] bash cache for related article — pair+ardhan+burhan / githubweb
#1582 (Oct 18, 2016 7:36:11 PM)

TEXT;

        
        $greenTags = [
            '#1587 (Oct 20, 2016 5:23:39 PM)' => [
            ],
            '#1586 (Oct 20, 2016 4:42:04 PM)' => [
            ],
            '#1583 (Oct 19, 2016 6:41:41 PM)' => [
            ],
            '#1582 (Oct 18, 2016 7:36:11 PM)' => [
            ],
        ];
        $expected = [
            '#1587 (Oct 20, 2016 5:23:39 PM)' => [
                'stories' => [111]
            ],
            '#1586 (Oct 20, 2016 4:42:04 PM)' => [
                'stories' => [211, 212, 213, 214, 215]
            ],
            '#1583 (Oct 19, 2016 6:41:41 PM)' => [
                'stories' => [311, 312, 313, 314]
            ],
            '#1582 (Oct 18, 2016 7:36:11 PM)' => [
                'stories' => []
            ],
        ];
        $this->assertEquals($expected, Helper::addStoriesToGreenTag($greenTags, $text));
    }

    public function test_parseText()
    {
        $text = <<<TEXT
#1587 (Oct 20, 2016 5:23:39 PM)

[finished #111][klikdokter] list of sub-channels from Rubrik — pair+himawan+pinto / githubweb
#1586 (Oct 20, 2016 4:42:04 PM)

[ref #211] change <?php to <?hh for files containing xhp — pair+ata+fadhil+nofriandi / githubweb
[ref #212] remove all type hints in properties — pair+ata+fadhil+nofriandi / githubweb
[ref #213] remove nullable type hints — pair+ata+fadhil+nofriandi / githubweb
[ref #214] temporarily comment out scalar type hints — pair+ata+fadhil+nofriandi / githubweb
[ref #215] add utility scripts — pair+ata+fadhil+nofriandi / githubweb
#1583 (Oct 19, 2016 6:41:41 PM)

[#311][klikdokter] importer data health topics beserta author dan — firodj / githubweb
[#312][klikdokter] add health topcis slide as multi page health — firodj / githubweb
[#313][klikdokter] remove nonsense KADE_DOMAIN env var and — firodj / githubweb
[finished #314][Consumption] bash cache for related article — pair+ardhan+burhan / githubweb

TEXT;

        $greenTags = [
            '#1587 (Oct 20, 2016 5:23:39 PM)' => [
                'greenTagId' => '#1587',
                'greenTagTiming' => 'Oct 20, 2016 5:23:39 PM',
                'stories' => [111]
            ],
            '#1586 (Oct 20, 2016 4:42:04 PM)' => [
                'greenTagId' => '#1586',
                'greenTagTiming' => 'Oct 20, 2016 4:42:04 PM',
                'stories' => [211, 212, 213, 214, 215]
            ],
            '#1583 (Oct 19, 2016 6:41:41 PM)' => [
                'greenTagId' => '#1583',
                'greenTagTiming' => 'Oct 19, 2016 6:41:41 PM',
                'stories' => [311, 312, 313, 314]
            ],
        ];

        $storyIds = [111, 211, 212, 213, 214, 215, 311, 312, 313, 314];
        $expected = [$greenTags, $storyIds];
        $this->assertEquals($expected, Helper::parseText($text));
    }
}
