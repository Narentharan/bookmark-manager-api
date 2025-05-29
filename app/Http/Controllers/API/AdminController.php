<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookmarkResource;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Services\BookmarkService;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    protected $bookmarkService;
    use ApiResponseTrait;

    public function __construct(BookmarkService $bookmarkService)
    {
        $this->bookmarkService = $bookmarkService;
    }
    public function allBookmarks(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);

            $bookmarks = $this->bookmarkService->getAllBookmarksPaginated($perPage);

            return $this->success(BookmarkResource::collection($bookmarks), 'All bookmarks fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
