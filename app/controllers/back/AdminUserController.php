<?php

namespace App\controllers\back;

use App\core\Controller;
use App\services\UserService;

class AdminUserController extends Controller
{
    private UserService $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }

    public function index()
    {
        $this->logger->info('Fetching all users');
        $users = $this->userService->getAllUsers();
        $messages = $this->session->get('messages') ?? [];
        $csrfToken = $this->security->generateCsrfToken();

        return $this->render('back/users.html.twig', ['users' => $users, 'messages' => $messages, 'csrf_token' => $csrfToken]);
    }

    public function search()
    {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $roleId = isset($_GET['role_id']) ? (int)$_GET['role_id'] : null;
        $status = isset($_GET['status']) ? $_GET['status'] : null;

        $this->logger->info('Searching users with keyword: ' . $keyword);
        $results = $this->userService->searchFilterUsers($keyword, $roleId, $status);
        $messages = $this->session->get('messages') ?? [];

        return $this->render('back/users.html.twig', [
            'users' => $results,
            'keyword' => $keyword,
            'role_id' => $roleId,
            'status' => $status,
            'messages' => $messages,
        ]);
    }

    public function filter()
    {
        $roleId = isset($_GET['role_id']) ? (int)$_GET['role_id'] : null;
        $status = isset($_GET['status']) ? (int)$_GET['status'] : null;

        $this->logger->info('Filtering users with role: ' . $roleId . ' and status: ' . $status);

        $results = $this->userService->filterUsers($roleId, $status);
        $messages = $this->session->get('messages') ?? [];

        return $this->render('back/users.html.twig', [
            'users' => $results,
            'role_id' => $roleId,
            'status' => $status,
            'messages' => $messages,
        ]);
    }

    public function updateStatus()
    {
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
        $status = isset($_POST['status']) ? (int)$_POST['status'] : null;
        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!$this->security->validateCsrfToken($csrfToken)) {
            $this->logger->error('Invalid CSRF token.');
            $this->session->set('error', 'Invalid CSRF token.');
            $this->redirect('/admin/users');
            exit;
        }

        if ($userId && isset($status)) {
            $this->logger->info('Updating user status: ' . $userId . ' to ' . $status);
            $this->userService->updateUserStatus($userId, $status);
            $this->redirect('/admin/users');
        } else {
            $this->logger->error('Failed to update user status.');
            $this->session->set('error', 'Failed to update user status.');
        }

        // $this->redirect('/back/users');
        exit;
    }
}
