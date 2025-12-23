<?php

namespace App\Repositories;

use App\DTOs\UserDTO;
use App\Models\User;

class UserRepository
{
    private \PDO $conn;

    public function __construct(\PDO $conn) {
        $this->conn = $conn;
    }

    public function create(UserDTO $userDTO)
    {
        $sql = "INSERT INTO users (username, email, password) VALUES(?, ?, ?) ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $userDTO->username,
            $userDTO->email,
            $userDTO->password
        ]);

        return $this->conn->lastInsertId();
    }

    public function findByEmailOrUsername(string $identifier): ?array
    {
        $sql = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $identifier,
            $identifier
        ]);

        $userData = $stmt->fetch();

        if(!$userData) {
            return null;
        }
        return $userData;
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch();
    }
}