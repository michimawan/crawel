<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Lib\StoryHelper;
use App\Lib\Helper;
use App\Models\Revision;
use App\Models\Story;
use App\Models\Tag;

class StoryHelperTest extends BaseLibTest
{
    public function test_parse_found_match()
    {
        $stories = '[#212] foo';
        $expected = [212];

        $parser = new StoryHelper();
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

        $parser = new StoryHelper();
        $this->assertEquals($expected, $parser->parse($stories));
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
        $this->assertEquals($expected, StoryHelper::parseGreenTag($text));
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
        $this->assertEquals($expected, StoryHelper::addStoriesToGreenTag($greenTags, $text));
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
        $this->assertEquals($expected, StoryHelper::addStoriesToGreenTag($greenTags, $text));
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
        $this->assertEquals($expected, StoryHelper::parseText($text));
    }

    private function prepareMailData()
    {
        $barRevision = factory(Revision::class)->create([
            'project' => 'bar',
        ]);
        $stories = factory(Story::class, 3)->create([
            'project_id' => 321222,
        ]);
        $barTags = factory(Tag::class, 2)->create([
            'project' => 'bar'
        ]);
        foreach($barTags as $bar) {
            $bar->syncStories($stories->pluck('id')->all());
        }
        $barRevision->syncTags($barTags->pluck('id')->all());
        $barRevision = collect([$barRevision]);

        $fooRevisions = factory(Revision::class, 2)->create([
            'project' => 'foo',
        ]);

        $stories = factory(Story::class, 3)->create([
            'project_id' => 222,
        ]);
        $fooTags = factory(Tag::class, 2)->create([
            'project' => 'foo'
        ]);
        foreach($fooTags as $foo) {
            $foo->syncStories($stories->pluck('id')->all());
        }
        $fooRevisions->first()->syncTags($fooTags->pluck('id')->all());

        $stories = factory(Story::class, 3)->create([
            'project_id' => 222,
        ]);
        $fooTags = factory(Tag::class, 2)->create([
            'project' => 'foo'
        ]);
        foreach($fooTags as $foo) {
            $foo->syncStories($stories->pluck('id')->all());
        }
        $fooRevisions->last()->syncTags($fooTags->pluck('id')->all());

        return [$fooRevisions, $barRevision];
    }

    public function test_prepareForMail()
    {
        list($fooRevisions, $barRevisions) = $this->prepareMailData();
        $selectedRevisions['foo'] = $fooRevisions->pluck('id')->all();
        $selectedRevisions['bar'] = $barRevisions->pluck('id')->all();

        $stories1ToString = $this->storiesToString('foo', $fooRevisions);
        $stories2ToString = $this->storiesToString('bar', $barRevisions);

        $blankSpace = ' ';
        $expected = <<<STRING
{$stories1ToString}{$blankSpace}
{$stories2ToString}{$blankSpace}
STRING;

        $this->assertEquals($expected, StoryHelper::prepareForMail($selectedRevisions));
    }

    public function test_sprepareForMail_when_no_childTagRevs()
    {
        $selectedChildTagRevs = [];
        $workspaces = array_keys(Config::get('pivotal.projects'));
        foreach ($workspaces as $idx => $workspace) {
            $selectedChildTagRevs[$workspace] = [];
        }
        $selectedChildTagRevs['foo'] = null;
        $selectedChildTagRevs['bar'] = null;

        $blankSpace = ' ';
        $expected = <<<STRING
Foo
No Child Tag Revs Today
{$blankSpace}
Bar
No Child Tag Revs Today
{$blankSpace}
STRING;

        $this->assertEquals($expected, StoryHelper::prepareForMail($selectedChildTagRevs));
    }

    private function storiesToString($project, $revisions)
    {
        $projects = Config::get('pivotal.projects');
        $mappedProjectIds = (new Helper)->reverseProjectIds($projects);

        $uppercasedProject = ucwords($project);
        $date = Carbon::today()->toDateString();
        $revisionString = "";
        $storiesString = "";

        $revisionString .= "{$uppercasedProject}\n";
        foreach ($revisions as $revision) {
            $storiesString = "";
            $storiesString .= "Date: {$date}\n";
            $storiesString .= "Revisions: {$revision->child_tag_revisions}\n";
            // $storiesString .= "Get Green Tag Time: {$revision->child_tag_revisions}\n";
            $storiesString .= "Stories:\n";

            $stories = collect();
            foreach ($revision->tags as $greenTag) {
                $stories = $stories->merge($greenTag->stories);
            }
            $stories = $stories->unique(function($item) {
                return $item->pivotal_id;
            });
            foreach ($stories as $idx => $story) {
                $projectName = $mappedProjectIds[$project][$story->project_id];
                $type = $story->story_type == 'chore' || $story->story_type == 'bug' ? $story->story_type : "{$story->point} point(s)";

                $storiesString .= "[#{$story->pivotal_id}][{$projectName}] {$story->title} ({$type}) \n";
            }
            // put here for other child tag rev properties
            $revisionString .= $storiesString . "\n ";
            $revisionString .= "End Time To Check Stories: {$revision->end_time_check_story}\n";
            $revisionString .= "End Time To Run Automate Test: {$revision->end_time_run_automate_test}\n";
            $revisionString .= "Time Get Canary: {$revision->time_get_canary}\n";
            $revisionString .= "End Time To Test Canary: {$revision->time_get_canary}\n";
            $revisionString .= "End Time To ELB : {$revision->time_to_elb}\n";
            $revisionString .= "Description: {$revision->description}\n";

        }

        return $revisionString;
    }
}