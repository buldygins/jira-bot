<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JiraIssueStatus extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
        'jiraId',
        'inner_name'
    ];
    protected static $separator = ':';

    public function getStatusFullName()
    {
        $name = $this->name;
        $name .= (!empty($this->inner_name)) ? static::$separator . ' ' . $this->inner_name : '';
        return $name;
    }

    public static function getStatusByFullName($full_name){
        $names = explode(static::$separator,$full_name);
        $status = JiraIssueStatus::query();
        $status->where('name', trim($names[0]));
        if (isset($names[1])){
            $status->where('inner_name', trim($names[1]));
        } else {
            $status->where('inner_name', null);
        }
        return $status->get()->first();
    }

    public static function getClosestStatusesName(JiraIssue $issue)
    {
        $current_status = $issue->status;
        $names = [];
        if (!$current_status) {
            $statuses = JiraIssueStatus::where('order', 1)->get();
        } else {
            $statuses = JiraIssueStatus::query();
            if ($issue->status->id - 1 > 0) {
                $statuses->where('order', $issue->status->id - 1)->orWhere('order', $issue->status->id + 1)->get();
            } else {
                $statuses->where('order', $issue->status->id + 1)->get();
            }
        }
        foreach ($statuses as $status){
            $names[] = $status->getStatusFullName();
        }
        return $names;
    }
}
