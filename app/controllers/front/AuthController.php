<?php

namespace App\controllers\front;

use App\services\UserService;
use App\core\Session;
use App\core\Validator;
use App\core\View;
use Google_Client;
use Google_Service_Oauth2;

class AuthController
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
        echo $view->render('front/auth.twig',[]);
        echo $view->render('base.html.twig',[]);
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

    public function loginWithGougle()
    {
        var_dump("hi");
        die();
        // Load environment variables for Google OAuth credentials
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 3));
        $dotenv->load();
        // Configure Google OAuth Client
        $client = new Google_Client();
        $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
        $client->addScope('email');
        $client->addScope('profile');

        // If the code is not set, redirect to Google OAuth consent screen
        if (!isset($_GET['code'])) {
            $authUrl = $client->createAuthUrl();
            header('Location: ' . $authUrl);
            exit();
        }

        // Exchange authorization code for access token
        try {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token);

            // Get user profile information
            $oauth2Service = new Google_Service_Oauth2($client);
            $googleUser = $oauth2Service->userinfo->get();

            // Prepare user data for registration/login
            $userData = [
                'email' => $googleUser->email,
                'first_name' => $googleUser->givenName,
                'last_name' => $googleUser->familyName,
                'role' => 'participant', // Default role, adjust as needed
                'google_id' => $googleUser->id
            ];

            // Check if user exists, if not, register
            if (!$this->userService->isEmailRegistered($userData['email'])) {
                $this->userService->register($userData);
            }

            // Login the user
            $user = $this->userService->loginWithGoogle($userData['email'], $userData['google_id']);
            
            if ($user) {
                $this->session->set('user', $user);
                // Redirect to dashboard or home page
                header('Location: /');
                exit();
            } else {
                // Handle login failure
                $this->session->set('error', 'Google Sign-In failed');
                header('Location: /auth');
                exit();
            }
        } catch (\Exception $e) {
            // Handle exceptions
            $this->session->set('error', 'An error occurred during Google Sign-In: ' . $e->getMessage());
            header('Location: /auth');
            exit();
        }
    }
}
