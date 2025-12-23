<?php

namespace App\DTOs;

class BookmarkDTO
{
    public function __construct(
        public int $userId,
        public int $bookId,
    ) {
    }

    public static  function fromArray(array $bookmarkData): self
    {
        return new self(
            userId: $bookmarkData['userId'],
            bookId: $bookmarkData['bookId']
        );
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'bookId' => $this->bookId,
        ];
    }
}