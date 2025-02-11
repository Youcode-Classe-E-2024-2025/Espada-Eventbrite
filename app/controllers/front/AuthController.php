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
    private UserService $userService;
    protected $session;
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
        
        // Clear any previously stored token
        $client->revokeToken();
        
        // Set scopes
        $client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
        $client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
        
        // Additional configuration for more robust token handling
        $client->setAccessType('offline');  // Request a refresh token
        $client->setPrompt('consent');      // Always show consent screen
        
        // Generate a secure random state
        $state = bin2hex(random_bytes(16));
        var_dump($state);
        die();
        
        // Generate and redirect to Google OAuth URL
        $authUrl = $client->createAuthUrl() . '&state=' . $state;
        
        // Store state in session to prevent CSRF
        $_SESSION['oauth_state'] = $state;
        
        header('Location: ' . $authUrl);
        exit();
    }

    public function handleGoogleCallback() {
        try {
            // Verify state to prevent CSRF
            $sessionState = $_SESSION['oauth_state'] ?? null;
            $requestState = $_GET['state'] ?? null;
            
            if (!$sessionState || !$requestState || $sessionState !== $requestState) {
                throw new \Exception('Invalid OAuth state');
            }
            
            $clientId = $_ENV['GOOGLE_CLIENT_ID'];
            $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];
            $redirectUri = $_ENV['GOOGLE_REDIRECT_URI'];

            $client = new Google_Client();
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->setRedirectUri($redirectUri);
            
            // Add scopes
            $client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);
            $client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
            
            // Exchange authorization code for access token
            $code = $_GET['code'] ?? null;
            if (!$code) {
                throw new \Exception('No authorization code found');
            }
            
            // Attempt to fetch access token
            $token = $client->fetchAccessTokenWithAuthCode($code);
            
            // Check for errors
            if (isset($token['error'])) {
                // Log the full error details
                error_log('Google OAuth Error Details: ' . print_r($token, true));
                
                // Handle specific error cases
                switch ($token['error']) {
                    case 'invalid_grant':
                        throw new \Exception('The authorization code has expired or been used. Please try logging in again.');
                    default:
                        throw new \Exception('Google OAuth Error: ' . $token['error']);
                }
            }
            
            // Validate the token
            if (!$token || !is_array($token) || !isset($token['access_token'])) {
                throw new \Exception('Invalid or missing access token');
            }
            
            // Set the access token
            $client->setAccessToken($token);
            
            // Verify the token
            if ($client->getAccessToken()) {
                // Get user info
                $oauth2Service = new Google_Service_Oauth2($client);
                $userInfo = $oauth2Service->userinfo->get();
                
                // Check if user exists, create or login
                $userService = new UserService();
                $user = $userService->findOrCreateGoogleUser([
                    'email' => $userInfo->getEmail(),
                    'name' => $userInfo->getName(),
                    'avatar' => $userInfo->getPicture(),
                    'google_id' => $userInfo->getId()
                ]);
                
                // Set user session
                $this->session->set('user', $user);
                
                // Clear the stored state
                unset($_SESSION['oauth_state']);
                
                // Redirect to dashboard or home page
                header('Location: /profile');
                exit();
            } else {
                throw new \Exception('Failed to validate access token');
            }
            
        } catch (\Exception $e) {
            // Log the error
            error_log('Google OAuth Callback Error: ' . $e->getMessage());
            
            // Clear the stored state
            unset($_SESSION['oauth_state']);
            
            // Redirect back to login with error message
            echo $_SESSION['error'] = $e->getMessage();
            // header('Location: /login');
            exit();
        }
    }
}
