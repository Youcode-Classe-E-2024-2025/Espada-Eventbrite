<?php

namespace App\controllers\front;

use App\services\UserService;
use App\core\Controller;
use App\core\View;
use App\core\Validator;

use App\core\Session;

use Google_Client;
use Google_Service_Oauth2;
use Exception;

class AuthController extends Controller

{
    private UserService $userService;
    private Validator $validator;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->session = new Session();
        $this->validator = new Validator();
    }

    public function index(): void
    {
        $view = new View();
        echo $view->render('front/auth.twig', []);
    }

    // Handle user login
    public function login(): void
    {
        $requestData = $this->getJsonInput();

        // Define validation rules
        $rules = [
            'email' => ['required'],
            'password' => ['required']
        ];

        // Validate the data
        if (!$this->validator->validate($requestData, $rules)) {

            $view = new View();
            echo $view->render('front/auth.twig', ['messege' => 'all feilds should are required ']);
        }

        $user = $this->userService->loginuser($requestData['email'], $requestData['password']);
        if ($user) {
            $this->session->set('user', $user);
            header('Location: /');
            die();
            return;
        }

        $view = new View();
        echo $view->render('front/auth.twig', ['messege' => 'password or email are wrong ']);
    }

    public function register(): void
    {
        $requestData = $this->getJsonInput();



        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
            'confirm_password' => ['required'],

            'username' => ['required']
        ];

        // Validate the data
        if (!$this->validator->validate($requestData, $rules)) {

            $view = new View();
            echo $view->render('front/auth.twig', ['messege' => 'all feilds should are required ']);
        }

        //Check if passwords match
        if ($requestData['password'] !== $requestData['confirm_password']) {

            $view = new View();
            echo $view->render('front/auth.twig', ['messege' => 'mismatch in passowrds feilds']);
        }

        // Hash the password
        $requestData['password'] = password_hash($requestData['password'], PASSWORD_BCRYPT);
        unset($requestData['confirm_password']); // Remove confirm_password before storing

        // Attempt to register the user
        if ($this->userService->register($requestData)) {


            $view = new View();
            echo $view->render('front/auth.twig', ['messege' => 'log in now']);

            return;
        }

        $view = new View();
        echo $view->render('front/auth.twig', ['messege' => 'somthing went wrong']);
    }

    // Helper method to get JSON input
    private function getJsonInput(): array
    {
        $data = file_get_contents('php://input');

        // Handle URL-encoded data (as you are receiving it)
        parse_str($data, $parsedData);

        // var_dump($parsedData);

        return $parsedData;  // This will be an associative array
    }

    public function logout()
    {
        $this->session->destroy();
        header('Location: / ');
    }
}
