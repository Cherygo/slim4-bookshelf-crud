<?php

namespace App\Http\Controllers;

use App\DTOs\UserDTO;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;
use Slim\Views\Twig;

class AuthController extends Controller
{
    public function __construct(
        Messages $flash,
        Twig $twig,
        private readonly UserService $userService,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct($flash, $twig);
    }

    public function registerPage($request, $response): ResponseInterface
    {
        $response = $this->render($response, 'auth/register.twig', [
            'errors' => $_SESSION['errors'] ?? [],
        ]);
        unset($_SESSION['errors']);
        return $response;
    }

    public function store(Request $request,Response $response)
    {
        $params= (array)$request->getParsedBody();
        $userDto = UserDTO::fromArray($params);

        try {
        $this->userService->registerUser($userDto);

        } catch (\RuntimeException $e) {
          return $response->withRedirect('/register');
        }

        $this->flash->addMessage('success', 'Registration completed');
        return $response->withRedirect('/');
    }

    public function loginPage($request, $response): ResponseInterface
    {
        $response = $this->render($response, 'auth/login.twig', [
            'errors' => $_SESSION['errors'] ?? [],
        ]);
        unset($_SESSION['errors']);
        return $response;
    }
    public function login($request, $response)
    {
        $params = (array)$request->getParsedBody();
        $identifier = $params['identifier'];
        $password = $params['password'];

        try{
        $user = $this->userService->loginUser($identifier, $password);
        } catch (\RuntimeException $e) {
            return $response->withRedirect('/login');
        }

        $this->flash->addMessage('success', 'Welcome back!');
        return $response->withRedirect('/')->withStatus(302);
    }

    public function logout(Request $request,Response $response)
    {
        session_destroy();
        return $response->withRedirect('/')->withStatus(302);

    }
}