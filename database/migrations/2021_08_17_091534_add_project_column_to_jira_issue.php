<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectColumnToJiraIssue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jira_issues', function (Blueprint $table) {
            $table->string('project_key')->nullable()->comment('Проект');
        });

        Schema::table('logs', function (Blueprint $table) {
            $table->string('project_key')->nullable()->comment('Проект');
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
            $table->dropColumn('project_key');
        });

        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn('project_key');
        });
    }
}
