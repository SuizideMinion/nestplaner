<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyFile extends Model
{
    use HasFactory;

    protected $fillable = ['folder_id', 'user_id', 'filename', 'original_name', 'mime_type', 'size'];

    public function folder()
    {
        return $this->belongsTo(FileFolder::class, 'folder_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
