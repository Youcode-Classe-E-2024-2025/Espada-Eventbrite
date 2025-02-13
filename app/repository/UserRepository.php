<?php

namespace App\repository;

use App\core\Database;
use PDO;
use App\models\User;

class UserRepository
{
    private Database $DB;

    public function __construct()
    {
        $this->DB = new Database();
    }



    // Get user by email
    public function getUserByEmail(string $email): ?object
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->DB->query($query, [':email' => $email,]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }


    public function createUser($userData): bool
    {

        $query = "INSERT INTO users (role_id, email, password, username, avatar, banned, archived) 
                  VALUES (:role_id, :email, :password, :username, :avatar, 0, 0)";

        $stmt = $this->DB->query($query, [
            ':role_id' => $userData['role_id'],
            ':email' => $userData['email'],
            ':password' => $userData['password'],
            ':username' => $userData['username'],
            ':avatar' => $userData['avatar'],
        ]);


        return true;
    }


    // Ban a user
    public function banUser(int $userId)
    {
        $query = "UPDATE users SET banned = 1 WHERE id = :id";
        return $this->DB->query($query, ['id' => $userId]);
    }

    //unban user

    public function unbanUser(int $userId)
    {
        $query = "UPDATE users SET banned = 0 WHERE id = :id";
        return $this->DB->query($query, ['id' => $userId]);
    }


    // Archive a user
    public function archiveUser(int $userId)
    {
        $query = "UPDATE users SET archived = 1 WHERE id = :id";
        return $this->DB->query($query, ['id' => $userId]);
    }

    public function unarchiveUser(int $userId)
    {
        $query = "UPDATE users SET archived = 0 WHERE id = :id";
        return $this->DB->query($query, ['id' => $userId]);
    }

    // Get all banned users
    public function getBannedUsers(): array
    {
        $query = "SELECT * FROM users WHERE banned = 1";
        $stmt = $this->DB->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get the total number of users
    public function getNumberOfUsers(): int
    {
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->DB->query($query);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total ?? 0;
    }

    // Get all users
    public function getAll(): array
    {
        $query = "SELECT * FROM users ORDER BY id DESC";
        $stmt = $this->DB->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Update user (only name and avatar URL)
    public function updateUser(int $userId, string $name, string $avatar): bool
    {
        $query = "UPDATE users SET username = :username, avatar = :avatar WHERE id = :id";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute([
            'username' => $name,
            'avatar' => $avatar,
            'id' => $userId,
        ]);
    }

    // public function filterByRole(int $roleId)
    // {
    //     if ($roleId === User::ADMIN) {
    //         $sql = "SELECT * FROM users WHERE role_id = :role_id";
    //     } elseif ($roleId === User::ORGANIZER) {
    //         $sql = "SELECT * FROM users WHERE role_id = :role_id";
    //     } else {
    //         $sql = "SELECT * FROM users WHERE role_id = :role_id";
    //     }
    //     $stmt = $this->DB->query($sql, ['role_id' => $roleId]);
    //     return $stmt->fetchAll(PDO::FETCH_OBJ);
    // }

    // public function filterByStatus(int $status)
    // {
    //     if ($status === User::BANNED) {
    //         $sql = "SELECT * FROM users WHERE banned = 1";
    //     } elseif ($status === User::ARCHIVED) {
    //         $sql = "SELECT * FROM users WHERE archived = 1";
    //     } elseif ($status === User::ACTIVE) {
    //         $sql = "SELECT * FROM users WHERE banned = 0 AND archived = 0";
    //     }
    //     $stmt = $this->DB->query($sql);
    //     return $stmt->fetchAll(PDO::FETCH_OBJ);
    // }

    public function searchUsers($keyword)
    {
        $sql = "SELECT * FROM users WHERE username LIKE :keyword or email LIKE :keyword";
        $stmt = $this->DB->query($sql, ['keyword' => "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function searchUsersWithFilters($keyword, $roleId, $status)
    {
        $results = $this->getAll();
        if (!empty($keyword)) {
            $results = $this->searchUsers($keyword);
        }

        if (!empty($roleId) || !empty($status)) {
            $results = $this->filterByRoleAndStatus($roleId, $status);
        }

        return $results;
    }

    public function filterByRoleAndStatus($roleId, $status)
    {
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];

        if (!empty($roleId)) {
            $sql .= " AND role_id = :role_id";
            $params['role_id'] = $roleId;
        }

        if ($status !== '' && $status !== null) {
            if ($status === User::BANNED) {
                $sql .= " AND banned = 1";
            } elseif ($status === User::ARCHIVED) {
                $sql .= " AND archived = 1";
            } elseif ($status === User::ACTIVE) {
                $sql .= " AND banned = 0 AND archived = 0";
            }
        }

        return $this->DB->query($sql, $params)->fetchAll(PDO::FETCH_OBJ);
    }

    // public function getPendingUsers()
    // {
    //     $sql = "SELECT * FROM users WHERE status = :status";
    //     $stmt = $this->DB->query($sql, ['status' => User::UNARCHIVED]);
    //     return $stmt->fetchAll(PDO::FETCH_OBJ);
    // }

    public function getRecentUsers()
    {
        $sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT 2";
        $stmt = $this->DB->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
