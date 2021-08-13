<?php

use Database\Seeders\PositionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateJiraUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jira_users', function (Blueprint $table) {
            $table->id();
            $table->integer('key')->nullable();
            $table->string('accountId')->nullable();
            $table->string('name')->nullable();
            $table->boolean('active')->nullable();
            $table->string('timeZone')->nullable();
            $table->string('displayName')->nullable();
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
        Schema::dropIfExists('jira_users');
    }
}
