<?php

namespace App\controllers\back;

use App\core\Controller;
use App\services\CategoryTagService;

class CategoryTagController extends Controller
{
    private CategoryTagService $categoryTagService;

    public function __construct()
    {
        parent::__construct();
        $this->categoryTagService = new CategoryTagService();
    }

    public function index()
    {
        $this->logger->info('Fetching all categories and tags');
        $categories = $this->categoryTagService->getAllCategories();
        $tags = $this->categoryTagService->getAllTags();
        $messages = $this->session->get('messages') ?? [];
        $csrfToken = $this->security->generateCsrfToken();

        return $this->render('back/categoryTag.html.twig', [
            'categories' => $categories,
            'tags' => $tags,
            'messages' => $messages,
            'csrf_token' => $csrfToken
        ]);
    }

    public function addTags()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $tags = $data['tags'];
            $csrfToken = $data['csrf_token'] ?? '';

            if (!$this->security->validateCsrfToken($csrfToken)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
                exit;
            }

            $result = $this->categoryTagService->addTags($tags);

            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit;
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    public function addCategory()
    {
        $title = $_POST['category_title'] ?? '';
        $icon = $_POST['category_icon'] ?? '';
        $this->logger->info('Attempting to add category: ' . $title);
        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!$this->security->validateCsrfToken($csrfToken)) {
            $this->session->set('error', 'Invalid CSRF token.');
            $this->redirect('/admin/categoryTag');
            exit;
        }

        if (!empty($title)) {
            $this->categoryTagService->addCategory($title, $icon);
            $this->session->set('success', 'Category added successfully.');
        } else {
            $this->session->set('error', 'Category title cannot be empty.');
        }

        $this->redirect('/admin/categoryTag');
    }

    public function updateCategory()
    {
        $id = $_POST['category_id'] ?? '';
        $name = $_POST['category_name'] ?? '';
        $icon = $_POST['category_icon'] ?? '';
        $csrfToken = $_POST['csrf_token'] ?? '';
        $this->logger->info('Attempting to update category with ID: ' . $id);

        if (!$this->security->validateCsrfToken($csrfToken)) {
            $this->session->set('error', 'Invalid CSRF token.');
            $this->redirect('/admin/categoryTag');
            exit;
        }

        if (!empty($name)) {
            $this->categoryTagService->updateCategory($id, $name, $icon);
            $this->session->set('success', 'Category updated successfully.');
        } else {
            $this->session->set('error', 'Category title cannot be empty.');
        }

        $this->redirect('/admin/categoryTag');
    }

    public function deleteCategory()
    {
        $id = $_POST['category_id'] ?? '';
        $this->logger->info('Attempting to delete category with ID: ' . $id);
        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!$this->security->validateCsrfToken($csrfToken)) {
            $this->session->set('error', 'Invalid CSRF token.');
            $this->redirect('/admin/categoryTag');
            exit;
        }

        if (!empty($id)) {
            $this->categoryTagService->deleteCategory($id);
            $this->session->set('success', 'Category deleted successfully.');
        } else {
            $this->session->set('error', 'Category id not found.');
        }

        $this->redirect('/admin/categoryTag');
    }

    public function deleteTag()
    {
        $id = $_POST['tag_id'] ?? '';
        $this->logger->info('Attempting to delete tag with ID: ' . $id);
        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!$this->security->validateCsrfToken($csrfToken)) {
            $this->session->set('error', 'Invalid CSRF token.');
            $this->redirect('/admin/categoryTag');
            exit;
        }

        if (!empty($id)) {
            $this->categoryTagService->deleteTag($id);
            $this->session->set('success', 'Tag deleted successfully.');
        } else {
            $this->session->set('error', 'Tag id not found.');
        }

        $this->redirect('/admin/categoryTag');
    }
}
