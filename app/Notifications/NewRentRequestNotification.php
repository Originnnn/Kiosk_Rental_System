<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRentRequestNotification extends Notification
{
    public $rentRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct($rentRequest)
    {
        $this->rentRequest = $rentRequest;
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
            'title' => 'Yêu cầu thuê Kiosk mới',
            'message' => 'Khách hàng ' . ($this->rentRequest->customer_name ?? 'Mới') . ' vừa gửi yêu cầu thuê Kiosk.',
            'url' => route('admin.rental_requests.index'),
            'type' => 'success',
        ];
    }
}
