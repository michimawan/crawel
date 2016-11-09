<?php

use Carbon\Carbon;

use App\Lib\Helper;
use App\Models\Story;
use App\Models\Tag;
use App\Lib\TagRepository;

class TagRepositoryTest extends BaseLibTest
{
    public function setUp()
    {
        parent::setUp();

        $this->stories1 = factory(Story::class, 5)->create();

        $this->stories2 = factory(Story::class, 3)->create();
        $this->data = [
            '#1587 (Oct 20, 2016 5:23:39 PM)' => [
                'greenTagId' => '#1587',
                'greenTagTiming' => 'Oct 20, 2016 5:23:39 PM',
                'stories' => $this->stories1->pluck('pivotal_id')->all()
            ],
            '#1586 (Oct 20, 2016 5:23:39 PM)' => [
                'greenTagId' => '#1586',
                'greenTagTiming' => 'Oct 20, 2016 5:23:39 PM',
                'stories' => $this->stories2->pluck('pivotal_id')->all()
            ]
        ];
    }

    public function test_store()
    {
        $tagCount = Tag::count();
        $tagRepo = new TagRepository();
        $tagRepo->store('foo', $this->data);

        $this->assertEquals($tagCount + 2, Tag::count());
        $this->assertEquals(5, Tag::where('code', '#1587')->first()->stories->count());

        $expected = $this->stories1->sortBy('id')->pluck('id')->all();
        $results = Tag::where('code', '#1587')->first()->stories->sortBy('id')->pluck('id')->all();
        $this->assertEquals($expected, $results);
    }

    public function test_store_dont_store_when_story_ids_is_not_persisted()
    {
        $data = [
            '#1586 (Oct 20, 2016 5:23:39 PM)' => [
                'greenTagId' => '#1585',
                'greenTagTiming' => 'Oct 20, 2016 5:23:39 PM',
                'stories' => [1000000, 9999999]
            ]
        ];
        $tagCount = Tag::count();
        $tagRepo = new TagRepository();
        $tagRepo->store('foo', $data);
        $this->assertEquals($tagCount, Tag::count());
    }

    public function test_store_same_greenTag_not_throw_exception_and_no_duplicating_row()
    {
        $tagRepo = new TagRepository();
        $tagRepo->store('foo', $this->data);
        $tagCount = Tag::count();
        $tagRepo->store('foo', $this->data);
        $this->assertEquals($tagCount, Tag::count());
    }

    public function test_store_not_override_greenTagId_from_other_workspace()
    {
        $tagRepo = new TagRepository();
        $tagRepo->store('foo', $this->data);
        $tagCount = Tag::count();
        $tagRepo->store('bar', $this->data);
        $this->assertEquals($tagCount + 2, Tag::count());
    }

    public function test_store_same_greenTag_should_update_stories_related_to_it()
    {
        $data = [
            '#1585 (Oct 20, 2016 5:23:39 PM)' => [
                'greenTagId' => '#1585',
                'greenTagTiming' => 'Oct 20, 2016 5:23:39 PM',
                'stories' => $this->stories1->pluck('pivotal_id')->all()
            ]
        ];
        $tagRepo = new TagRepository();
        $tagRepo->store('foo', $data);

        $tag = Tag::where('code', '#1585')->first();
        $storyCount = $tag->stories->count();

        $data['#1585 (Oct 20, 2016 5:23:39 PM)']['stories'] = $this->stories2->pluck('pivotal_id')->all();
        $tagRepo->store('foo', $data);
        $this->assertEquals(3, Tag::where('code', '#1585')->first()->stories->count());
    }

    public function test_getByDate_return_expected_greenTag()
    {
        $yesterday = Carbon::now()->subDay();
        $tag = factory(Tag::class)->create([
            'created_at' => $yesterday,
        ]);
        factory(Tag::class)->create();
        $tagRepo = new TagRepository();

        $yesterdayDate = Helper::sanitizeDate(Carbon::today()->subDay()->toDateTimeString(), ' ');
        $this->assertEquals(1, $tagRepo->getByDate($yesterdayDate)->count());
        $this->assertEquals($tag->id, $tagRepo->getByDate($yesterdayDate)->first()->id);
    }

    public function test_getByDate_when_no_date_send_return_today()
    {
        $yesterday = Carbon::now()->subDay();
        factory(Tag::class)->create([
            'created_at' => $yesterday,
        ]);
        $tag = factory(Tag::class)->create();
        $tagRepo = new TagRepository();

        $yesterdayDate = Helper::sanitizeDate(Carbon::today()->subDay()->toDateTimeString(), ' ');
        $this->assertEquals(1, $tagRepo->getByDate()->count());
        $this->assertEquals($tag->id, $tagRepo->getByDate()->first()->id);
    }
}