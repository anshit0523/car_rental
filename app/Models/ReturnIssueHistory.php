<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnIssueHistory extends Model
{
    protected $fillable = [
        'return_issue_id',
        'issue_status_id',
        'changed_by',
        'event_type',
        'title',
        'message',
        'final_charge',
        'booking_status_name',
    ];

    protected $casts = [
        'final_charge' => 'decimal:2',
    ];

    public function returnIssue(): BelongsTo
    {
        return $this->belongsTo(ReturnIssue::class);
    }

    public function issueStatus(): BelongsTo
    {
        return $this->belongsTo(IssueStatus::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}