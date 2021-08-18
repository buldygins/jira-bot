<?php

namespace App\Console\Commands;

use App\Models\JiraIssueStatus;
use App\Models\Subscriber;
use Illuminate\Console\Command;
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Project\ProjectService;

class ParseAllStatusesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jira:statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $user = Subscriber::where('jira_login','!=',null)->where('api_token','!=',null)->where('jira_user_id','!=',null)->get()->first();
        $projectService = new ProjectService(new ArrayConfiguration([
            'jiraHost' => env('JIRA_URL'),
            'jiraUser' => $user->jira_login,
            'jiraPassword' => $user->api_token,
        ]));

        $projects = $projectService->getAllProjects();
        foreach ($projects as $project){
            $statuses = $projectService->getStatuses($project->id);
            foreach ($statuses as $status_collection){
                foreach ($status_collection->statuses as $status) {
                JiraIssueStatus::firstOrCreate(['jiraId' => $status->id, 'name' => $status->name]);
                }
            }
        }
        $this->info('successfully exported all statuses');
    }
}
