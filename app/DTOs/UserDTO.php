<?php

namespace App\DTOs;

use Carbon\Carbon;

class UserDTO
{
    public function __construct(
        public string $username,
        public string $email,
        public string $password,
    ) {
    }

    public static  function fromArray(array $userData): self
    {
        return new self(
          username: $userData['username'],
          email: $userData['email'],
          password: $userData['password'],
        );
    }

    public function toArray(): array
    {
        return [
            'username' =>$this->username,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}