<?php

namespace App\Services;

use App\DTOs\BookDTO;
use App\Repositories\BookRepository;

class BookService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
    ) {
    }


    public function addBook(BookDTO $bookDTO)
    {

    }
}