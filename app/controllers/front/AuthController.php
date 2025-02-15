<?php

namespace App\controllers\front;

use App\services\UserService;
use App\core\Controller;
use App\core\View;
use App\core\request;
// use App\core\Validator;
use Google\Client;
use Google\Service\Oauth2;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AuthController extends Controller

{
    private UserService $userService;
    private Client $client;
    private request $request;
    protected Logger $logger;
    // private  $validator;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
        $this->request = new request();
        
        // Create a logger
        $this->logger = new Logger('google_oauth');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/google_oauth.log', Logger::DEBUG));
        
        // Load Google OAuth configuration
        $this->client = new Client();
        
        // Require environment variables for Google OAuth
        $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? null;
        $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? null;
        $redirectUri = $_ENV['GOOGLE_REDIRECT_URI'] ?? null;
        
        if (!$clientId || !$clientSecret || !$redirectUri) {
            $this->logger->error('Missing Google OAuth configuration', [
                'client_id' => $clientId ? 'set' : 'missing',
                'client_secret' => $clientSecret ? 'set' : 'missing',
                'redirect_uri' => $redirectUri ? 'set' : 'missing'
            ]);
            throw new \RuntimeException('Google OAuth configuration is incomplete. Please check your .env file.');
        }
        
        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
        $this->client->setRedirectUri($redirectUri);
        
        // Configure scopes
        $this->client->setScopes([
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile'
        ]);
        
        // Set the PSR-3 compatible logger
        $this->client->setLogger($this->logger);
    }

    public function index(): void
    {
           
        if(!empty($this->session->get('user'))){
            $this->redirect('/');
        }else{
            echo $this->render('front/auth.twig', []);
        }
        
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

            echo $this->render('front/auth.twig', ['messege' => 'all feilds should are required ']);
        }

        $user = $this->userService->loginuser($requestData['email'], $requestData['password']);
        if ($user) {
            $this->session->set('user', $user);
            header('Location: /dashboard');
            die();
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

            echo $this->render('front/auth.twig', ['messege' => 'all feilds should are required ']);
        }

        //Check if passwords match
        if ($requestData['password'] !== $requestData['confirm_password']) {

            echo $this->render('front/auth.twig', ['messege' => 'mismatch in passowrds feilds']);
        }

        // Hash the password
        $requestData['password'] = password_hash($requestData['password'], PASSWORD_BCRYPT);
        unset($requestData['confirm_password']); // Remove confirm_password before storing

        // Attempt to register the user
        if ($this->userService->register($requestData)) {


            echo $this->render('front/auth.twig', ['messege' => 'log in now']);

            return;
        }

        echo $this->render('front/auth.twig', ['messege' => 'somthing went wrong']);
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


    public function googleLogin()
    {
        try {
            // Generate a unique state parameter to prevent CSRF
            $state = bin2hex(random_bytes(16));
            $this->session->set('oauth_state', $state);
            
            // Set state to prevent CSRF attacks
            $this->client->setState($state);
            
            // Create authorization URL
            $authUrl = $this->client->createAuthUrl();
            
            // Redirect to Google's OAuth page
            header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
            exit();
        } catch (\Exception $e) {
            // Log the full error for debugging
            error_log('Google Login Error: ' . $e->getMessage());
            
            // Redirect to login page with error
            header('Location: /login');
            exit();
        }
    }

    public function googleCallback()
    {
        try {
            // Verify state to prevent CSRF
            $storedState = $this->session->get('oauth_state');
            $receivedState = $this->request->get('state');
            
            if (empty($storedState) || $storedState !== $receivedState) {
                throw new \Exception('Invalid OAuth state');
            }
            
            // Exchange authorization code for access token
            $code = $this->request->get('code');
            if (empty($code)) {
                throw new \Exception('No authorization code found');
            }
            
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            // Check for errors in token retrieval
            if (isset($token['error'])) {
                throw new \Exception('Token retrieval error: ' . $token['error']);
            }
            
            // Set the access token
            $this->client->setAccessToken($token);
            
            // Get user info
            $oauth2Service = new Oauth2($this->client);
            $userInfo = $oauth2Service->userinfo->get();
            
            // Find or create user
            $user = $this->findOrCreateGoogleUser([
                'google_id' => $userInfo->getId(),
                'email' => $userInfo->getEmail(),
                'name' => $userInfo->getName(),
                'picture' => $userInfo->getPicture()
            ]);
            
            if ($user) {
                // Set user session
                $this->session->set('user', $user);
                header('Location: /dashboard');
                exit();
            } else {
                throw new \Exception('Unable to create or find user');
            }
        } catch (\Exception $e) {
            // Comprehensive error logging
            error_log('Google Callback Error: ' . $e->getMessage());
            header('Location: /login');
            exit();
        }
    }

    public function findOrCreateGoogleUser(array $googleUser): array|false
    {
        try {
            // Validate input
            if (empty($googleUser['google_id']) || empty($googleUser['email'])) {
                $this->logger->error('Invalid Google User Data', [
                    'google_id' => $googleUser['google_id'] ?? 'MISSING',
                    'email' => $googleUser['email'] ?? 'MISSING'
                ]);
                throw new \InvalidArgumentException('Google ID and email are required');
            }

            // Log incoming user data
            $this->logger->info('Processing Google User', [
                'google_id' => $googleUser['google_id'],
                'email' => $googleUser['email'],
                'name' => $googleUser['name'] ?? 'N/A'
            ]);

            // Check if user exists by google_id
            $user = $this->userService->findByGoogleId($googleUser['google_id']);
            
            if (!$user) {
                // If not, check by email
                $user = $this->userService->findByEmail($googleUser['email']);
                
                if (!$user) {
                    // Create new user
                    $userData = [
                        'email' => $googleUser['email'],
                        'username' => $this->generateUniqueUsername($googleUser['name']),
                        'password' => bin2hex(random_bytes(16)), // Generate a secure random password
                        'avatar' => $googleUser['picture'] ?? null,
                        'google_id' => $googleUser['google_id'],
                        'is_google' => true,
                        'role' => 2, // Explicitly set to USER role
                        'role_id' => 2 // Ensure role_id is also set
                    ];
                    
                    // Log user creation attempt
                    $this->logger->info('Attempting to create new Google user', $userData);
                    
                    $createdUser = $this->userService->create($userData);
                    
                    if (!$createdUser) {
                        $this->logger->error('Failed to create user', [
                            'userData' => $userData
                        ]);
                        throw new \Exception('User creation failed');
                    }
                    
                    return $createdUser;
                }
                
                // Existing user without Google ID: update with Google information
                $updatedUser = $this->userService->updateGoogleId($user['id'], [
                    'google_id' => $googleUser['google_id'],
                    'is_google' => true,
                    'avatar' => $googleUser['picture'] ?? $user['avatar']
                ]);

                if (!$updatedUser) {
                    $this->logger->error('Failed to update user with Google ID', [
                        'user_id' => $user['id'],
                        'google_id' => $googleUser['google_id']
                    ]);
                    throw new \Exception('User update failed');
                }

                return $updatedUser;
            }
            
            // User already exists with Google ID
            // Optionally update user information if it has changed
            if ($this->shouldUpdateUserInfo($user, $googleUser)) {
                $updatedUser = $this->userService->updateUserInfo($user['id'], [
                    'username' => $googleUser['name'],
                    'avatar' => $googleUser['picture']
                ]);

                if (!$updatedUser) {
                    $this->logger->error('Failed to update user information', [
                        'user_id' => $user['id'],
                        'name' => $googleUser['name']
                    ]);
                }

                return $updatedUser ?? $user;
            }
            
            return $user;
        } catch (\Exception $e) {
            // Log the full error
            $this->logger->error('Google User Creation/Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'google_user' => $googleUser
            ]);
            
            return false;
        }
    }

    // Helper method to generate a unique username
    private function generateUniqueUsername(string $name): string 
    {
        // Remove special characters and convert to lowercase
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        
        // If username is too short, append random string
        if (strlen($baseUsername) < 3) {
            $baseUsername .= bin2hex(random_bytes(3));
        }
        
        // Check if username exists and make it unique
        $username = $baseUsername;
        $counter = 1;
        while ($this->userService->findByUsername($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }

    // Helper method to determine if user info needs updating
    private function shouldUpdateUserInfo(array $existingUser, array $googleUser): bool 
    {
        return 
            $existingUser['username'] !== $googleUser['name'] || 
            $existingUser['avatar'] !== $googleUser['picture'];
    }
}
