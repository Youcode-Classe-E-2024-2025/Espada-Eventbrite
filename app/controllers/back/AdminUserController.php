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
        $users = $this->userService->getAllUsers();

        return $this->render('back/admin-users.html.twig', ['users' => $users]);
    }

    public function search()
    {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $roleId = isset($_GET['role_id']) ? (int)$_GET['role_id'] : null;
        $status = isset($_GET['status']) ? (int)$_GET['status'] : null;

        $results = $this->userService->searchFilterUsers($keyword, $roleId, $status);

        return $this->render('back/admin-users.html.twig', [
            'users' => $results,
            'keyword' => $keyword,
            'role_id' => $roleId,
            'status' => $status
        ]);
    }

    public function filter()
    {
        $roleId = isset($_GET['role_id']) ? (int)$_GET['role_id'] : null;
        $status = isset($_GET['status']) ? (int)$_GET['status'] : null;

        $results = $this->userService->filterUsers($roleId, $status);

        return $this->render('back/admin-users.html.twig', [
            'users' => $results,
            'role_id' => $roleId,
            'status' => $status
        ]);
    }

    public function updateStatus()
    {
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
        $status = isset($_POST['status']) ? (int)$_POST['status'] : null;

        if ($userId && $status) {
            $this->userService->updateUserStatus($userId, $status);
            $this->redirect('/back/admin-users.html.twig');
        }

        $this->redirect('/back/admin-users.html.twig');
        exit;
    }
}
