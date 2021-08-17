<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Team extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = ['id'];

    public function getProjectsAttribute()
    {
        $arr = explode(',', $this->projects);
        foreach ($arr as $k => $v) {
            $arr[$k] = trim($v);
            if (trim($v) == '') {
                unset($arr[$k]);
            }
        }
        return $arr;
    }
}
