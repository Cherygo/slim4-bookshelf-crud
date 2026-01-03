<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\BookRepository;
use App\Repositories\UserRepository;
use App\Services\BookService;
use App\Services\UserService;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Http\ServerRequest as Request;
use Psr\Http\Message\ResponseInterface;

class UserController extends Controller
{
    public function __construct(
        Messages $flash,
        Twig $twig,
        private readonly UserRepository $userRepository,
        private readonly UserService $userService,
    ) {
        parent::__construct($flash, $twig);
    }

    public function index($request, $response): ResponseInterface
    {
        $allUsers = $this->userRepository->allUsers();

        return $this->render($response, 'admin/users/index.twig', [
           'users' => $allUsers,
        ]);
    }

    public function changeUserRole($request, $response, $args)
    {
        $user = $this->userService->getUserDataById($args['id']);
        $this->userService->updateUserRole($user);

        return $response->withRedirect('/admin/users/index', 302);
    }
}