<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JiraProject extends Model
{

    public $timestamps = false;
    public $guarded = ['id'];

    public function teams(){
        return $this->hasMany(Team::class);
    }
}
