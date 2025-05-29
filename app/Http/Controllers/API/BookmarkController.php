<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BookmarkService;
use Illuminate\Support\Facades\Validator;
use App\Models\Bookmark;
use App\Models\SharedBookmark;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\BookmarkResource;
use Illuminate\Http\JsonResponse;


class BookmarkController extends Controller
{
    protected $bookmarkService;
    use ApiResponseTrait;

    public function __construct(BookmarkService $bookmarkService)
    {
        $this->bookmarkService = $bookmarkService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10); 

            $bookmarks = $this->bookmarkService->getUserBookmarksPaginated($perPage);

            return $this->success(BookmarkResource::collection($bookmarks), 'Bookmarks fetched successfully');
        } catch (\Exception $e) {
            //return $this->error($e->getMessage());
            return $this->error('Something went wrong. Please try again later.');
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $bookmark = $this->bookmarkService->getById($id);

            if (!$bookmark) {
                return $this->fail('Bookmark not found', 404);
            }

            return $this->success(new BookmarkResource($bookmark), 'Bookmark fetched successfully');
        } catch (\Exception $e) {
            return $this->error('Something went wrong. Please try again later.');
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'url' => 'required|url',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'category' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation failed', 422, $validator->errors());
        }

        try {
            $data = $request->only(['title', 'url', 'category', 'tags']);

            $bookmark = $this->bookmarkService->createBookmark($data);

            return $this->success(new BookmarkResource($bookmark), 'Bookmark created successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Something went wrong. Please try again later.');
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'url' => 'required|url',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'category' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation failed', 422, $validator->errors());
        }

        try {
            $data = $request->only(['title', 'url', 'category', 'tags']);

            $bookmark = $this->bookmarkService->updateBookmark($id, $data);

            if (!$bookmark) {
                return $this->fail('Bookmark not found or not authorized', 404);
            }

            return $this->success(new BookmarkResource($bookmark), 'Bookmark updated successfully');
        } catch (\Exception $e) {
            return $this->error('Something went wrong. Please try again later.');
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->bookmarkService->deleteBookmark($id);

            if (!$deleted) {
                return $this->fail('Bookmark not found or not authorized to delete', 404);
            }

            return $this->success(null, 'Bookmark deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Something went wrong. Please try again later.');
        }
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('query');

        if (!$query) {
            return $this->fail('Search query is required', 422);
        }

        try {
            $results = $this->bookmarkService->searchBookmarks($query);

            return $this->success($results, 'Bookmarks search results fetched successfully');
        } catch (\Exception $e) {
            return $this->error('Something went wrong. Please try again later.');
        }
    }

    public function share(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation failed', 422, $validator->errors());
        }

        try {
            $message = $this->bookmarkService->shareBookmark($id, $request->email);

            return $this->success(null, $message);
        } catch (\Exception $e) {
            return $this->error('Something went wrong. Please try again later.');
        }
    }

    public function generateShareLink($id): JsonResponse
    {
        try {
            $link = $this->bookmarkService->generatePublicLink($id);

            return $this->success(['link' => $link], 'Public share link generated successfully');
        } catch (\Exception $e) {
            return $this->error('Something went wrong. Please try again later.');
        }
    }

    public function viewShared($token): JsonResponse
    {
        try {
            $bookmark = $this->bookmarkService->getPublicBookmark($token);

            if (!$bookmark) {
                return $this->fail('Invalid or expired link', 404);
            }

            return $this->success(new BookmarkResource($bookmark), 'Shared bookmark accessed successfully');
        } catch (\Exception $e) {
            return $this->error('Something went wrong. Please try again later.');
        }
    }

}
