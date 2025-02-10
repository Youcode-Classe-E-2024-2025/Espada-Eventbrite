<?php
namespace app\core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View {
    private $twig;

    public function __construct() {
        $loader = new FilesystemLoader(dirname(__DIR__) . '/views');
        $this->twig = new Environment($loader, [
            'cache' => false,
            'debug' => true,
            'auto_reload' => true
        ]);

        // Add the asset function
        $this->twig->addFunction(new \Twig\TwigFunction('asset', function ($path) {
            return '/assets/' . ltrim($path, '/');
        }));

        // Add the url function
        $this->twig->addFunction(new \Twig\TwigFunction('url', function ($path) {
            return '/' . ltrim($path, '/');
        }));
    }

    public function render($template, $data = []) {
        // Add user data to all views if user is logged in
        if (isset($_SESSION['user'])) {
            $data['user'] = $_SESSION['user'];
        }
        echo $this->twig->render($template, $data);
    }
}
