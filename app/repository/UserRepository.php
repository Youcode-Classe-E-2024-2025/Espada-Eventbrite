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


    public function getDefaultRole(): int
    {
        try {
            // Check if default role exists
            $query = "SELECT id FROM role WHERE name = 'user'";
            $stmt = $this->DB->query($query);
            $existingRole = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingRole) {
                return $existingRole['id'];
            }

            // Create default 'user' role if not exists
            $insertQuery = "INSERT INTO role (name) VALUES ('user') RETURNING id";
            $stmt = $this->DB->query($insertQuery);
            $newRole = $stmt->fetch(PDO::FETCH_ASSOC);

            return $newRole['id'];
        } catch (\PDOException $e) {
            error_log('Role Creation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createUser($userData): ?object
    {
        try {
            // If no role specified, get default role
            if (!isset($userData['role_id']) || $userData['role_id'] === 0) {
                $userData['role_id'] = $this->getDefaultRole();
            }

            // Prepare the query with all possible fields
            $query = "INSERT INTO users (
                role_id, 
                email, 
                password, 
                username, 
                avatar, 
                banned, 
                archived, 
                google_id, 
                is_google
            ) VALUES (
                :role_id, 
                :email, 
                :password, 
                :username, 
                :avatar, 
                0, 
                0, 
                :google_id, 
                :is_google
            ) RETURNING *";

            // Prepare parameters
            $params = [
                ':role_id' => $userData['role_id'],
                ':email' => $userData['email'],
                ':password' => $userData['password'],
                ':username' => $userData['username'],
                ':avatar' => $userData['avatar'] ?? null,
                ':google_id' => $userData['google_id'] ?? null,
                ':is_google' => isset($userData['is_google']) 
                    ? ($userData['is_google'] ? 1 : 0) 
                    : 0  // Default to 0 if not set
            ];

            // Execute query and return the created user
            $stmt = $this->DB->query($query, $params);
            return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
        } catch (\PDOException $e) {
            // Log the specific database error
            error_log('User Creation Error: ' . $e->getMessage());
            return null;
        }
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
    public function getAll($page = 1, $perPage = 4): array
    {
        $offset = ($page - 1) * $perPage;

        $query = "SELECT * FROM users ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->DB->query($query, [
            ':limit' => $perPage,
            ':offset' => $offset
        ]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Update user (only name and avatar URL)
    public function updateUser(int $userId, string $name)
    {
        $query = "UPDATE users SET username = :username WHERE id = :id";
        $stmt = $this->DB->getConnection()->prepare($query);
        return $stmt->execute([
            'username' => $name,
            'id' => $userId,
        ]);
    }

    // Get user by username
    public function getUserByUsername(string $username): ?object
    {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->DB->query($query, [':username' => $username]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    // Get user by Google ID
    public function getUserByGoogleId(string $googleId): ?object
    {
        $query = "SELECT * FROM users WHERE google_id = :google_id";
        $stmt = $this->DB->query($query, [':google_id' => $googleId]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    // Update user with Google ID
    public function updateUserGoogleId(int $userId, array $data): ?object
    {
        $query = "UPDATE users SET 
                    google_id = :google_id, 
                    is_google = :is_google, 
                    avatar = COALESCE(:avatar, avatar) 
                  WHERE id = :id 
                  RETURNING *";

        $stmt = $this->DB->query($query, [
            ':id' => $userId,
            ':google_id' => $data['google_id'],
            ':is_google' => $data['is_google'] ? 1 : 0,
            ':avatar' => $data['avatar'] ?? null
        ]);

        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    // Update user information
    public function updateUserInfo(int $userId, array $data): ?object
    {
        $query = "UPDATE users SET 
                    username = COALESCE(:username, username), 
                    avatar = COALESCE(:avatar, avatar) 
                  WHERE id = :id 
                  RETURNING *";

        $stmt = $this->DB->query($query, [
            ':id' => $userId,
            ':username' => $data['username'] ?? null,
            ':avatar' => $data['avatar'] ?? null
        ]);

        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
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

    public function searchUsers($keyword, $page = 1, $limit = 4)
    {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM users WHERE username LIKE :keyword or email LIKE :keyword LIMIT :limit OFFSET :offset";
        $stmt = $this->DB->query($sql, ['keyword' => "%$keyword%", ':limit' => $limit, ':offset' => $offset]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function searchUsersWithFilters($keyword, $roleId, $status, $page = 1, $perPage = 4)
    {
        $results = $this->getAll($page, $perPage);
        if (!empty($keyword)) {
            $results = $this->searchUsers($keyword, $page, $perPage);
        }

        if (!empty($roleId) || !empty($status)) {
            $results = $this->filterByRoleAndStatus($roleId, $status);
        }

        return $results;
    }

    public function getTotalUserSearchResults($keyword)
    {
        $sql = "SELECT COUNT(*) as total FROM users 
            WHERE username LIKE :keyword 
            OR email LIKE :keyword";

        $result = $this->DB->query($sql, ['keyword' => "%$keyword%"])->fetch(PDO::FETCH_OBJ);
        return $result->total;
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

    public function getUserById($userId)
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->DB->query($sql, ['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getUserGrowthLastSixMonths()
    {
        $query = "SELECT COUNT(*) as count 
              FROM users 
              WHERE created_at >= NOW() - INTERVAL '6 months'
              GROUP BY DATE_TRUNC('month', created_at) 
              ORDER BY DATE_TRUNC('month', created_at)";

        $stmt = $this->DB->query($query);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
