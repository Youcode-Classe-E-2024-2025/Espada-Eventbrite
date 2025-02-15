<?php

namespace App\controllers\back;

use App\core\Controller;
use App\services\EventService;

class AdminEventController extends Controller
{
    private EventService $eventService;

    public function __construct()
    {
        parent::__construct();
        $this->eventService = new EventService();
    }

    public function index()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 5;

        $this->logger->info('Fetching all events');
        $events = $this->eventService->getPaginatedEvents($page, $perPage);
        $totalEvents = $this->eventService->getTotalEvents();
        $totalPages = ceil($totalEvents / $perPage);

        $messages = $this->session->get('messages') ?? [];
        $csrfToken = $this->security->generateCsrfToken();

        return $this->render('back/events.html.twig', [
            'events' => $events,
            'messages' => $messages,
            'csrf_token' => $csrfToken,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalEvents' => $totalEvents
        ]);
    }

    public function search()
    {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 5;

        $this->logger->info('Searching events with keyword: ' . $keyword);

        $events = $this->eventService->searchEvents($keyword, $page, $perPage);
        $totalEvents = $this->eventService->getTotalSearchResults($keyword);
        $totalPages = ceil($totalEvents / $perPage);

        $messages = $this->session->get('messages') ?? [];

        return $this->render('back/events.html.twig', [
            'events' => $events,
            'keyword' => $keyword,
            'messages' => $messages,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalEvents' => $totalEvents
        ]);
    }

    public function updateStatus()
    {
        $eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : null;
        $status = isset($_POST['status']) ? (int)$_POST['status'] : null;
        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!$this->security->validateCsrfToken($csrfToken)) {
            $this->logger->error('Invalid CSRF token.');
            $this->session->set('error', 'Invalid CSRF token.');
            $this->redirect('/admin/events');
            exit;
        }

        if ($eventId && isset($status)) {
            $this->logger->info('Updating event status: ' . $eventId . ' to ' . $status);
            $this->eventService->updateEventStatus($eventId, $status);
            // $this->redirect('/admin/events');
        } else {
            $this->logger->error('Failed to update event status.');
            $this->session->set('error', 'Failed to update event status.');
        }

        $this->redirect('/admin/events');
        exit;
    }

    public function delete()
    {
        $eventId = isset($_POST['event_id']) ? (int)$_POST['event_id'] : null;
        $csrfToken = $_POST['csrf_token'] ?? '';

        if (!$this->security->validateCsrfToken($csrfToken)) {
            $this->logger->error('Invalid CSRF token.');
            $this->session->set('error', 'Invalid CSRF token.');
            $this->redirect('/admin/events');
            exit;
        }

        if ($eventId) {
            $this->logger->info('Deleting event: ' . $eventId);
            $this->eventService->deleteEvent($eventId);
        } else {
            $this->logger->error('Failed to delete event.');
            $this->session->set('error', 'Failed to delete event.');
        }

        $this->redirect('/admin/events');
    }

    public function sort()
    {
        $sortBy = $_GET['sort'] ?? '';

        $events = $this->eventService->sortEvents($sortBy);

        header('Content-Type: application/json');
        echo json_encode(['events' => $events]);
        exit;
    }
}
