<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function open(Request $request, string $notificationId): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user, 403);

        $notification = $user->notifications()->whereKey($notificationId)->firstOrFail();
        $notification->markAsRead();

        $data = is_array($notification->data) ? $notification->data : (array) $notification->data;
        if ($user->role === 'staff') {
            $defaultUrl = route('staff.orders.index');
        } elseif ($user->role === 'admin') {
            $defaultUrl = route('admin.orders.index');
        } else {
            $defaultUrl = route('orders.index');
        }
        $url = $data['url'] ?? $defaultUrl;

        if (is_string($url) && str_contains($url, '/preview')) {
            $orderCode = $data['order_code'] ?? null;

            if (is_string($orderCode) && $orderCode !== '') {
                $url = ($user->role === 'staff' ? route('staff.orders.index') : route('admin.orders.index')) . '?open=' . urlencode($orderCode);
            } else {
                $url = $defaultUrl;
            }
        }

        return redirect()->to($url);
    }

    public function markAll(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user, 403);

        try {
            $user->unreadNotifications->markAsRead();
        } catch (\Throwable $e) {
            logger()->error('Failed to mark notifications as read: ' . $e->getMessage());
            return back()->with('error', 'Không thể đánh dấu tất cả thông báo.');
        }

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }
}
