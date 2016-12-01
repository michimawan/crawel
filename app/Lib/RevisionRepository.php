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

    private function isNotValidSelectedGreenTags($tags) {
        return is_null($tags) || count($tags) == 0;
    }

    public static function getProperties(Request $request)
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
}
