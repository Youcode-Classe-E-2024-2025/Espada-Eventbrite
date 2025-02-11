<?php

namespace app\core;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Stores a value in session
     * 
     * @param string $key Session key
     * @param mixed $value Value to store
     * @return void
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieves a value from session
     * 
     * @param string $key Session key
     * @return mixed|null Value from session or null if not found
     */
    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Destroys current session
     * 
     * @return void
     */
    public function destroy()
    {
        session_destroy();
    }
}
