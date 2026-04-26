<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = Cart::query()
            ->where('user_id', $request->user()->id)
            ->with(['items.book.authors:id,name'])
            ->first();

        $items = $cart?->items ?? collect();

        $subtotal = $items->sum(function (CartItem $item): float {
            return (float) $item->unit_price * $item->quantity;
        });

        return view('cart.index', [
            'items' => $items,
            'subtotal' => $subtotal,
        ]);
    }

    public function store(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        if ($book->status !== 'available' || $book->stock_quantity < 1) {
            return back()->with('error', 'Sách hiện không khả dụng để thêm vào giỏ hàng.');
        }

        $quantity = (int) ($validated['quantity'] ?? 1);

        $cart = Cart::query()->firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $item = $cart->items()->where('book_id', $book->id)->first();
        $targetQuantity = $item ? ($item->quantity + $quantity) : $quantity;

        if ($targetQuantity > $book->stock_quantity) {
            return back()->with('error', 'Số lượng vượt quá tồn kho hiện tại.');
        }

        $unitPrice = (float) ($book->discount_price ?? $book->price);

        if ($item) {
            $item->update([
                'quantity' => $targetQuantity,
                'unit_price' => $unitPrice,
            ]);
        } else {
            $cart->items()->create([
                'book_id' => $book->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]);
        }

        return back()->with('success', 'Đã thêm sách vào giỏ hàng.');
    }

    public function destroy(Request $request, CartItem $item): RedirectResponse
    {
        $belongsToCurrentUser = Cart::query()
            ->where('id', $item->cart_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $belongsToCurrentUser) {
            abort(403);
        }

        $item->delete();

        return back()->with('success', 'Đã xóa sách khỏi giỏ hàng.');
    }
}
