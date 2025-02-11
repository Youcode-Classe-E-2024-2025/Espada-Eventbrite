<?php

namespace App\controllers\front;

use App\services\UserService;
use App\core\Session;
use App\core\Validator;
use App\core\View;
use App\core\Controller;
use Google_Client;
use Google_Service_Oauth2;
use Exception;

class AuthController extends Controller
{
    // private UserService $userService;
    protected $session;
    private Validator $validator;

    public function __construct()
    {
        // $this->userService = new UserService();
        $this->session = new Session();
        $this->validator = new Validator();
    }

    public function index(): void
    {
        $view = new View();
        echo $view->render('front/auth.twig',[]);
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

        //Check if passwords match
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

    //Handle the login process
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

    public function loginWithGoogle(){
        $clientId = $_ENV['GOOGLE_CLIENT_ID'];
        $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];
        $redirectUri = $_ENV['GOOGLE_REDIRECT_URI'];

        $client = new Google_Client();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($redirectUri);
        $client->addScope('email');
        $client->addScope('profile');

        // Generate and redirect to Google OAuth URL
        $authUrl = $client->createAuthUrl();
        header('Location: ' . $authUrl);
        exit();
    }

    public function handleGoogleCallback() {
        try {
            $clientId = $_ENV['GOOGLE_CLIENT_ID'];
            $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];
            $redirectUri = $_ENV['GOOGLE_REDIRECT_URI'];

            // Validate environment variables
            if (empty($clientId) || empty($clientSecret) || empty($redirectUri)) {
                throw new Exception('Missing Google OAuth configuration');
            }

            $client = new Google_Client();
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->setRedirectUri($redirectUri);

            // Exchange authorization code for access token
            $code = $_GET['code'] ?? null;
            if (!$code) {
                throw new Exception('No authorization code found');
            }

            $token = $client->fetchAccessTokenWithAuthCode($code);
            
            // Detailed error logging for token fetch
            if (isset($token['error'])) {
                error_log('Google Token Error: ' . json_encode($token['error']));
                throw new Exception('Google OAuth Token Error: ' . $token['error']);
            }

            $client->setAccessToken($token);

            // Fetch user info
            $oauth2 = new Google_Service_Oauth2($client);
            $userInfo = $oauth2->userinfo->get();

            // Validate user info
            if (!$userInfo || !$userInfo->email) {
                throw new Exception('Unable to retrieve user information');
            }

            // Check if user exists or create new user
            $userService = new UserService();
            $user = $userService->findOrCreateGoogleUser([
                'email' => $userInfo->email,
                'name' => $userInfo->name,
                'avatar' => $userInfo->picture
            ]);

            // Set user session
            $this->session->set('user', $user);

            // Redirect to profile or dashboard
            header('Location: /profile');
            exit();

        } catch (Exception $e) {
            // Comprehensive error logging
            error_log('Google OAuth Error: ' . $e->getMessage());
            error_log('Error Trace: ' . $e->getTraceAsString());

            // Redirect to login with error
            header('Location: /login?error=google_auth_failed');
            exit();
        }
    }
}
