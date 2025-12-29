<?php

namespace App\DTOs;

use Carbon\Carbon;

class BookDTO
{
    public function __construct(
        public string $author,
        public string $title,
        public ?string $url
    ) {
    }

    public static  function fromArray(array $bookData): self
    {
        return new self(
            author: $bookData['author'],
            title: $bookData['title'],
            url: $bookData['url'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'author' => $this->author,
            'title' => $this->title,
            'url' => $this->url
        ];
    }
}