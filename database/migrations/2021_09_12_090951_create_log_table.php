<?php

use Database\Seeders\PositionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->integer('issue_id')->nullable();
            $table->string('issue_key')->nullable();
            $table->string('webhook_event')->nullable();
            $table->string('name')->nullable()->comment('Сообщение');
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
        Schema::dropIfExists('logs');
    }
}
