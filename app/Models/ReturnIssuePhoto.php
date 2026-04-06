<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnIssuePhoto extends Model
{
    protected $fillable = [
        'return_issue_id',
        'photo_path',
        'caption',
    ];

    public function returnIssue()
    {
        return $this->belongsTo(ReturnIssue::class);
    }

    
}