<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Redirect;
use Config;

use App\Lib\RevisionRepository;
use App\Http\Requests;
use App\Models\Tag;
use App\Lib\Helper;
use View;

class RevisionsController extends Controller
{
    public function index()
    {
    }

    public function create()
    {
        $startDate = Carbon::today()->subDays(7);
        $endDate = Carbon::today()->addDay();
        $tag = Tag::where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->get();
        $projects = Config::get('pivotal.projects');
        $projects = (new Helper)->reverseProjectIds($projects);

        $tag = (new Helper)->grouping($projects, $tag);
        return View::make('revisions.create', [
            'greenTags' => $tag,
            'projects' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $properties = (new RevisionRepository)->getProperties($request);
        $selectedGreenTags = (new Helper)->getSelectedGreenTags($request);
        $status = (new RevisionRepository)->store($properties, $selectedGreenTags);
        if ($status) {
            return Redirect::route('stories.index');
        }
        return Redirect::route('revisions.create');
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
