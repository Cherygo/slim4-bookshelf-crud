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

    public function allUsers()
    {
        $sql = "SELECT * FROM users";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function changeUserRole($user)
    {
        if($user['role'] === 'admin') {
            $sql = "UPDATE users SET role = 'user' WHERE id = ?";
        }
        else {
            $sql = "UPDATE users SET role = 'admin' WHERE id = ?";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user['id']]);
    }
}