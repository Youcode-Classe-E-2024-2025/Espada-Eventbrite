<?php

namespace App\core;

class Logger
{
    private $logFile;

    public function __construct()
    {
        $this->logFile = __DIR__ . '/../../logs/app.log';

        // Create logs directory if it doesn't exist
        if (!is_dir(__DIR__ . '/../../logs')) {
            mkdir(__DIR__ . '/../../logs', 0777, true);
        }
    }

    /**
     * Logs an informational message
     * 
     * @param string $message Message to log
     * @return void
     */
    public function info(string $message): void
    {
        $this->log('INFO', $message);
    }


    /**
     * Logs an error message
     * 
     * @param string $message Message to log
     * @return void
     */
    public function error(string $message): void
    {
        $this->log('ERROR', $message);
    }

    /**
     * Logs a debug message
     * 
     * @param string $message Message to log
     * @return void
     */
    public function debug(string $message): void
    {
        $this->log('DEBUG', $message);
    }

    /**
     * Writes a formatted log entry to the log file
     * 
     * @param string $level Log level (INFO, ERROR, DEBUG)
     * @param string $message Message to log
     * @return void
     */
    private function log(string $level, string $message): void
    {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}
