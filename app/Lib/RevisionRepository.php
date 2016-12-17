<?php
namespace App\lib;

use Carbon\Carbon;
use Config;


use App\Models\Revision;

class RevisionRepository
{
    /**
     * @return Collection of Tag based on date parameter
     *
     * @param string of date, e.g: 2016-01-30
     */
    public function getByDate($date = null)
    {
        if ($date == null) {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->addDay();
        } else {
            $date .= ' 00';
            $startDate = Carbon::createFromFormat('Y-m-d H', $date);
            $endDate = clone $startDate;
            $endDate->addDay();
        }
        return Revision::where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->with('tags', 'tags.stories')->get();
    }
}
