<?php
namespace App\Lib;

use Config;

class Curler
{

	public function curl($project, $ids, $curl)
	{
		$projectIds = $this->getProjectIds($project);

		$responses = [];
		foreach($projectIds as $pId) {
			$responses[$pId] = $this->fetchData($pId, $ids, $curl);
		}
		return $responses;
	}

	public function fetchData($projectId, $ids, $curl)
	{
		$baseUrl = "https://www.pivotaltracker.com/services/v5/projects/{$projectId}/stories/bulk?";
		$encodedId = urlencode(join(',', $ids));
		$url = $baseUrl . "ids={$encodedId}";
		$curl->get($url);

		return json_decode($curl->response);
	}

	public function getProjectIds($project)
	{
		return Config::get("pivotal.projects.{$project}.projectIds");
	}
}