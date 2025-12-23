<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\BookRepository;
use App\Services\BookService;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;

class BookController extends Controller
{
    public function __construct(
        Messages $flash,
        Twig $twig,
        private readonly BookRepository $bookRepository,
    ) {
        parent::__construct($flash, $twig);
    }


    public function index($request, $response): ResponseInterface
    {
        $allBooks = $this->bookRepository->getAllBooks();

        return $this->render($response, 'admin/admin_books.twig', [
            'allBooks' => $allBooks,
        ]);
    }

    public function create()
    {

    }

    public function store()
    {

    }
}