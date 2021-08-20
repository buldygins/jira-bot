<?php

namespace App\Console\Commands;

use App\Models\JiraIssueStatus;
use App\Models\Subscriber;
use Illuminate\Console\Command;
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Project\ProjectService;

class JiraStatusesCommand extends Command
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
    protected $description = 'Parse statuses from Jira';

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
        $this->info('Start parsing statuses from Jira projects...');

        $user = Subscriber::where('jira_login', '!=', null)->where('api_token', '!=', null)->where('jira_user_id', '!=', null)->get()->first();
        $config = new ArrayConfiguration([
            'jiraHost' => config('app.jira_url'),
            'jiraUser' => 'vsmorodinskiy',
            'jiraPassword' => '40SmsDkpLh',
        ]);

        $projectService = new ProjectService($config);

        $projects = $projectService->getAllProjects();

        $bar = $this->output->createProgressBar(count($projects));

        foreach ($projects as $project) {
            $statuses = $projectService->getStatuses($project->id);
            foreach ($statuses as $status_collection) {
                foreach ($status_collection->statuses as $status) {
                    JiraIssueStatus::firstOrCreate(['jiraId' => $status->id, 'name' => $status->name]);
                }
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info(PHP_EOL . 'Successfully exported all statuses');
    }
}
