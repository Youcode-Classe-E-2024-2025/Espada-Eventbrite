<?php

namespace App\core;

use Google\Service\BeyondCorp\Resource\V;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

abstract class Controller
{
    protected $db;
    protected $view;
    protected $security;
    protected $session;
    protected $validator;
    protected Logger $logger;

    public function __construct()
    {
        $this->db = new Database();
        $this->view = new View();
        $this->security = new Security();
        $this->session = new Session();
        $this->validator = new Validator();

        // Initialize base logger
        $this->logger = new Logger('app');
        $this->logger->pushHandler(
            new StreamHandler(
                __DIR__ . '/../../logs/app.log', 
                Logger::DEBUG
            )
        );
    }

    /**
     * Renders a view template with provided data
     * 
     * @param string $template Path to the template file
     * @param array $data Data to be passed to the view
     * @return string Rendered view content
     */
    protected function render(string $template, array $data = [])
    {
        $data['session'] = new Session();
        $this->logger->debug("Rendering template: {$template}");
        return $this->view->render($template, $data);
    }

    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit();
    }

    // Optional: Centralized logging method
    protected function log($message, $level = Logger::INFO)
    {
        $this->logger->log($level, $message);
    }
}
