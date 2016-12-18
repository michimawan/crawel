<?php

use App\Lib\Helper;
use App\Lib\RevisionRepository;
use App\Models\Revision;
use Carbon\Carbon;

class RevisionRepositoryTest extends BaseLibTest
{
    public function test_store()
    {
        $tagRev = "#117 (Nov 13, 2015 12:17:18 PM)";
        $revisionCount = Revision::count();

        $revRepo = new RevisionRepository();
        list($status, $rev) = $revRepo->store($tagRev, 'foo');

        $this->assertTrue($status);
        $this->assertEquals($revisionCount + 1, Revision::count());
    }

    public function test_getByDate_return_expected_greenTag()
    {
        $yesterday = Carbon::now()->subDay();
        $tag = factory(Revision::class)->create([
            'created_at' => $yesterday,
        ]);
        factory(Revision::class)->create();
        $revRepo = new RevisionRepository();

        $yesterdayDate = Helper::sanitizeDate(Carbon::today()->subDay()->toDateTimeString(), ' ');
        $this->assertEquals(1, $revRepo->getByDate($yesterdayDate)->count());
        $this->assertEquals($tag->id, $revRepo->getByDate($yesterdayDate)->first()->id);
    }

    public function test_getByDate_when_no_date_send_return_today()
    {
        $yesterday = Carbon::now()->subDay();
        factory(Revision::class)->create([
            'created_at' => $yesterday,
        ]);
        $tag = factory(Revision::class)->create();
        $revRepo = new RevisionRepository();

        $yesterdayDate = Helper::sanitizeDate(Carbon::today()->subDay()->toDateTimeString(), ' ');
        $this->assertEquals(1, $revRepo->getByDate()->count());
        $this->assertEquals($tag->id, $revRepo->getByDate()->first()->id);
    }
}
