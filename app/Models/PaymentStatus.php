<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * A payment status can apply to many payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
