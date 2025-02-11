<?php

namespace App\controllers\front;

use App\services\UserService;
use App\core\Session;
use App\core\Validator;
use App\core\View;

class AuthController extends View
{
    private UserService $userService;
    private Session $session;
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
        echo $view->render('/front/auth.twig');
        // echo $view->render('base.html.twig',[]);
    }
    // Handle the registration process
    public function register(array $requestData)
    {
        
        $rules = [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
            'confirm_password' => ['required'],
            'role' => ['required']
        ];

        // Validate the data
        if (!$this->validator->validate($requestData, $rules)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        // Check if passwords match
        if ($requestData['password'] !== $requestData['confirm_password']) {
            return [
                'success' => false,
                'errors' => ['password' => 'Passwords do not match']
            ];
        }

        // Check if email is already registered
        if ($this->userService->isEmailRegistered($requestData['email'])) {
            return [
                'success' => false,
                'errors' => ['email' => 'Email is already registered']
            ];
        }

        // Attempt to register the user
        if ($this->userService->register($requestData)) {
            return ['success' => true];
        }

        return ['success' => false, 'errors' => ['general' => 'Registration failed']];
    }

    // Handle the login process
    public function login(array $requestData)
    {
        // Validation rules
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required']
        ];

        // Validate the data
        if (!$this->validator->validate($requestData, $rules)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        // Attempt to authenticate the user
        $user = $this->userService->login($requestData['email'], $requestData['password']);
        if ($user) {
            $this->session->set('user', $user);
            return ['success' => true];
        }

        return [
            'success' => false,
            'errors' => ['general' => 'Invalid email or password']
        ];
    }

    // Handle logout
    public function logout()
    {
        $this->session->destroy();
    }
}
