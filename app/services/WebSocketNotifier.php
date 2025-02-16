<?php
namespace App\Services;

use WebSocket\Client;
use Exception;

class WebSocketNotifier {
    private static $instance = null;
    private $client;
    private $host;
    private $port;

    private function __construct(string $host = 'localhost', int $port = 3009) {
        $this->host = $host;
        $this->port = $port;
        $this->connect();
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect(): bool {
        try {
            $this->client = new Client("ws://{$this->host}:{$this->port}");
            return true;
        } catch (Exception $e) {
            error_log("WebSocket connection failed: " . $e->getMessage());
            return false;
        }
    }

    public function send(string $userName, string $action, string $userId): bool {
        if (!$this->client) {
            throw new Exception("Not connected to WebSocket server");
        }

        $data = [
            'action' => $action,
            'event_name' => $userName,
            'user_ids' => $userId
        ];

        try {
            $this->client->send(json_encode($data));
            return true;
        } catch (Exception $e) {
            error_log("Error sending WebSocket message: " . $e->getMessage());
            return false;
        }
    }

    public function close(): void {
        if ($this->client) {
            $this->client->close();
        }
    }
}
