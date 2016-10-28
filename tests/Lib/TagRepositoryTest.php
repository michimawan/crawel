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
        $tagRepo->store($this->data);

        $this->assertEquals($tagCount + 2, Tag::count());
        $this->assertEquals(5, Tag::where('code', '#1587')->first()->stories->count());
        $this->assertEquals($this->stories1->pluck('id')->all(), Tag::where('code', '#1587')->first()->stories->pluck('id')->all());
    }

    public function test_store_same_greenTag_not_throw_exception_and_no_duplicating_row()
    {
        $tagRepo = new TagRepository();
        $tagRepo->store($this->data);
        $tagCount = Tag::count();
        $tagRepo->store($this->data);
        $this->assertEquals($tagCount, Tag::count());
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
        $tagRepo->store($data);

        $tag = Tag::where('code', '#1585')->first();
        $storyCount = $tag->stories->count();

        $data['#1585 (Oct 20, 2016 5:23:39 PM)']['stories'] = $this->stories2->pluck('pivotal_id')->all();
        $tagRepo->store($data);
        $this->assertEquals(3, Tag::where('code', '#1585')->first()->stories->count());
    }

    public function test_getByDate_return_expected_greenTag()
    {
        $tag = factory(Tag::class)->create();
        $tagRepo = new TagRepository();
    }

    public function xtest_getByDate_when_no_date_send_return_today()
    {
    }
}
