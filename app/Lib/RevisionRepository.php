<?php
namespace App\lib;

use Carbon\Carbon;
use Config;


use App\Models\Revision;
use Exception;

class RevisionRepository
{
    public function store($tagRev = '', $workspace)
    {
        $rev = new Revision;
        $rev->child_tag_revisions = $tagRev;
        $rev->project = $workspace;

        $status = false;
        try {
            $status = $rev->save();
        } catch (Exception $e) {
            $rev = Revision::whereChildTagRevisions($tagRev)
                ->whereProject($workspace)
                ->first();

            if ($rev) {
                $status = true;
            }
        }
        return [$status, $rev];
    }

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
