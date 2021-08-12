<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id')->comment('ID чата');
            $table->string('fio')->default('Не известно')->comment('ФИО');
            $table->string('id_position')->default(0)->comment('Позиция');
            $table->boolean('is_active')->default(true)->comment('Включен');
            $table->string('waited_command')->nullable()->comment('Ожидаем ввода для команды');
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
        Schema::dropIfExists('subscribers');
    }
}
