<?php

namespace App\core;

use Google\Service\BeyondCorp\Resource\V;

abstract class Controller
{
    protected $db;
    protected $view;
    protected $security;
    protected $session;

    protected $validator;

    public function __construct()
    {
        $this->db = new Database();
        $this->view = new View();
        $this->security = new Security();
        $this->session = new Session();
        $this->validator = new Validator();
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
        return $this->view->render($template, $data);
    }

    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit();
    }
}
