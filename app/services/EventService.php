<?php

namespace App\services;

use App\repository\EventRepository;
use App\models\Event;

class EventService
{
    private EventRepository $eventRepository;
    private Event $event;

    public function __construct(EventRepository $eventRepository, Event $event)
    {
        $this->eventRepository = $eventRepository;
        $this->event = $event;
    }

    public function searchEvents(string $keyword, int $page, int $limit): array
    {
        if (!$this->event->validateSearchKeyword($keyword)) {
            return [
                'events' => [],
                'total' => 0,
                'pages' => 0
            ];
        }

        $events = $this->eventRepository->searchEvents($keyword, $page, $limit);
        $totalCount = $this->eventRepository->searchCount($keyword);

        return [
            'events' => $events,
            'total' => $totalCount,
            'pages' => ceil($totalCount / $limit)
        ];
    }
}
