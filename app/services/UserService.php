<?php

namespace App\services;

use App\repository\UserRepository;
use App\core\Database;

class UserService {
    private UserRepository $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    // Handle user login
    public function login(string $email, string $password): ?object {
        $user = $this->userRepository->getUserByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }

        return null;
    }

    // Handle user registration
    public function register(array $userData): bool {
        // Hash the password before storing it
        // $userData['password'] = password_hash($userData['password'], PASSWORD_BCRYPT);

        return $this->userRepository->createUser($userData);
    }

    // Handle banning a user
    public function banUser(int $userId): bool {
        return $this->userRepository->banUser($userId);
    }

    // Handle unbanning a user
    public function unbanUser(int $userId): bool {
        return $this->userRepository->unbanUser($userId);
    }

    // Handle archiving a user
    public function archiveUser(int $userId): bool {
        return $this->userRepository->archiveUser($userId);
    }

    // Example function for additional logic or business rules
    public function isEmailRegistered(string $email): bool {
        return $this->userRepository->getUserByEmail($email) !== null;
    }
}
