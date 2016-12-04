<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Kraken;

class LogGreenTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'print:latest-revisions
                            {workspace : Workspace name, based on config.pivotal}
                            {repo : Path to repository, e.g: ~/myRepo }
                            {baseRevUrl : Source of last revisions that deployed, e.g: www.lastRev.com}
                            {--wet-run : Wet run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print Latest Revisions that will be deployed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $rawBaseRevision = $this->getLastRev();
        $gitLogs = $this->getCommits();

        $this->render($rawBaseRevision, $gitLogs);
    }

    private function render($baseRevision, $gitLogs)
    {
        $workspace = $this->argument('workspace');
        echo (new Kraken($workspace, $baseRevision, $gitLogs))->execute();
    }

    private function getLastRev()
    {
        $baseRevUrl = $this->argument('baseRevUrl');

        return exec("curl {$baseRevUrl}");
    }

    private function getCommits()
    {
        $gitCommand = "git log --decorate --pretty=oneline --abbrev-commit";
        $fileOutput = "commits.log";
        $repo = $this->argument('repo');
        $commits = shell_exec("cd {$repo}; {$gitCommand}");
        return $commits;
    }
}