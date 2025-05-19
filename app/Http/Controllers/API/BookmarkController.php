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


class BookmarkController extends Controller
{
    protected $bookmarkService;

    public function __construct(BookmarkService $bookmarkService)
    {
        $this->bookmarkService = $bookmarkService;
    }

    public function index()
    {
        try {
            $bookmarks = $this->bookmarkService->getAll();
            return response()->json(['status' => 'success', 'data' => $bookmarks], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $bookmark = $this->bookmarkService->getById($id);
            if (!$bookmark) {
                return response()->json(['status' => 'fail', 'message' => 'Bookmark not found'], 404);
            }
            return response()->json(['status' => 'success', 'data' => $bookmark], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'url' => 'required|url',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'category' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'fail', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->all();

            if (isset($data['tags']) && is_array($data['tags'])) {
                $data['tags'] = implode(',', $data['tags']);
            }

            $bookmark = $this->bookmarkService->create($data);
            return response()->json(['status' => 'success', 'data' => $bookmark], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $bookmark = $this->bookmarkService->update($id, $request->all());

            if (!$bookmark) {
                return response()->json(['status' => 'fail', 'message' => 'Bookmark not found or not authorized'], 404);
            }

            return response()->json(['status' => 'success', 'data' => $bookmark], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->bookmarkService->delete($id);

            if (!$deleted) {
                return response()->json(['status' => 'fail', 'message' => 'Bookmark not found or not authorized'], 404);
            }

            return response()->json(['status' => 'success', 'message' => 'Bookmark deleted'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 500);
        }
    }

        public function search(Request $request)
    {
        $query = $request->input('query');
        $category = $request->input('category');

        if (!$query && !$category) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Query or category is required for searching'
            ], 422);
        }

        try {
            $results = $this->bookmarkService->search($query, $category);
            return response()->json([
                'status' => 'success',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Search failed'
            ], 500);
        }
    }

    public function share(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();
        $bookmark = Bookmark::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$bookmark) {
            return response()->json(['status' => 'fail', 'message' => 'Bookmark not found'], 404);
        }

        SharedBookmark::create([
            'bookmark_id' => $bookmark->id,
            'shared_with_user_id' => $user->id
        ]);

        return response()->json(['status' => 'success', 'message' => "Bookmark shared with {$user->email}"]);
    }

    public function generateShareLink($id)
    {
        $bookmark = Bookmark::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$bookmark) {
            return response()->json(['status' => 'fail', 'message' => 'Bookmark not found'], 404);
        }

        $token = Str::random(32);

        SharedBookmark::create([
            'bookmark_id' => $bookmark->id,
            'token' => $token,
            'expires_at' => Carbon::now()->addDays(7)
        ]);

        return response()->json([
            'status' => 'success',
            'link' => url("/api/shared/bookmarks/{$token}")
        ]);
    }

    public function viewShared($token)
    {
        $shared = SharedBookmark::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$shared) {
            return response()->json(['status' => 'fail', 'message' => 'Invalid or expired link'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $shared->bookmark
        ]);
    }

}
