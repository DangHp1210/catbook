<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $ownedOrder = Order::query()
            ->where('user_id', $request->user()->id)
            ->where('id', $order->id)
            ->where('order_status', 'completed')
            ->whereHas('items', function ($query) use ($book): void {
                $query->where('book_id', $book->id);
            })
            ->firstOrFail();

        Review::query()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'book_id' => $book->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return redirect()
            ->route('orders.show', $ownedOrder)
            ->with('success', 'Đã lưu đánh giá cho sách "'.$book->title.'".');
    }
}