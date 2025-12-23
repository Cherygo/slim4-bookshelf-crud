<?php

namespace App\Services;

use App\DTOs\BookDTO;
use App\DTOs\BookmarkDTO;
use App\Repositories\BookmarkRepository;
use App\Repositories\BookRepository;

class BookmarkService
{
    public  function __construct(
        private readonly BookmarkRepository $bookmarkRepository,
        private readonly BookRepository $bookRepository,
    ) {
    }

    public function addBookmark(int $userId, int $bookId)
    {
        $book = $this->bookRepository->getBookById($bookId);

        if(!$book) {
            throw new \RuntimeException();
        }

        $bookmark = $this->bookmarkRepository->bookmarkBook($userId, $bookId);
        if($bookmark === null) {
            throw new \RuntimeException();
        }
    }

    public function deleteBookmark(int $userId, int $bookId)
    {
        if($bookId == null) {
            throw new \RuntimeException();
        }

        $this->bookmarkRepository->unbookmarkBook($userId, $bookId);
    }

    public function getBookmarkIds(?int $userId): array
    {
        if($userId === null) {
            throw new \RuntimeException();
        }

        return $this->bookmarkRepository->getUserBookmarks($userId);
    }
}