<?php
namespace App\lib;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Config;
use Log;
use DB;


use App\Models\Revision;
use App\Lib\Helper;
use App\Models\Tag;

class RevisionRepository
{
    /**
     * @return boolean of success save or not
     *
     * @param array of properties that's been grouped based on workspace
     * @param array of selected Revision that's been grouped based on workspace
     */
    public function store($properties, $selectedGreenTags)
    {
        $success = true;
        $workspaces = array_keys(Config::get('pivotal.projects'));

        $diff = array_diff_key($properties, $selectedGreenTags);
        if (count($diff)) {
            return false;
        }

        try {
            foreach ($workspaces as $workspace) {
                DB::beginTransaction();
                $revision = new Revision;
                $revision->fill($properties[$workspace]);
                $revision->project = $workspace;

                if ($this->isNotValidSelectedGreenTags($selectedGreenTags[$workspace])) {
                    DB::rollBack();
                    continue;
                }

                if ($revision->save()) {
                    $revision->syncTags($selectedGreenTags[$workspace]);
                    DB::commit();
                } else {
                    DB::rollBack();
                    $success = false;
                    break;
                }
            }

            return $success;
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $success = false;

            return $success;
        }
    }

    /**
     * @return boolean of valid or not the selected greenTags array
     *
     * @param array of selectedGreenTags
     */
    private function isNotValidSelectedGreenTags($tags) {
        return is_null($tags) || count($tags) == 0;
    }

    /**
     * @return array of properties of Revision model grouped based on workspace
     *
     * @param Request from controller
     */
    public static function getProperties($request)
    {
        $fields = [
            'child_tag_revisions',
            'end_time_check_story',
            'end_time_run_automate_test',
            'time_get_canary',
            'time_to_elb',
            'description',
        ];
        $workspaces = array_keys(Config::get('pivotal.projects'));

        $properties = [];
        foreach ($workspaces as $workspace) {
            $lowercased = strtolower($workspace);
            $tmp = [];
            foreach ($fields as $field) {
                $inputName = "{$lowercased}_{$field}";
                $tmp[$field] = $request->input($inputName);
            }

            $properties[$workspace] = $tmp;
        }

        return $properties;
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
