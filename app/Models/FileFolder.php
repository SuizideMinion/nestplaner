<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileFolder extends Model
{
    use HasFactory;

    protected $fillable = ['family_id', 'user_id', 'name', 'visibility'];

    public function files()
    {
        return $this->hasMany(FamilyFile::class, 'folder_id');
    }

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
