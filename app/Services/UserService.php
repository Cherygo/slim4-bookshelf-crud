<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\Models\User;
use App\Repositories\UserRepository;
use http\Exception\RuntimeException;


class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    private function makeSession($user)
    {
        $_SESSION['user'] = $user;
    }

    public function registerUser(UserDTO $userDto)
    {
        $_SESSION['errors'] = [];

        foreach ($userDto->toArray() as $value) {
            if(empty($value)) {
                $_SESSION['errors'][] = 'Fill all fields';
                break;
            }
        }

        if($this->userRepository->findByEmailOrUsername($userDto->username)) {
            $_SESSION['errors'][] = 'Username already exists';
        }

        if($this->userRepository->findByEmailOrUsername($userDto->email)) {
            $_SESSION['errors'][] = 'Email already exists';
        }

        if(!filter_var($userDto->email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['errors'][] = 'Enter correct email';
        }

        if(!empty($_SESSION['errors'])) {
           throw new \RuntimeException();
        }

        $userDto->password = password_hash($userDto->password, PASSWORD_DEFAULT);

        $userId = $this->userRepository->create($userDto);
        $user = $this->userRepository->findById($userId);
        $this->makeSession(User::fromArray($user));
    }

    public function loginUser(string $identifier, string $password): ?User
    {
        $_SESSION['errors'] = [];

        $userData = $this->userRepository->findByEmailOrUsername($identifier);

        $user = $userData ? User::fromArray($userData) : null;

        if ( !$user || empty($password) ||!password_verify($password, $user->password)) {
            $_SESSION['errors'][] = 'Incorrect login credentials';
        }

        if(!empty($_SESSION['errors'])) {
            throw new \RuntimeException();
        }

        $this->makeSession($user);

        return null;
    }

    public function getAuthorizedUser(): User
    {
        if(!isset($_SESSION['user'])) {
            throw new \RuntimeException();
        }

        return $_SESSION['user'];
    }

    public function checkAuthorizedUser(): bool
    {
        return $_SESSION['user'] ?? false;
    }

    public function getUserDataById($id): array
    {
        return $this->userRepository->findById($id);
    }

    public function updateUserRole($user)
    {
        $this->userRepository->changeUserRole($user);
    }
}