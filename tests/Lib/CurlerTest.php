<?php

use App\Lib\Curler;
use Curl\Curl;
use Config;

class CurlerTest extends BaseLibTest
{
	public function test_getProjectIds()
	{
		$expected = Config::get('pivotal.projects.foo.projectIds');

		$this->assertEquals($expected, (new Curler)->getProjectIds('foo'));
	}

}
