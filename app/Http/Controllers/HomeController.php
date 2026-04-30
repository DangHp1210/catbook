<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\CartItem;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredBooks = Book::query()
            ->with(['authors:id,name', 'categories:id,name'])
            ->where('status', 'available')
            ->orderByDesc('stock_quantity')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $newArrivals = Book::query()
            ->with(['authors:id,name'])
            ->where('status', 'available')
            ->latest()
            ->limit(4)
            ->get();

        $topCategories = Category::query()
            ->withCount('books')
            ->orderByDesc('books_count')
            ->limit(8)
            ->get();

        $topAuthors = Author::query()
            ->withCount('books')
            ->orderByDesc('books_count')
            ->limit(4)
            ->get();

        // Calculate cart count for authenticated user (use user's cart relation)
        $cartCount = 0;
        if (auth()->check()) {
            $cart = auth()->user()->cart()->with('items')->first();
            $cartCount = $cart ? (int) $cart->items->sum('quantity') : 0;
        }

        return view('welcome', [
            'featuredBooks' => $featuredBooks,
            'newArrivals' => $newArrivals,
            'topCategories' => $topCategories,
            'topAuthors' => $topAuthors,
            'stats' => [
                'books' => Book::count(),
                'authors' => Author::count(),
                'categories' => Category::count(),
            ],
            'cartCount' => $cartCount,
        ]);
    }
}
