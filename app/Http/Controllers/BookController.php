<?php

namespace App\Http\Controllers;

use App\DTOs\BookDTO;
use App\Models\Book;
use App\Repositories\BookmarkRepository;
use App\Repositories\BookRepository;
use App\Services\BookmarkService;
use App\Services\UserService;
use Slim\Flash\Messages;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Views\Twig;
use Slim\Http\ServerRequest as Request;

class BookController extends Controller
{
    public function __construct(
        Messages $flash,
        Twig $twig,
        private readonly BookRepository $bookRepository,
        private readonly BookmarkRepository $bookmarkRepository,
        private readonly UserService $userService,
        private readonly BookmarkService $bookmarkService
    ) {
        parent::__construct($flash, $twig);
    }

    public function catalogPage($request, $response): ResponseInterface
    {
        $allBooks = $this->bookRepository->getAllBooks();

        try {
            $user = $this->userService->getAuthorizedUser();
            $bookmarkedBookIds = $this->bookmarkService->getBookmarkIds($user->id);

        } catch (\RuntimeException $e) {
            $this->flash->addMessage('danger', 'Login to access this page');
            return $response->withRedirect('/login');
        }

        return $this->render($response,'catalog/books_catalog.twig', [
            'books' => $allBooks,
            'bookmarkedBookIds' => $bookmarkedBookIds,
        ]);
    }

    public function store(Request $request, Response $response)
    {
        $params = (array)$request->getParsedBody();
        $book = new BookDTO($params['author'], $params['title'], $params['url']);

        try {
            $this->bookRepository->addBook($book->toArray());
        } catch (\RuntimeException $e) {
            $this->flash->addMessage('danger', "Book could be added");
        }
    }
}