<?php

namespace App\Lib;

class ParseStories
{
	public function parse(string $textToParse = '')
	{
		$pattern = '/#(?P<id>\d+)/i';
		$matches = [];
		$ids = [];

		$found = preg_match_all($pattern, $textToParse, $matches);
		
		if ($found) {
			foreach($matches['id'] as $match) {
				$ids[] = $match;
			}
		}

		return $ids;
	}
}