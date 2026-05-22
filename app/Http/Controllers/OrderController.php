<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $base = Order::query()->where('user_id', $request->user()->id)->withCount('items');

        $activeOrders = (clone $base)
            ->whereIn('order_status', ['pending', 'confirmed', 'shipping'])
            ->latest()
            ->get();

        $completedOrders = (clone $base)
            ->where('order_status', 'completed')
            ->latest()
            ->get();

        $cancelledOrders = (clone $base)
            ->whereIn('order_status', ['cancelled', 'refunded'])
            ->latest()
            ->get();

        return view('orders.index', [
            'activeOrders' => $activeOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->with(['items.book', 'payments'])
            ->findOrFail($order->id);

        $bookIds = $order->items()->pluck('book_id')->filter()->unique()->values()->all();

        if (empty($bookIds)) {
            $reviewsByBookId = collect();
        } else {
            $reviewsByBookId = $request->user()
                ->reviews()
                ->whereIn('book_id', $bookIds)
                ->get()
                ->keyBy('book_id');
        }

        return view('orders.show', [
            'order' => $order,
            'reviewsByBookId' => $reviewsByBookId,
        ]);
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->with(['items', 'payments'])
            ->findOrFail($order->id);

        if ($order->order_status !== 'pending') {
            return back()->with('error', 'Chỉ có thể hủy đơn khi đơn còn ở trạng thái pending.');
        }

        DB::transaction(function () use ($order): void {
            foreach ($order->items as $item) {
                $book = Book::query()->find($item->book_id);

                if (! $book) {
                    continue;
                }

                $book->update([
                    'stock_quantity' => $book->stock_quantity + $item->quantity,
                    'status' => 'available',
                ]);
            }

            $order->update([
                'order_status' => 'cancelled',
                'payment_status' => $order->payment_status === 'paid' ? 'refunded' : $order->payment_status,
            ]);

            $order->payments()->update([
                'status' => 'failed',
            ]);
        });

        return redirect()->route('orders.show', $order)->with('success', 'Đã hủy đơn hàng thành công.');
    }
}
