<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventVendorDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $url,
        public ?int $organizationId = null,
        public string $activity = 'vendor'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'organization_id' => $this->organizationId,
            'activity' => $this->activity,
            'created_label' => now()->format('d-m-Y H:i'),
        ];
    }
}

