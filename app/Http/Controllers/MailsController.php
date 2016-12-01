<?php
namespace App\Http\Controllers;

use Google_Client as GoogleClient;
use Google_Service_Gmail as Gmail;
use Google_Service_Gmail_Message;
use Google_Service_Gmail_Draft;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Redirect;
use Config;
use View;

use App\Lib\TagRepository;
use App\Models\Revision;
use App\Lib\Helper;

class MailsController extends Controller
{
    public function create(Request $request)
    {
        if ($r = $this->checkGmailSession($request)) {
            return $r;
        }

        $startDate = Carbon::today()->subDays(7);
        $endDate = Carbon::today()->addDay();
        $revisions = Revision::where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->get();
        $projects = Config::get('pivotal.projects');
        $projects = (new Helper)->reverseProjectIds($projects);

        $revisions = (new Helper)->grouping($projects, $revisions);
        return View::make('mails.create', [
            'revisions' => $revisions,
            'projects' => $projects,
        ]);
    }

    private function checkGmailSession(Request $request)
    {
        if ($request->session()->has('access_token') &&
            $request->session()->exists('access_token')) {
            return;
        }

        return Redirect::route('mails.oauth');
    }

    public function send(Request $request)
    {
        $selectedRevisions = Helper::getSelectedRevisions($request);
        $stringMessage = Helper::prepareForMail($selectedRevisions);

        $client = $this->setUpGoogleClient();
        $client->setAccessToken($request->session()->get('access_token'));

        $service = new Gmail($client);
        $message = new Google_Service_Gmail_Message();

        $email = strtr(base64_encode($stringMessage), array('+' => '-', '/' => '_'));
        $message->setRaw($email);
        $user = 'me';
        $draft = new Google_Service_Gmail_Draft();
        $draft->setMessage($message);
        try {
            $draft = $service->users_drafts->create($user, $draft);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return Redirect::route('mails.create');
        }
        return Redirect::route('stories.index');
    }

    public function auth(Request $request)
    {
        $code = $request->input('code');

        $client = $this->setUpGoogleClient();
        if (is_null($code)) {
            $auth_url = $client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
        } else {
            $client->authenticate($code);
            session(['access_token' => $client->getAccessToken()]);
            return Redirect::route('mails.create');
        }
    }

    private function setUpGoogleClient()
    {
        $url = route('mails.oauth');
        $scopes = implode(' ', [
            Gmail::MAIL_GOOGLE_COM,
            Gmail::GMAIL_COMPOSE,
        ]);

        $client = new GoogleClient();
        $client->setAuthConfigFile(Config::get('google.client_secret_path'));
        $client->setRedirectUri($url);
        $client->setScopes($scopes);
        $client->setAccessType('offline');

        return $client;
    }
}
