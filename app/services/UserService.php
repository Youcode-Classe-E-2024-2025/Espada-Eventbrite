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
            // If the user exists, return false or some error
            return false; // You can replace this with a more descriptive error message if needed
        }

        // If the email does not exist, proceed with registration
        $roles = ['organizer' => 1, 'participant' => 2];

        // Add role_id to user data
        $userData['role_id'] = $roles[$userData['role']] ?? null; // Add default if the role doesn't exist

        return $this->userRepository->createUser($userData);
    }

    // Handle banning a user
    public function banUser(int $userId): bool
    {
        return $this->userRepository->banUser($userId);
    }

    // Handle unbanning a user
    public function unbanUser(int $userId): bool
    {
        return $this->userRepository->unbanUser($userId);
    }

    // Handle archiving a user
    public function archiveUser(int $userId): bool
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

    public function searchFilterUsers($keyword, $roleId, $status)
    {
        return $this->userRepository->searchUsersWithFilters($keyword, $roleId, $status);
    }

    public function updateUserStatus($userId, $status)
    {
        switch ($status) {
            case User::BANNED:
                $this->banUser($userId);
                break;
            case User::ACTIVE:
                $this->unbanUser($userId);
                break;
            case User::ARCHIVED:
                $this->archiveUser($userId);
                break;
            default:
                throw new \Exception("Invalid status");
        }
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAll();
    }
}
