<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Config;
use View;

use App\Lib\TagRepository;
use App\Lib\Helper;
use App\Models\Tag;

class MailsController extends Controller
{
    public function create()
    {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDay();
        $tag = Tag::where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->get();
        $projects = Config::get('pivotal.projects');
        $projects = (new Helper)->reverseProjectIds($projects);

        $tag = (new Helper)->grouping($projects, $tag);
        return View::make('mails.create', [
            'tag' => $tag,
            'projects' => $projects,
        ]);
    }
}
