<?php

namespace App\Repositories;

use App\Models\Book;

class BookRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getLastBooks($count = 3): array
    {
        $sql = "SELECT * FROM books ORDER BY id DESC LIMIT 3";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        $books = $stmt->fetchAll();

        return $books;
    }

    public function getBooksCount()
    {
        $countStmt = $this->conn->prepare('SELECT COUNT(id) AS total FROM books');
        $countStmt->execute();

        return  $countStmt->fetchColumn();
    }
    
    public function getAllBooks(): array
    {
        $sql = "SELECT * FROM books ORDER BY id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getBookById($id)
    {
        $sql = "SELECT * FROM books WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function addBook($book)
    {
        $sql = "INSERT INTO books(title, author, url) VALUES(?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$book->title, $book->author, $book->url]);
    }


    public function deleteBook($id)
    {
        $sql = "DELETE FROM books WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }
}