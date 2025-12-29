<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\BookDTO;
use App\Http\Controllers\Controller;
use App\Repositories\BookRepository;
use App\Services\BookService;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;
use Slim\Views\Twig;

class BookController extends Controller
{
    public function __construct(
        Messages $flash,
        Twig $twig,
        private readonly BookRepository $bookRepository,
        private readonly BookService $bookService,
    ) {
        parent::__construct($flash, $twig);
    }


    public function index($request, $response): ResponseInterface
    {
        $allBooks = $this->bookRepository->getAllBooks();

        return $this->render($response, 'admin/books/index.twig', [
            'allBooks' => $allBooks,
        ]);
    }

    public function create($request, $response): ResponseInterface
    {
        $response = $this->render($response, 'admin/books/create.twig', [
            'errors' => $_SESSION['errors'] ?? [],
        ]);
        unset($_SESSION['errors']);
        return $response;
    }

    public function store(Request $request, Response $response): ResponseInterface
    {
        $params = (array)$request->getParsedBody();
        $book = BookDTO::fromArray($params);

        try {
            $this->bookService->store($book);
        } catch (\RuntimeException $e) {
            return $response->withRedirect('/admin/books/create');
        }

        return $response->withRedirect('/admin/books');
    }

    public function destroy($request, $response, $args): ResponseInterface
    {
        $id = $args['id'];

        $this->bookRepository->deleteBook($id);
        return $response->withRedirect('/admin/books');
    }
}