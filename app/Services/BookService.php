<?php

namespace App\Services;

use App\DTOs\BookDTO;
use App\Repositories\BookRepository;
use Symfony\Component\Translation\Exception\RuntimeException;

readonly class BookService
{
    public function __construct(
        private BookRepository $bookRepository,
    ) {
    }


    public function store(BookDTO $bookData)
    {
        $_SESSION['errors'] = [];

        if(!$bookData->title || !$bookData->author) {
            $_SESSION['errors'][] = 'Fill all required fields';
        }

        if($bookData->url === null) {
            $bookData->url = '';
        }

        if(!empty($_SESSION['errors'])) {
            throw new \RuntimeException();
        }

        $this->bookRepository->addBook($bookData);
    }
}