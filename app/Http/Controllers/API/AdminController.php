<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;

class AdminController extends Controller
{
    public function allBookmarks()
    {
        $bookmarks = Bookmark::with('user')->latest()->get();

        $data = $bookmarks->map(function ($bookmark) {
            return [
                'user' => $bookmark->user->email,
                'title' => $bookmark->title,
                'url' => $bookmark->url,
                'tags' => explode(',', $bookmark->tags),
                'category' => $bookmark->category,
                'created_at' => $bookmark->created_at,
            ];
        });

        return response()->json(['status' => 'success', 'data' => $data]);
    }
}
