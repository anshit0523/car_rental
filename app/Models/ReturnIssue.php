<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnIssue extends Model
{
    protected $fillable = [
        'booking_id',
        'reported_by',
        'issue_type',
        'title',
        'description',
        'issue_status_id',
        'estimated_charge',
        'final_charge',
        'reported_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'estimated_charge' => 'decimal:2',
        'final_charge' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function photos()
    {
        return $this->hasMany(ReturnIssuePhoto::class);
    }

     public function issueStatus(): BelongsTo
    {
        return $this->belongsTo(IssueStatus::class);
    }
}