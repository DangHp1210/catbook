<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Order $order, protected ?string $oldStatus = null)
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation for database storage.
     */
    public function toDatabase(object $notifiable): array
    {
        $newStatus = $this->order->order_status;

        $titles = [
            'pending' => 'Đơn hàng đang chờ xử lý',
            'confirmed' => 'Đơn hàng đã được xác nhận',
            'shipping' => 'Đơn hàng đang vận chuyển',
            'completed' => 'Đơn hàng đã hoàn tất',
            'cancelled' => 'Đơn hàng đã bị huỷ',
        ];

        $title = $titles[$newStatus] ?? ('Trạng thái: ' . $newStatus);

        $message = sprintf('Đơn hàng #%s đã chuyển sang trạng thái "%s".',
            $this->order->order_code,
            $title
        );

        return [
            'type' => 'order_status',
            'title' => $title,
            'message' => $message,
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'url' => route('orders.show', $this->order),
        ];
    }
}
