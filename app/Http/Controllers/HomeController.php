<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $bestSellers = Book::query()
            ->with(['authors:id,name', 'categories:id,name'])
            ->where('status', 'available')
            ->withSum('orderItems as sold_quantity', 'quantity')
            ->orderByDesc('sold_quantity')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $newArrivals = Book::query()
            ->with(['authors:id,name'])
            ->where('status', 'available')
            ->latest()
            ->limit(6)
            ->get();

        $discountBooks = Book::query()
            ->with(['authors:id,name'])
            ->where('status', 'available')
            ->whereNotNull('discount_price')
            ->whereColumn('discount_price', '<', 'price')
            ->orderByRaw('(price - discount_price) DESC')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $cartCount = Auth::check()
            ? (int) (Auth::user()?->cart?->items()->sum('quantity') ?? 0)
            : 0;

        return view('home.index', [
            'featuredBooks' => $bestSellers,
            'bestSellers' => $bestSellers,
            'newArrivals' => $newArrivals,
            'discountBooks' => $discountBooks,
            'stats' => [
                'books' => Book::count(),
                'authors' => Author::count(),
                'categories' => Category::count(),
            ],
            'cartCount' => $cartCount,
        ]);
    }
}
