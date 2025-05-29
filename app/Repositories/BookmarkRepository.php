<?php

namespace App\Repositories;

use App\Models\Bookmark;
use App\Models\SharedBookmark;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class BookmarkRepository
{
    public function getUserBookmarksPaginated($perPage = 10): LengthAwarePaginator
    {
        return Bookmark::with('tags') 
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    public function create(array $data): Bookmark
    {
        $data['user_id'] = Auth::id();
        return Bookmark::create($data);
    }

    public function update($id, array $data): ?Bookmark
    {
        $bookmark = $this->getById($id);
        if ($bookmark) {
            $bookmark->update($data);
        }
        return $bookmark;
    }

   public function deleteBookmark($id): bool
    {
        $bookmark = Bookmark::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$bookmark) {
            return false;
        }

        // Detach associated tags (optional, but explicit)
        $bookmark->tags()->detach();

        // Delete the bookmark
        return $bookmark->delete();
    }

    public function searchBookmarks(string $query): LengthAwarePaginator
    {
        return Bookmark::with(['tags', 'user'])
            ->where('user_id', auth()->id())
            ->where(function ($q) use ($query) {
                $q->where('title', 'ILIKE', "%{$query}%")
                ->orWhereHas('tags', function ($tagQuery) use ($query) {
                    $tagQuery->where('name', 'ILIKE', "%{$query}%");
                });
            })
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    }


    public function shareBookmark($bookmarkId, $email): string
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new \Exception('User to share with does not exist');
        }

        $bookmark = Bookmark::where('id', $bookmarkId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$bookmark) {
            throw new \Exception('Bookmark not found or not authorized');
        }

        SharedBookmark::updateOrCreate(
            [
                'bookmark_id' => $bookmark->id,
                'shared_with_user_id' => $user->id
            ],
            [] // No additional data for now
        );

        return "Bookmark shared with {$user->email}";
    }


    public function generateShareLink($id): JsonResponse
    {
        try {
            $link = $this->bookmarkService->generatePublicLink($id);

            return $this->success(['link' => $link], 'Public share link generated successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function generatePublicLink($bookmarkId): string
    {
        $bookmark = Bookmark::with(['tags', 'user']) 
            ->where('id', $bookmarkId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$bookmark) {
            throw new \Exception('Bookmark not found or not authorized');
        }

        $token = Str::random(32);

        $expiresInDays = 7; 

        SharedBookmark::create([
            'bookmark_id' => $bookmark->id,
            'token' => $token,
            'expires_at' => now()->addDays((int) $expiresInDays)
        ]);

        return url("/api/v1/shared/bookmarks/{$token}");
    }

    public function getAllBookmarksPaginated($perPage = 10): LengthAwarePaginator
    {
        return Bookmark::with(['tags', 'user']) 
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
    }

    public function getPublicBookmark($token): ?Bookmark
    {
        $shared = SharedBookmark::with('bookmark.tags')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        return $shared ? $shared->bookmark : null;
    }

    public function createBookmark(array $data): Bookmark
    {
        $bookmark = Bookmark::create([
            'title' => $data['title'],
            'url' => $data['url'],
            'category' => $data['category'] ?? null,
            'user_id' => auth()->id()
        ]);

        // Handle tags
        if (!empty($data['tags']) && is_array($data['tags'])) {
            $tagIds = [];

            foreach ($data['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['name' => trim($tagName)]);
                $tagIds[] = $tag->id;
            }

            $bookmark->tags()->sync($tagIds);
        }

        return $bookmark;
    }

    public function updateBookmark($id, array $data): ?Bookmark
    {
        $bookmark = Bookmark::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$bookmark) {
            return null;
        }

        $bookmark->update([
            'title' => $data['title'],
            'url' => $data['url'],
            'category' => $data['category'] ?? null
        ]);

        // Handle tags
        if (!empty($data['tags']) && is_array($data['tags'])) {
            $tagIds = [];

            foreach ($data['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['name' => trim($tagName)]);
                $tagIds[] = $tag->id;
            }

            $bookmark->tags()->sync($tagIds);
        }

        return $bookmark;
    }

    public function getById($id): ?Bookmark
    {
        return Bookmark::with('tags')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();
    }

}
