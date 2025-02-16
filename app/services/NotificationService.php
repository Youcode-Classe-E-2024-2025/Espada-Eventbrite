<?php

namespace App\services;

use App\repository\NotificationRepository;

class NotificationService {
    private NotificationRepository $notificationRepo;

    public function __construct() {
        $this->notificationRepo = new NotificationRepository();
    }

    /**
     * Send a new notification
     */
    public function sendNotification(array $notificationData) {
        // var_dump($notificationData);
        // echo '<br>';
        return $this->notificationRepo->create($notificationData);
    }

    /**
     * Retrieve notifications for a specific user
     */
    public function getUserNotifications($userId) {
        return $this->notificationRepo->getNotificationsForUser($userId);
    }

    /**
     * Mark a notification as read
     */
    public function markNotificationAsRead($notificationId) {
        return $this->notificationRepo->markAsRead($notificationId);
    }
}
