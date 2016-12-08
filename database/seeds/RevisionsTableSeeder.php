<?php

use Illuminate\Database\Seeder;
use App\Models\Tag;
use App\Models\Story;
use App\Models\Revision;

class RevisionsTableSeeder extends Seeder
{
    public function run()
    {
    	$this->createStories();
    }

    private function createStories()
    {
    	$projects = Config::get('pivotal.projects');
    	foreach ($projects as $workspaceName => $workspace) {
	    	$tagCollection = collect();
    		foreach ($workspace as $projectName => $projectId) {
	    		$story1 = factory(Story::class)->create([
		    		'project_id' => $projectId,
		    	]);
	    		$story2 = factory(Story::class)->create([
		    		'project_id' => $projectId,
		    	]);
	    		$tag = factory(Tag::class)->create([
                    'project' => $workspaceName
                ]);
	    		$tag->syncStories([$story1->id, $story2->id]);

	    		$tagCollection->push($tag);
    		}
    		$rev = factory(Revision::class)->create();
    		$rev->syncTags($tagCollection->pluck('id')->all());
    	}
    }
}
