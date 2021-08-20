<?php

namespace App\Console\Commands;

use App\Models\JiraIssue;
use App\Models\JiraIssueStatus;
use App\Models\Subscriber;
use Illuminate\Console\Command;
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Project\ProjectService;

class JiraTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jira:tasks {project=WEB : Project key in Jira}';

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
        $projectKey = $this->argument('project');
        $this->info('Start parsing issues from Jira project ' . $projectKey);

//        $user = Subscriber::where('jira_login', '!=', null)->where('api_token', '!=', null)->where('jira_user_id', '!=', null)->get()->first();
        $config = new ArrayConfiguration([
            'jiraHost' => config('app.jira_url'),
            'jiraUser' => 'origamiv@gmail.com',
            'jiraPassword' => 'HIVNRLqy7yjXcDTe3Vu72AF1',
        ]);

        $issueService = new IssueService($config);

        $jql = "project = \"{$projectKey}\"";

        while ($search_result = $issueService->search($jql, 0, 500)) {

            foreach ($search_result->issues as $issue) {
                $status = JiraIssueStatus::where('jiraId',$issue->fields->status->id)->orderBy('order')->first();
                $jiraIssue = JiraIssue::updateOrCreate(['key' => $issue->key], [
                    'key' => $issue->key,
                    'summary' => $issue->fields->summary,
                    'issue_url' => config('app.jira_url') . '/browse/' . $issue->key,
                    'issue_id' => $issue->id,
                    'project_key' => $issue->fields->project->key,
                    'old_wf_status_id' => $status->id,
                ]);
                $jiraIssue->src = (!empty($jiraIssue->src)) ? $jiraIssue->src : $issue;
                $jiraIssue->status_id = (!empty($jiraIssue->status_id)) ? $jiraIssue->status_id : $status->id;
                $jiraIssue->save();
            }
        }

        $this->info(PHP_EOL . 'Successfully exported all tasks');
    }
}
