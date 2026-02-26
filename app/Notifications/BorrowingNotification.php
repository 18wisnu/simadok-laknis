<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BorrowingNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $equipmentName;
    protected $action;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $equipmentName, $action = 'borrow')
    {
        $this->title = $title;
        $this->message = $message;
        $this->equipmentName = $equipmentName;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'equipment_name' => $this->equipmentName,
            'action' => $this->action,
            'icon' => $this->action === 'borrow' ? 'fa-hand-holding' : 'fa-arrow-rotate-left',
        ];
    }
}
