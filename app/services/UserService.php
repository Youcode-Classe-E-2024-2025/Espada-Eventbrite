<?php

namespace App\services;

use App\repository\UserRepository;
use App\core\Database;
use App\models\User;

class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    // Handle user login
    public function loginuser(string $email, string $password): ?object
    {
        // Fetch user by email
        $user = $this->userRepository->getUserByEmail($email);

        if ($user) {
            // Check if the user is banned or archived
            if ($user->banned == 1 || $user->archived == 1) {
                // If banned or archived, return null
                return null;
            }

            // Check if the password matches
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }

        return null;
    }


    // Handle user registration
    public function register(array $userData): bool
    {
        // Check if email already exists
        $existingUser = $this->userRepository->getUserByEmail($userData['email']);

        if ($existingUser) {
            // If the user exists, return false
            return false;
        }

        // If the email does not exist, proceed with registration
        $roles = ['organizer' => 1, 'participant' => 2];

        // Add role_id to user data
        $userData['role_id'] = $roles[$userData['role']] ?? null;

        // Hash the password
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

        // Attempt to create user
        $createdUser = $this->userRepository->createUser($userData);

        // Return true if user was created successfully, false otherwise
        return $createdUser !== null;
    }

    // Handle banning a user
    public function banUser(int $userId)
    {
        return $this->userRepository->banUser($userId);
    }

    // Handle unbanning a user
    public function unbanUser(int $userId)
    {
        return $this->userRepository->unbanUser($userId);
    }

    // Handle archiving a user
    public function archiveUser(int $userId)
    {
        return $this->userRepository->archiveUser($userId);
    }

    // Example function for additional logic or business rules
    public function isEmailRegistered(string $email): bool
    {
        return $this->userRepository->getUserByEmail($email) !== null;
    }

    public function findOrCreateGoogleUser(array $googleUserData): ?object
    {
        // Check if user exists by email
        $existingUser = $this->userRepository->getUserByEmail($googleUserData['email']);

        if ($existingUser) {
            // Update user's avatar if it has changed
            if ($existingUser->avatar !== $googleUserData['avatar']) {
                $this->userRepository->updateUser(
                    $existingUser->id,
                    $googleUserData['name'],
                    $googleUserData['avatar']
                );
            }
            return $existingUser;
        }

        // Create new user with Google data
        $newUserData = [
            'first_name' => explode(' ', $googleUserData['name'])[0],
            'last_name' => explode(' ', $googleUserData['name'])[1] ?? '',
            'email' => $googleUserData['email'],
            'avatar' => $googleUserData['avatar'],
            'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT), // Random secure password
            'role' => 'user', // Default role for Google-authenticated users
            'google_id' => true // Flag to indicate Google-authenticated user
        ];

        $this->userRepository->createUser($newUserData);
        return $this->userRepository->getUserByEmail($googleUserData['email']);
    }

    public function searchFilterUsers($keyword, $roleId, $status, $page = 1, $limit = 4)
    {
        return $this->userRepository->searchUsersWithFilters($keyword, $roleId, $status, $page, $limit);
    }

    public function getTotalUserSearchResults($keyword)
    {
        return $this->userRepository->getTotalUserSearchResults($keyword);
    }

    public function filterUsers($roleId, $status)
    {
        return $this->userRepository->filterByRoleAndStatus($roleId, $status);
    }


    public function updateUserStatus($userId, $status)
    {
        switch ($status) {
            case User::BANNED:
                return $this->userRepository->banUser($userId);
            case User::ACTIVE:
                return $this->userRepository->unbanUser($userId);
            case User::ARCHIVED:
                return $this->userRepository->archiveUser($userId);
            case User::UNARCHIVED:
                return $this->userRepository->unarchiveUser($userId);
            default:
                throw new \Exception("Invalid status");
        }
    }

    public function getAllUsers($page = 1, $limit = 5)
    {
        return $this->userRepository->getAll($page, $limit);
    }

    public function getTotalUsers(): int
    {
        return $this->userRepository->getNumberOfUsers();
    }

    // public function getPendingUsers()
    // {
    //     return $this->userRepository->getPendingUsers();
    // }
    
    public function getRecentUsers()
    {
        return $this->userRepository->getRecentUsers();
    }

    public function updateUser(int $userId, string $name, string $avatar)
{
    return $this->userRepository->updateUser($userId, $name, $avatar);
}

    // Find user by username
    public function findByUsername(string $username): ?array
    {
        $user = $this->userRepository->getUserByUsername($username);
        return $user ? (array) $user : null;
    }

    // Find user by Google ID
    public function findByGoogleId(string $googleId): ?array
    {
        $user = $this->userRepository->getUserByGoogleId($googleId);
        return $user ? (array) $user : null;
    }

    // Update user with Google ID
    public function updateGoogleId(int $userId, array $data): ?array
    {
        $updatedUser = $this->userRepository->updateUserGoogleId($userId, $data);
        return $updatedUser ? (array) $updatedUser : null;
    }

    // Update user information
    public function updateUserInfo(int $userId, array $data): ?array
    {
        $updatedUser = $this->userRepository->updateUserInfo($userId, $data);
        return $updatedUser ? (array) $updatedUser : null;
    }

    // Create a user (with modifications for Google login)
    public function create(array $userData): ?array
    {
        // Validate required fields
        if (empty($userData['email']) || empty($userData['username'])) {
            error_log('User creation failed: Missing required fields');
            return null;
        }

        // Predefined roles
        $roles = ['organizer' => 1, 'participant' => 2, 'admin' => 3];

        // Ensure role_id is set, defaulting to participant (2)
        $userData['role_id'] = $userData['role_id'] 
            ?? $userData['role_id'] 
            ?? $roles[$userData['role'] ?? 'participant'] 
            ?? 2;

        // Remove 'role' key to match repository method
        unset($userData['role']);

        // Check if email already exists
        $existingUser = $this->userRepository->getUserByEmail($userData['email']);
        if ($existingUser) {
            error_log('User creation failed: Email already exists - ' . $userData['email']);
            return null;
        }

        // Check if username already exists
        $existingUsername = $this->userRepository->getUserByUsername($userData['username']);
        if ($existingUsername) {
            error_log('User creation failed: Username already exists - ' . $userData['username']);
            return null;
        }

        // Hash password if it's not a Google login
        if (!isset($userData['is_google']) || !$userData['is_google']) {
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        }

        // Ensure google_id and is_google are set for Google users
        if (isset($userData['google_id'])) {
            $userData['is_google'] = true;
        }

        // Attempt to create user
        $createdUser = $this->userRepository->createUser($userData);
        
        if (!$createdUser) {
            error_log('User creation failed: Database insertion error');
            return null;
        }

        // Convert to array and return
        return (array) $createdUser;
    }

    // Find user by email (wrapper for getUserByEmail)
    public function findByEmail(string $email): ?array
    {
        $user = $this->userRepository->getUserByEmail($email);
        return $user ? (array) $user : null;
    }
}
