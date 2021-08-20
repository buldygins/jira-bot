<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableJiraIssueStatusesAddColumnInnerNameAndOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jira_issue_statuses', function (Blueprint $table) {
            $table->string('inner_name')->nullable()->default(null)->comment('Внутреннее название статуса');
            $table->integer('order')->nullable()->default(null)->comment('Порядок');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jira_issue_statuses', function (Blueprint $table) {
            $table->dropColumn('inner_name');
            $table->dropColumn('order');
        });
    }
}
