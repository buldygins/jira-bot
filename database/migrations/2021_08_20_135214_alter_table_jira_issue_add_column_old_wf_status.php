<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableJiraIssueAddColumnOldWfStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jira_issues', function (Blueprint $table) {
            $table->integer('old_wf_status_id')->nullable()->default(null)->comment('Статус по старому workflow');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jira_issues', function (Blueprint $table) {
            $table->dropColumn('old_wf_status_id');
        });
    }
}
