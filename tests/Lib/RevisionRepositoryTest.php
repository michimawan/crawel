<?php

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Lib\Helper;
use App\Models\Tag;
use App\Models\Revision;
use App\Lib\RevisionRepository;

class RevisionRepositoryTest extends BaseLibTest
{
    public function setUp()
    {
        parent::setUp();
        $this->fields = [
            'child_tag_revisions',
            'end_time_check_story',
            'end_time_run_automate_test',
            'time_get_canary',
            'time_to_elb',
            'description',
        ];
        $this->defaultValues = [
            'child_tag_revisions' => 'foo',
            'end_time_check_story' => 'foo',
            'end_time_run_automate_test' => 'foo',
            'time_get_canary' => 'foo',
            'time_to_elb' => 'foo',
            'description' => 'foo',
        ];
    }

    public function test_store_return_false_when_there_are_some_workspace_that_not_same()
    {
        $properties['foo'] = $this->defaultValues;
        $properties['hello'] = $this->defaultValues;

        $selectedGreenTags['foo'] = [1,  2, 3];
        $selectedGreenTags['bar'] = [1,  2, 3];

        $this->assertFalse((new RevisionRepository)->store($properties, $selectedGreenTags));
    }

    public function test_store_return_false_when_failed_to_save_one_workspace()
    {
        $properties['foo'] = $this->defaultValues;
        $properties['bar'] = $this->defaultValues;
        $properties['bar']['child_tag_revisions'] = null;

        $selectedGreenTags['foo'] = [1,  2, 3];
        $selectedGreenTags['bar'] = [1,  2, 3];

        $this->assertFalse((new RevisionRepository)->store($properties, $selectedGreenTags));
    }

    public function test_store_return_false_when_there_are_same_name_on_child_tag_revisions()
    {
        $properties['foo'] = $this->defaultValues;
        $properties['bar'] = $this->defaultValues;

        $selectedGreenTags['foo'] = [1,  2, 3];
        $selectedGreenTags['bar'] = [1,  2, 3];

        $this->assertFalse((new RevisionRepository)->store($properties, $selectedGreenTags));
    }

    public function test_store_return_true_when_there_are_workspace_without_selectedgreentag()
    {
        $revisionsCount = Revision::count();
        $properties['foo'] = $this->defaultValues;
        $properties['bar'] = $this->defaultValues;
        $properties['bar']['child_tag_revisions'] = 'bar';

        $selectedGreenTags['foo'] = [1,  2, 3];
        $selectedGreenTags['bar'] = null;

        $this->assertTrue((new RevisionRepository)->store($properties, $selectedGreenTags));
        $this->assertEquals($revisionsCount + 1, Revision::count());
    }

    public function test_store_return_true()
    {
        $properties['foo'] = $this->defaultValues;
        $properties['bar'] = $this->defaultValues;
        $properties['bar']['child_tag_revisions'] = 'bar';

        $selectedGreenTags['foo'] = [1,  2, 3];
        $selectedGreenTags['bar'] = [1,  2, 3];

        $this->assertTrue((new RevisionRepository)->store($properties, $selectedGreenTags));
    }

    public function test_getProperties()
    {
        $request = $this->createMock(Request::class);
        $workspaces = array_keys(Config::get('pivotal.projects'));

        $expected = [];
        foreach ($workspaces as $idx => $workspace) {
            $lowered = strtolower($workspace);

            foreach ($this->fields as $idx2 => $field) {
                $fieldName = "{$lowered}_{$field}";
                $position = ($idx * 6) + $idx2;
                $request->expects($this->at($position))
                    ->method('input')
                    ->with($fieldName)
                    ->will($this->returnValue('foo'));
            }

            $expected[$workspace] = $this->defaultValues;
        }

        $this->assertEquals($expected, RevisionRepository::getProperties($request));
    }
}
