<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IssueStatus extends Model
{
    protected $fillable = [
        'name',
        'label',
        'color',
    ];

    public function returnIssues(): HasMany
    {
        return $this->hasMany(ReturnIssue::class);
    }
}