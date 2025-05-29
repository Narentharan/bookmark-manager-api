<?php

namespace App\Services;

use App\Repositories\BookmarkRepository;
use Exception;

class BookmarkService
{
    protected $bookmarkRepo;

    public function __construct(BookmarkRepository $bookmarkRepo)
    {
        $this->bookmarkRepo = $bookmarkRepo;
    }

    public function getAll()
    {
        return $this->bookmarkRepo->getAll();
    }

    public function getById($id)
    {
        return $this->bookmarkRepo->getById($id);
    }

    public function create($data)
    {
        return $this->bookmarkRepo->create($data);
    }

    public function updateBookmark($id, array $data)
    {
        return $this->bookmarkRepo->updateBookmark($id, $data);
    }

    public function deleteBookmark($id)
    {
        return $this->bookmarkRepo->deleteBookmark($id);
    }

    public function searchBookmarks(string $query)
    {
        return $this->bookmarkRepo->searchBookmarks($query);
    }

    public function shareBookmark($bookmarkId, $email)
    {
        return $this->bookmarkRepo->shareBookmark($bookmarkId, $email);
    }

    public function generatePublicLink($bookmarkId)
    {
        return $this->bookmarkRepo->generatePublicLink($bookmarkId);
    }

    public function getPublicBookmark($token)
    {
        return $this->bookmarkRepo->getPublicBookmark($token);
    }

    public function getUserBookmarksPaginated($perPage = 10)
    {
        return $this->bookmarkRepo->getUserBookmarksPaginated($perPage);
    }

    public function getAllBookmarksPaginated($perPage = 10)
    {
        return $this->bookmarkRepo->getAllBookmarksPaginated($perPage);
    }

    public function createBookmark(array $data)
    {
    return $this->bookmarkRepo->createBookmark($data);
    }

}
