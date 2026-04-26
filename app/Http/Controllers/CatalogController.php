<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function categories(Request $request): View
    {
        $keyword = trim((string) $request->query('q', ''));

        $categories = Category::query()
            ->withCount('books')
            ->orderBy('name')
            ->get();

        $booksQuery = Book::query()
            ->with(['authors:id,name', 'categories:id,name'])
            ->where('status', 'available')
            ->latest();

        $books = $this->applyKeywordFilter($booksQuery, $keyword)
            ->paginate(12)
            ->withQueryString();

        return view('catalog.categories', [
            'categories' => $categories,
            'books' => $books,
            'keyword' => $keyword,
            'selectedCategory' => null,
            'heading' => 'Tat ca danh muc sach',
        ]);
    }

    public function category(Request $request, Category $category): View
    {
        $keyword = trim((string) $request->query('q', ''));

        $categories = Category::query()
            ->withCount('books')
            ->orderBy('name')
            ->get();

        $booksQuery = $category->books()
            ->with(['authors:id,name', 'categories:id,name'])
            ->where('status', 'available')
            ->latest('books.created_at');

        $books = $this->applyKeywordFilter($booksQuery, $keyword)
            ->paginate(12)
            ->withQueryString();

        return view('catalog.categories', [
            'categories' => $categories,
            'books' => $books,
            'keyword' => $keyword,
            'selectedCategory' => $category,
            'heading' => 'Danh muc: '.$category->name,
        ]);
    }

    private function applyKeywordFilter(Builder $query, string $keyword): Builder
    {
        if ($keyword === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($keyword): void {
            $builder->where('title', 'like', "%{$keyword}%")
                ->orWhere('isbn', 'like', "%{$keyword}%")
                ->orWhereHas('authors', function (Builder $authorQuery) use ($keyword): void {
                    $authorQuery->where('name', 'like', "%{$keyword}%");
                });
        });
    }

    public function book(Book $book): View
    {
        $book->load([
            'authors:id,name',
            'categories:id,name',
            'publisher:id,name',
            'reviews.user:id,full_name',
        ]);

        $book->loadCount('reviews');
        $book->loadAvg('reviews', 'rating');

        $relatedBooks = Book::query()
            ->with(['authors:id,name'])
            ->where('status', 'available')
            ->where('id', '!=', $book->id)
            ->whereHas('categories', function ($query) use ($book) {
                $query->whereIn('categories.id', $book->categories->pluck('id'));
            })
            ->latest()
            ->limit(4)
            ->get();

        return view('catalog.book-detail', [
            'book' => $book,
            'relatedBooks' => $relatedBooks,
        ]);
    }
}
