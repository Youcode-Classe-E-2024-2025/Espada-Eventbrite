<?php

namespace App\repository;

use App\core\Database;
use PDO;

class UserRepository {
    private Database $DB;

    public function __construct() {
        $this->DB = new Database();
    }

    // Get user by email
    public function getUserByEmail(string $email): ?object {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->DB->getConnection()->prepare($query);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    // Create a new user
    public function createUser(array $userData): bool {
        $query = "INSERT INTO users (role_id, email, password, username, avatar, banned, archived) 
                  VALUES (:role_id, :email, :password, :username, :avatar, 0, 0)";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute($userData);
    }

    // Ban a user
    public function banUser(int $userId): bool {
        $query = "UPDATE users SET banned = 1 WHERE id = :id";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute(['id' => $userId]);
    }

    //unban user

    public function unbanUser(int $userId): bool {
        $query = "UPDATE users SET banned = 0 WHERE id = :id";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute(['id' => $userId]);
    }
    

    // Archive a user
    public function archiveUser(int $userId): bool {
        $query = "UPDATE users SET archived = 1 WHERE id = :id";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute(['id' => $userId]);
    }

    // Get all banned users
    public function getBannedUsers(): array {
        $query = "SELECT * FROM users WHERE banned = 1";
        $stmt = $this->DB->getConnection()->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get the total number of users
    public function getNumberOfUsers(): int {
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->DB->getConnection()->query($query);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total ?? 0;
    }

    // Get all users
    public function getAll(): array {
        $query = "SELECT * FROM users";
        $stmt = $this->DB->getConnection()->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Update user (only name and avatar URL)
    public function updateUser(int $userId, string $name, string $avatar): bool {
        $query = "UPDATE users SET username = :username, avatar = :avatar WHERE id = :id";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute([
            'username' => $name,
            'avatar' => $avatar,
            'id' => $userId,
        ]);
    }
}
