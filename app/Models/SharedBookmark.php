<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharedBookmark extends Model
{
    protected $fillable = ['bookmark_id', 'shared_with_user_id', 'token', 'expires_at'];

    public function bookmark()
    {
        return $this->belongsTo(Bookmark::class);
    }

    public function sharedWith()
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }
}
