<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingApproval extends Model
{
    protected $fillable = [
        'booking_id',
        'approver_id',
        'level',
        'status',
        'catatan',
        'approved_at',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function canApprove(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        if ($this->level === 1) {
            return true;
        }

        return self::where('booking_id', $this->booking_id)
            ->where('level', $this->level - 1)
            ->where('status', 'approved')
            ->exists();
    }
}