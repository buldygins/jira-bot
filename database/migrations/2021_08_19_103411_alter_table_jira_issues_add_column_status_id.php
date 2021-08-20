<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableJiraIssuesAddColumnStatusId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jira_issues', function (Blueprint $table) {
            $table->integer('status_id');
            $table->integer('previous_status_id');
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
            $table->dropColumn('status_id');
            $table->dropColumn('previous_status_id');
        });
    }
}
