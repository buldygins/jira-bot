<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Subscriber extends Model
{
    use HasFactory, Notifiable;
    protected $guarded=['id'];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'id_position');
    }

    public function user(){
        return $this->belongsTo(JiraUser::class);
    }

    public function canSendCommands(){
        return $this->jira_login && $this->api_token && $this->jira_user_id;
    }
}
