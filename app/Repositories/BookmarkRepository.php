<?php

namespace App\Repositories;

use App\Models\Book;

class BookmarkRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function isBookmarked(int $userId, int $bookId)
    {
        $sql = "SELECT 1 FROM bookmarked WHERE user_id = ? and book_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId, $bookId]);

        return $stmt->fetchColumn();
    }
    public function bookmarkBook(int $userId, int $bookId)
    {
        if(!$this->isBookmarked($userId, $bookId)) {
            $sql = "INSERT INTO bookmarked (user_id, book_id, created_at) VALUES (?, ?, NOW())
            ON CONFLICT DO NOTHING";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$userId, $bookId]);

            return 1;
        }

        return null;
    }

    public function unbookmarkBook(int $userId, int $bookId)
    {
        $sql = "DELETE FROM bookmarked WHERE user_id = ? and book_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId, $bookId]);

        return $stmt->fetch();
    }

    public function getBookmarkedBooks(int $userId): array
    {
        $sql = "
        SELECT b.* FROM books b 
        JOIN bookmarked bm ON b.id = bm.book_id
        WHERE bm.user_id = ?
        ORDER BY bm.created_at DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);
        $bookData = $stmt->fetchAll();

        $books = [];

        foreach ($bookData as $book) {
            $books[] = Book::fromArray($book);
        }

        return $books;
    }

    public function getUserBookmarks(int $userId): array
    {
        $sql = "SELECT book_id FROM bookmarked WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}