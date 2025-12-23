<?php

namespace App\Models;

use Carbon\Carbon;

class User
{
    public Carbon $created_at;
    public function __construct(
        public int $id,
        public string $username,
        public string $email,
        public string $password,
        public string $role,
        string $created_at,
    ) {
        $this->created_at = Carbon::parse($created_at);
    }

    public static function fromArray(array $userData): self
    {
        return new self(
            id: $userData['id'],
            username: $userData['username'],
            email: $userData['email'],
            password: $userData['password'],
            role: $userData['role'],
            created_at: $userData['created_at'],
        );
    }

    public function toArray($userData): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
        ];
    }
}