<?php
namespace App\Lib;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Config;

use App\Models\Revision;
use App\Models\Tag;

class Helper
{
    /**
     * @return array of reversed projectIds from Config.pivotal.projects
     *
     * @param array of config.pivotal.projects
     */
    public static function reverseProjectIds($groups = [])
    {
        $projectIds = [];
        foreach($groups as $groupName => $group) {
            foreach($group as $projectName => $projectId) {
                $projectIds[$groupName][$projectId] = $projectName;
            }
        }

        return $projectIds;
    }

    /**
     * @return Collection that has been grouped based on workspace
     *
     * @param array of workspace group, based on config.pivotal.projects
     * @param Collection of Revision
     */
    public function grouping($groups, $greenTags)
    {
        $collection = collect();

        foreach($groups as $groupName => $groups) {

            $projectIds = array_keys($groups);
            $filteredStories = $greenTags->where('project', $groupName);

            $collection->put($groupName, $filteredStories);
        }

        return $collection;
    }

    /**
     * @return date 2016-12-31
     *
     * @param string of date, e.g: 2016-12-01 15:03:55
     * @param char separator that will be used
     */
    public static function sanitizeDate($date, $delimeter)
    {
        return substr($date, 0, strpos($date, $delimeter));
    }

    /**
     * @return array of Revision that been selected on mails/create
     *
     * @param Request object from controller
     */
    public static function getSelectedRevisions($request)
    {
        $workspaces = array_keys(Config::get('pivotal.projects'));

        $selectedRevisions = [];
        foreach ($workspaces as $workspace) {
            $lowercased = strtolower($workspace);
            $inputName = "{$lowercased}_revisions";
            $selectedRevisions[$workspace] = $request->input($inputName);
        }

        return $selectedRevisions;
    }

    /**
     * @return array of Tag that been selected on revisions/create
     *
     * @param Request object from controller
     */
    public static function getSelectedGreenTags($request)
    {
        $workspaces = array_keys(Config::get('pivotal.projects'));

        $selectedGreenTags = [];
        foreach ($workspaces as $workspace) {
            $lowercased = strtolower($workspace);
            $inputName = "{$lowercased}_tags";
            $selectedGreenTags[$workspace] = $request->input($inputName);
        }

        return $selectedGreenTags;
    }
}