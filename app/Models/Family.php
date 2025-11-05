<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Family extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'owner_id', 'invite_code'];

    protected static function booted()
    {
        static::creating(function ($family) {
            $family->invite_code = Str::upper(Str::random(8));
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }
}
