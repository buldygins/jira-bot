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

    public function getStatus()
    {
        $name = $this->name;
        $name .= (!empty($this->inner_name)) ? ': ' . $this->inner_name : '';
        return $name;
    }
}
