<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Response;
use Redirect;
use Config;
use View;

use App\Http\Requests\RevisionUpdateRequest;
use App\Lib\StoreRevision;
use App\Lib\RevisionRepository;
use App\Models\Revision;
use App\Models\Tag;
use App\Lib\Helper;

class RevisionsController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date') ? $request->input('date') : null;

        $projects = Config::get('pivotal.projects');
        $projects = (new Helper)->reverseProjectIds($projects);
        $rev = (new RevisionRepository)->getByDate($date);

        $rev = (new Helper)->grouping($projects, $rev);

        return view('revisions.index', [
            'rev' => $rev,
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        $project = Config::get('pivotal.projects');
        $option = [];
        foreach($project as $key => $p) {
            $option[$key] = $key;
        }
        return view('revisions.create', [
            'options' => $option
        ]);
    }

    public function store(Request $request)
    {
        $childTagRev = $request->input('child_tag_rev');
        $workspace = $request->input('workspace');
        if (is_null($childTagRev) || is_null($workspace)) {
            return Redirect::route('revisions.index');
        }
        (new StoreRevision($workspace, $childTagRev))->process();

        return Redirect::route('revisions.index');
    }

    public function update(Request $request, $id)
    {
        $rev = Revision::whereId($id)->first();
        if ($rev) {
            $rev->fill($request->all());
            if ($rev->save()) {
                return Response::json(['status' => true], 200);
            }
        }

        return Response::json(['status' => false], 200);
    }
}
