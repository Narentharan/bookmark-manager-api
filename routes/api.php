<?php
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookmarkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AdminController;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('/bookmarks/search', [BookmarkController::class, 'search']);

// Route for Bookmark CRUD

    Route::get('/bookmarks', [BookmarkController::class, 'index']);
    Route::get('/bookmarks/{id}', [BookmarkController::class, 'show'])->where('id', '[0-9]+');
    Route::post('/bookmarks', [BookmarkController::class, 'store']);
    Route::put('/bookmarks/{id}', [BookmarkController::class, 'update']);
    Route::delete('/bookmarks/{id}', [BookmarkController::class, 'destroy']);

// Route for Sharing Functionality

    Route::post('/bookmarks/{id}/share', [BookmarkController::class, 'share']);
    Route::get('/bookmarks/{id}/share-link', [BookmarkController::class, 'generateShareLink']);
    

  // Admin access
  Route::middleware(['auth:api', 'admin'])->get('/admin/bookmarks', [AdminController::class, 'allBookmarks']);  

});

// Unprotected route for public access
Route::get('/shared/bookmarks/{token}', [BookmarkController::class, 'viewShared']);
