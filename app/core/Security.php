<?php

namespace App\core;

class Security
{
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * Generates a new CSRF token and stores it in session
     * 
     * @return string Generated CSRF token
     */
    public function generateCsrfToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->session->set('csrf_token', $token);
        return $token;
    }

    /**
     * Validates provided CSRF token against stored token
     * 
     * @param string|null $token Token to validate
     * @return bool Validation result
     */
    public function validateCsrfToken(?string $token): bool
    {
        if (!$token) return false;
        $storedToken = $this->session->get('csrf_token');
        return $storedToken && hash_equals($storedToken, $token);
    }

    /**
     * Escapes HTML special characters in a string
     * 
     * @param string $data Input string to escape
     * @return string Escaped string
     */
    public function escape(string $data): string
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitizes input data recursively
     * 
     * @param mixed $data Input data to sanitize
     * @return mixed Sanitized data
     */
    public function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        return $this->escape(trim($data));
    }

    /**
     * Validates and filters integer input
     * 
     * @param mixed $value Value to validate
     * @return int Validated integer
     */
    public function validateInteger($value): int
    {
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    /**
     * Validates email format
     * 
     * @param string $email Email to validate
     * @return bool Validation result
     */
    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Hashes password using secure algorithm
     * 
     * @param string $password Password to hash
     * @return string Hashed password
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
