<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminOrderPlacedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Order $order)
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
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $role = $notifiable->role ?? 'admin';

        return [
            'type' => 'order',
            'title' => 'Đơn hàng mới #' . $this->order->order_code,
            'message' => sprintf(
                '%s vừa đặt đơn trị giá %sđ bằng %s.',
                $this->order->recipient_name,
                number_format((float) $this->order->total_amount, 0, ',', '.'),
                $this->paymentLabel($this->order->payment_method)
            ),
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'customer_name' => $this->order->recipient_name,
            'payment_method' => $this->order->payment_method,
            'total_amount' => (float) $this->order->total_amount,
            'url' => ($role === 'staff' ? '/staff/orders' : '/admin/orders') . '?open=' . urlencode($this->order->order_code),
        ];
    }

    private function paymentLabel(string $method): string
    {
        return match ($method) {
            'cod' => 'COD',
            'bank_transfer' => 'chuyển khoản',
            'momo' => 'MoMo',
            'vnpay' => 'VNPay',
            default => $method,
        };
    }
}
