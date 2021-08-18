<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JiraIssueStatus extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
        'jiraId'
    ];
}
