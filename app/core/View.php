<?php

namespace App\core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{
    private $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../views');

        $this->twig = new Environment($loader, [
            'cache' => false,
            'debug' => true,
            'auto_reload' => true
        ]);
    }

    /**
     * Renders a template with provided data
     * 
     * @param string $template Template file name
     * @param array $data Data to pass to the template
     * @return string Rendered template content
     */
    public function render(string $template, array $data = []): string
    {

        return $this->twig->render($template, $data);
    }
}
