<?php
namespace App\Lib;

use Config;

class Curler
{

	public function curl($project = '', $ids = [], $curl) : array
	{
		$projectIds = $this->getProjectIds($project);

		$responses = [];
		foreach($projectIds as $pId) {
			$responses[$pId] = $this->fetchData($pId, $ids, $curl);
		}
		return $responses;
	}

	public function fetchData($projectId = null, $ids = [], $curl) : array
	{
		if (is_null($projectId) || count($ids) == 0) {
			return [];
		}

		$baseUrl = "https://www.pivotaltracker.com/services/v5/projects/{$projectId}/stories/bulk?";
		$encodedId = urlencode(join(',', $ids));
		$url = $baseUrl . "ids={$encodedId}";
		$curl->get($url);

		$responses = json_decode($curl->response);
		if (isset($responses->kind) && $responses->kind == 'error') {
			return [];
		}

		return $responses;
	}

	public function getProjectIds($project = '') : array
	{
		$projectIds = Config::get("pivotal.projects.{$project}.projectIds");
		return is_null($projectIds) ? [] : array_values($projectIds);
	}
}