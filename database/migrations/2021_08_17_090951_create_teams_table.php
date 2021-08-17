<?php

use Database\Seeders\PositionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Без названия')->nullable()->comment('Название команды');
            $table->string('projects')->nullable()->comment('Проекты через запятую для фильтра');
            $table->timestamps();
        });

        Schema::table('subscribers', function (Blueprint $table) {
            $table->string('team_id')->default(1)->comment('ID команды');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
    }
}
