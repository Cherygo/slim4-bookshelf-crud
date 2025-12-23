<?php

namespace App\Models;

use Carbon\Carbon;

class Book
{
    public Carbon $created_at;
    public function __construct(
        public int $id,
        public string $title,
        public string $author,
        public string $url,
        string $created_at,
    ) {
        $this->created_at = Carbon::parse($created_at);
    }

    public static function fromArray(array $bookData): self
    {
        return new self(
            id: $bookData['id'],
            title: $bookData['title'],
            author: $bookData['author'],
            url: $bookData['url'],
            created_at: $bookData['created_at'],
        );
    }

    public function toArray($bookData): array
    {
        return [
            'id' => $this->id,
            'title' => $this->author,
            'author' => $this->title,
            'url' => $this->url,
        ];
    }
}