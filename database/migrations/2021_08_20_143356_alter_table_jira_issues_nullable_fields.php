<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableJiraIssuesNullableFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jira_issues', function (Blueprint $table) {
            $table->integer('assignee_id')->comment('Id исполнителя (jira_users)')->nullable()->default(null)->change();
            $table->integer('previous_status_id')->comment('Предыдущий статус')->nullable()->default(null)->change();
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
            //
        });
    }
}
