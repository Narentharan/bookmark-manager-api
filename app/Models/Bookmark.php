<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'url', 'tags', 'category'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shared()
    {
        return $this->hasMany(SharedBookmark::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getTagNamesAttribute()
    {
        return $this->tags->pluck('name')->toArray();
    }

}
