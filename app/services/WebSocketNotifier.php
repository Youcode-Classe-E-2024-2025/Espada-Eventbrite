<?php

namespace App\services;


class WebSocketNotifier
{
    private static ?WebSocketNotifier $instance = null;
    private string $host;
    private int $port;
    private $socket;

    private function __construct(string $host = 'localhost', int $port = 3009)
    {
        $this->host = $host;
        $this->port = $port;
        $this->connect();
    }

    public static function getInstance(): WebSocketNotifier
    {
        if (self::$instance === null) {
            self::$instance = new WebSocketNotifier();
        }
        return self::$instance;
    }

    private function connect(): void
    {
        $this->socket = fsockopen($this->host, $this->port, $errno, $errstr, 30);
        if (!$this->socket) {
            error_log("WebSocket connection failed: $errstr ($errno)");
        }
    }

    public function sendNotification(string $eventName, string $action, array $userIds): bool
    {
        if (!$this->socket) {
            $this->connect(); // Try to reconnect if needed
            if (!$this->socket) {
                return false;
            }
        }

        $message = json_encode([
            'event_name' => $eventName,
            'action' => $action,
            'users' => $userIds
        ]);

        fwrite($this->socket, $message);
        return true;
    }

    public function __destruct()
    {
        if ($this->socket) {
            fclose($this->socket);
        }
    }
}
