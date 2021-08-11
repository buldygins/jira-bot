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
            $table->string('summary')->nullable()->comment('Название задачи');
            $table->string('issue_url')->nullable()->comment('URL задачи');
            $table->integer('issue_id')->nullable()->comment('ID задачи в Жире')->unique();
            $table->string('webhookEvent')->nullable()->comment('Событие');
            $table->string('updateAuthor')->nullable()->comment('Автор события');
            $table->dateTime('added')->nullable()->comment('дата добавления задачи');
            $table->dateTime('event_created')->nullable()->comment('дата когда прилетело событие');
            $table->dateTime('event_processed')->nullable()->comment('дата последнего обработанного события');
            $table->dateTime('started')->nullable()->comment('дата взятия в работу задачи');
            $table->dateTime('ended')->nullable()->comment('дата окончания работы по задаче');
            $table->jsonb('src')->nullable()->comment('исходник события');
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
