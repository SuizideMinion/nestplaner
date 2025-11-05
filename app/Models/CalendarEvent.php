<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'title',
        'start',
        'end',
        'color',
        'is_recurring',
        'recurrence_type',
        'recurrence_date',
        'all_day',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'is_recurring' => 'boolean',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }
}

