<?php

namespace App\Repositories;

use App\Models\Bookmark;
use Illuminate\Support\Facades\Auth;

class BookmarkRepository
{
    public function getAll()
    {
        return Bookmark::where('user_id', Auth::id())->get();
    }

    public function getById($id)
    {
        return Bookmark::where('id', $id)->where('user_id', Auth::id())->first();
    }

    public function create(array $data)
    {
        $data['user_id'] = Auth::id();
        return Bookmark::create($data);
    }

    public function update($id, array $data)
    {
        $bookmark = $this->getById($id);
        if ($bookmark) {
            $bookmark->update($data);
        }
        return $bookmark;
    }

    public function delete($id)
    {
        $bookmark = $this->getById($id);
        if ($bookmark) {
            $bookmark->delete();
            return true;
        }
        return false;
    }

    public function search($query = null, $category = null)
    {
        $userId = Auth::id();

        return Bookmark::where('user_id', $userId)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('title', 'ILIKE', "%{$query}%")
                        ->orWhere('tags', 'ILIKE', "%{$query}%");
                });
            })
            ->when($category, function ($q) use ($category) {
                $q->where('category', 'ILIKE', "%{$category}%");
            })
            ->get();
    }

}
