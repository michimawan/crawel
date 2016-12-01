<?php
namespace App\Lib;

use Config;

class Curler
{
    /**
     * @param array of response
     * the response is grouped by the given projects
     *
     * @param string of projects / workspace
     * @param array of pivotaltracker story ID
     * @param Curl object of Curl class
     */
    public function curl($project = '', $ids = [], $curl)
    {
        $projectIds = $this->getProjectIds($project);

        $responses = [];
        foreach($projectIds as $pId) {
            $responses[$pId] = $this->fetchData($pId, $ids, $curl);
        }
        return $responses;
    }

    /**
     * @return array of response from pivotal API
     *
     * @param integer of project ID on pivotaltracker
     * @param array of searched pivotaltracker story ID
     * @param Curl object
     */
    public function fetchData($projectId = null, $ids = [], $curl)
    {
        if (is_null($projectId) || count($ids) == 0) {
            return [];
        }

        $baseUrl = "https://www.pivotaltracker.com/services/v5/projects/{$projectId}/stories/bulk?";
        $encodedId = urlencode(join(',', $ids));
        $url = $baseUrl . "ids={$encodedId}";
        $curl->get($url);

        $responses = json_decode($curl->response);
        if (is_null($responses) || (isset($responses->kind) && $responses->kind == 'error')) {
            return [];
        }

        return $responses;
    }

    /**
     * @return array of workspace IDs
     *
     * @param string of desired project in config
     */
    public function getProjectIds($project = '')
    {
        $projectIds = Config::get("pivotal.projects.{$project}");
        return is_null($projectIds) ? [] : array_values($projectIds);
    }
}
