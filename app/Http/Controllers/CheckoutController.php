<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $cart = Cart::query()
            ->where('user_id', $request->user()->id)
            ->with(['items.book'])
            ->first();

        $items = $cart?->items ?? collect();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống, chưa thể thanh toán.');
        }

        $subtotal = $items->sum(function ($item): float {
            return (float) $item->unit_price * $item->quantity;
        });

        $shippingFee = $this->shippingFee($subtotal);

        $defaultAddress = $request->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->latest('id')
            ->first();

        return view('checkout.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'shippingFee' => $shippingFee,
            'total' => $subtotal + $shippingFee,
            'defaultAddress' => $defaultAddress,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cod,bank_transfer,momo,vnpay'],
            'note' => ['nullable', 'string'],
        ]);

        $cart = Cart::query()
            ->where('user_id', $request->user()->id)
            ->with(['items.book'])
            ->first();

        $items = $cart?->items ?? collect();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống, chưa thể tạo đơn hàng.');
        }

        foreach ($items as $item) {
            $book = $item->book;

            if (! $book || $book->status !== 'available' || $book->stock_quantity < $item->quantity) {
                return redirect()->route('cart.index')->with('error', 'Một số sản phẩm không còn đủ tồn kho. Vui lòng kiểm tra lại giỏ hàng.');
            }
        }

        $subtotal = $items->sum(function ($item): float {
            $unitPrice = (float) ($item->book->discount_price ?? $item->book->price);

            return $unitPrice * $item->quantity;
        });

        $shippingFee = $this->shippingFee($subtotal);
        $totalAmount = $subtotal + $shippingFee;

        $order = DB::transaction(function () use ($request, $validated, $items, $subtotal, $shippingFee, $totalAmount) {
            $order = Order::query()->create([
                'user_id' => $request->user()->id,
                'order_code' => $this->generateOrderCode(),
                'recipient_name' => $validated['recipient_name'],
                'recipient_phone' => $validated['recipient_phone'],
                'shipping_address' => $validated['shipping_address'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'unpaid',
                'order_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($items as $item) {
                $book = $item->book;
                $unitPrice = (float) ($book->discount_price ?? $book->price);

                $order->items()->create([
                    'book_id' => $book->id,
                    'book_title_snapshot' => $book->title,
                    'unit_price' => $unitPrice,
                    'quantity' => $item->quantity,
                    'discount_amount' => 0,
                    'total_price' => $unitPrice * $item->quantity,
                ]);

                $newStock = $book->stock_quantity - $item->quantity;
                $book->update([
                    'stock_quantity' => $newStock,
                    'status' => $newStock > 0 ? $book->status : 'out_of_stock',
                ]);
            }

            Payment::query()->create([
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'transaction_code' => null,
                'amount' => $totalAmount,
                'status' => 'pending',
                'paid_at' => null,
            ]);

            $request->user()->cart?->items()->delete();

            return $order;
        });

        // Handle online payment methods (VNPay & MoMo)
        if ($validated['payment_method'] === 'vnpay') {
            return redirect()->route('payment.vnpay', ['order_id' => $order->id]);
        }

        if ($validated['payment_method'] === 'momo') {
            return redirect()->route('payment.momo', ['order_id' => $order->id]);
        }

        // For COD and bank transfer, show order confirmation
        return redirect()->route('orders.show', $order)->with('success', 'Đặt hàng thành công. Mã đơn: '.$order->order_code);
    }

    private function shippingFee(float $subtotal): float
    {
        return $subtotal >= 299000 ? 0 : 30000;
    }

    private function generateOrderCode(): string
    {
        do {
            $orderCode = 'CB'.now()->format('YmdHis').random_int(100, 999);
        } while (Order::query()->where('order_code', $orderCode)->exists());

        return $orderCode;
    }
}
