<?php

namespace App\Livewire\Layout;

use App\Services\CRM\BrokerNotificationService;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $isOpen = false;

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function render()
    {
        $service = app(BrokerNotificationService::class);
        $data = $service->getBrokerAlerts();

        return view('livewire.layout.notification-bell', [
            'totalUnread' => $data['total_unread'],
            'alerts'      => $data['alerts'],
        ]);
    }
}
