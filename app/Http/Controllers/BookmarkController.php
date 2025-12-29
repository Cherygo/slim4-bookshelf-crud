<?php

namespace App\Http\Controllers;

use App\DTOs\BookmarkDTO;
use App\Services\BookmarkService;
use App\Repositories\BookmarkRepository;
use App\Repositories\BookRepository;
use App\Services\UserService;
use Slim\Flash\Messages;
use Slim\Http\Response;
use Slim\Views\Twig;
use Slim\Http\ServerRequest as Request;
use Psr\Http\Message\ResponseInterface;

class BookmarkController extends Controller
{
    public function __construct(
        Messages $flash,
        Twig $twig,
        private readonly BookmarkRepository $bookmarkRepository,
        private readonly BookmarkService $bookmarkService,
        private readonly UserService $userService,
    ) {
        parent::__construct($flash, $twig);
    }

    public function myBookmarksPage($request, $response): ResponseInterface
    {
        try {
            $user = $this->userService->getAuthorizedUser();

        } catch (\RuntimeException $e) {
            $this->flash->addMessage('danger', 'Login to access this page');
            return $response->withRedirect('/login');
        }

        $bookmarkedBooks = $this->bookmarkRepository->getBookmarkedBooks($user->id);
        $bookmarkedBooksIds = $this->bookmarkRepository->getUserBookmarks($user->id);


        return $this->render($response, 'bookmarks/my_books.twig', [
            'bookmarkedBooks' => $bookmarkedBooks,
            'bookmarkedBookIds' => $bookmarkedBooksIds,
        ]);
    }

    public function addBookmark(Request $request, Response $response)
    {
        $params = (array)$request->getParsedBody();
        $bookId = $params['bookId'];
        $user = $this->userService->getAuthorizedUser();

        try {
            $this->bookmarkService->addBookmark($user->id, $bookId);

            $this->flash->addMessage('success', 'Bookmark added');
        } catch (\RuntimeException $e) {
            $this->flash->addMessage('danger', 'Bookmark could not be added');
        }

        return $response->withRedirect('/catalog');
    }


    public function deleteBookmark(Request $request, Response $response, $args)
    {
        $bookId = $args['id'];
        $user = $this->userService->getAuthorizedUser();

        try {
            $this->bookmarkService->deleteBookmark($user->id, $bookId);
        } catch (\RuntimeException $e) {
            $this->flash->addMessage('danger', 'Error: Bookmark could not be deleted');
        }

        return $response->withRedirect('/catalog');
    }
}