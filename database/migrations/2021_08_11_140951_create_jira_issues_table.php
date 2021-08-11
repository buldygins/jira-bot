<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJiraIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jira_issues', function (Blueprint $table) {
            $table->id();
            $table->string('key')->comment('Jira issue key');
            $table->dateTime('added')->nullable()->comment('дата добавления задачи');
            $table->dateTime('started')->nullable()->comment('дата взятия в работу задачи');
            $table->dateTime('ended')->nullable()->comment('дата окончания работы по задаче');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jira_issues');
    }
}
