<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnIssue extends Model
{
    protected $fillable = [
        'booking_id',
        'reported_by',
        'issue_type',
        'title',
        'description',
        'status',
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
}