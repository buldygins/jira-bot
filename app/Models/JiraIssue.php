<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class JiraIssue extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function status(){
        return $this->belongsTo(JiraIssueStatus::class);
    }

    public function assignee(){
        return $this->belongsTo(JiraUser::class,'assignee_id');
    }

    public function previous_stauts(){
        return $this->belongsTo(JiraIssueStatus::class,'previous_status_id');
    }

    public function getLink(){
        return "<a href='{$this->issue_url}'>{$this->key}</a>";
    }
}
