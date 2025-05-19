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

    public function update($id, $data)
    {
        return $this->bookmarkRepo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->bookmarkRepo->delete($id);
    }

    public function search($query = null, $category = null)
    {
        return $this->bookmarkRepo->search($query, $category);
    }

}
