<?php
namespace App\controllers\front;

use App\services\UserService;
use App\core\Session;
use App\core\Validator;
use App\core\View;

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

    // Handle user login
    public function login(): void
    { return;
        $requestData = $this->getJsonInput();

        // Define validation rules
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required']
        ];

        // Validate the data
        if (!$this->validator->validate($requestData, $rules)) {
            $this->respond(false, $this->validator->getErrors(), 422);
            return;
        }

        // Attempt to authenticate the user
        $user = $this->userService->login($requestData['email'], $requestData['password']);
        if ($user) {
            $this->session->set('user', $user);
           header('Location: /');
            return;
        }

        $this->respond(false, ['Invalid email or password'], 401);
    }

    // Handle user registration
    public function register(): void
    {
        $requestData = $this->getJsonInput();

        // Define validation rules
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
            'confirm_password' => ['required'],
            'role_id' => ['required'], // Assuming roles are managed
            'username' => ['required']
        ];

        // Validate the data
        if (!$this->validator->validate($requestData, $rules)) {
            $this->respond(false, $this->validator->getErrors(), 422);
            return;
        }

        // Check if passwords match
        if ($requestData['password'] !== $requestData['confirm_password']) {
            $this->respond(false, ['Passwords do not match'], 422);
            return;
        }

        // Hash the password
        $requestData['password'] = password_hash($requestData['password'], PASSWORD_BCRYPT);
        unset($requestData['confirm_password']); // Remove confirm_password before storing

        // Attempt to register the user
        if ($this->userService->register($requestData)) {
            $this->respond(true, ['Registration successful']);
            return;
        }

        $this->respond(false, ['Registration failed'], 500);
    }

    // Helper method to get JSON input
    private function getJsonInput(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    // Helper method to standardize responses
    private function respond(bool $success, array $message, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        echo json_encode(['success' => $success, 'message' => $message]);
    }
}
