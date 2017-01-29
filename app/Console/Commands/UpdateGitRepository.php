<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Config;

class UpdateGitRepository extends Command
{

    protected $signature = 'update:git_repository-5-mins';
    protected $description = 'This command will update all git specified git repository';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $repos = Config::get('pivotal.repo_path');

        foreach ($repos as $repo) {
            $cmd = "cd {$repo}; git pull --rebase";
            shell_exec("{$cmd} >> /tmp/pull.log 2>&1");
        }
    }
}
