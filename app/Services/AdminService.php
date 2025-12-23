<?php

namespace App\Services;

use App\Models\Book;
use App\Repositories\AdminRepository;
use Slim\Views\Twig;

class AdminService
{
    public function __construct(
        private readonly AdminRepository $adminRepository,
    ) {
    }

    public function addBookAdmin(Book $book)
    {

    }
}