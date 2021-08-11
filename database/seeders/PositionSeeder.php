<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Position::query()->create(['name'=>'Тимлид']);
        Position::query()->create(['name'=>'Бэкендер']);
        Position::query()->create(['name'=>'Фронтендер']);
        Position::query()->create(['name'=>'Тестировщик']);
        Position::query()->create(['name'=>'Аналитик']);
        Position::query()->create(['name'=>'Менеджер']);
    }
}
