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
        return !empty($this->jira_login) && !empty($this->api_token) && !empty($this->jira_user_id);
    }

    public function wantsOnlyTagged(){
        return (bool) $this->show_only_tagged;
    }

    // проверка, тегнут ли юзер
    public function isUserTagged($msg){
        $jiraLogin = optional($this->jira_login);
        $userKey = optional($this->user->getAccountKey());
        return (bool) (strpos($msg, $jiraLogin) !== false || strpos($msg, $userKey) !== false);
    }
}
