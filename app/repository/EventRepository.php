<?php

namespace app\repository;

use app\models\Event;
use app\core\Database;

class EventRepository extends BaseRepository
{
    protected $table = 'events';
    private Event $eventModel;

    public function __construct(Database $database, Event $event)
    {
        parent::__construct($database);
        $this->eventModel = $event;
    }

    public function searchEvents($keyword, int $page, int $limit)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " WHERE title ILIKE :keyword OR description ILIKE :keyword";
            $params['keyword'] = "%{$keyword}%";
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        return $this->db->findAll($sql, $params);
    }

    public function searchCount($keyword): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " WHERE title ILIKE :keyword OR description ILIKE :keyword";
            $params['keyword'] = "%{$keyword}%";
        }

        $result = $this->db->find($sql, $params);
        return (int) $result['count'];
    }

    public function search(string $keyword, int $page, int $limit)
    {
        return $this->searchEvents($keyword, $page, $limit);
    }
}
