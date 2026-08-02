<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentAlertNotification extends Notification
{
    use Queueable;

    public $payment;
    public $alertType;

    /**
     * Create a new notification instance.
     */
    public function __construct($payment, $alertType = 'overdue')
    {
        $this->payment = $payment;
        $this->alertType = $alertType;
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
        $kioskCode = $this->payment->contract->kiosk->code ?? 'N/A';
        $customerName = $this->payment->contract->customer->name ?? 'Khách hàng';
        
        $title = $this->alertType === 'overdue' ? 'Thanh toán quá hạn' : 'Sắp đến hạn thanh toán';
        $message = "Kiosk {$kioskCode} của {$customerName} " . ($this->alertType === 'overdue' ? 'đã quá hạn' : 'sắp đến hạn') . ' thanh toán.';
        
        return [
            'title' => $title,
            'message' => $message,
            'url' => route('admin.payments.index', ['status' => $this->alertType === 'overdue' ? 'overdue' : 'pending']),
            'type' => $this->alertType === 'overdue' ? 'danger' : 'warning',
        ];
    }
}
