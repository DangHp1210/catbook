<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    public function categories(Request $request): View
    {
        return $this->renderCatalogPage($request, null);
    }

    public function category(Request $request, Category $category): View
    {
        return $this->renderCatalogPage($request, $category);
    }

    private function renderCatalogPage(Request $request, ?Category $routeCategory): View
    {
        $keyword = trim((string) $request->query('q', ''));
        $sortBy = (string) $request->query('sort', 'newest');
        $viewMode = (string) $request->query('view', 'grid');
        $stockFilter = (string) $request->query('stock', 'all');
        $languageFilter = trim((string) $request->query('language', ''));

        if (! in_array($sortBy, ['newest', 'price_asc', 'price_desc', 'title_asc'], true)) {
            $sortBy = 'newest';
        }

        if (! in_array($viewMode, ['grid', 'list'], true)) {
            $viewMode = 'grid';
        }

        if (! in_array($stockFilter, ['all', 'in_stock', 'out_of_stock'], true)) {
            $stockFilter = 'all';
        }

        $parentCategories = Category::query()
            ->whereNull('parent_id')
            ->withCount('children')
            ->orderBy('name')
            ->get();

        $selectedParent = null;
        $selectedChild = null;

        if ($routeCategory !== null) {
            if ($routeCategory->parent_id === null) {
                $selectedParent = $routeCategory;
            } else {
                $selectedChild = $routeCategory;
                $selectedParent = $routeCategory->parent;
            }
        }

        if ($selectedParent === null) {
            $parentSlug = trim((string) $request->query('parent', ''));
            if ($parentSlug !== '') {
                $selectedParent = $parentCategories->firstWhere('slug', $parentSlug);
            }
        }

        $childCategories = collect();
        if ($selectedParent !== null) {
            $childCategories = Category::query()
                ->where('parent_id', $selectedParent->id)
                ->withCount('books')
                ->orderBy('name')
                ->get();
        }

        if ($selectedChild === null) {
            $childSlug = trim((string) $request->query('child', ''));
            if ($childSlug !== '') {
                $selectedChild = $childCategories->firstWhere('slug', $childSlug);
            }
        }

        $booksQuery = Book::query()
            ->with(['authors:id,name', 'categories:id,name'])
            ->where('status', 'available');

        if ($selectedParent !== null) {
            $childIds = $childCategories->pluck('id')->all();
            $categoryIds = array_merge([$selectedParent->id], $childIds);

            $booksQuery->whereHas('categories', function (Builder $query) use ($categoryIds): void {
                $query->whereIn('categories.id', $categoryIds);
            });
        }

        if ($selectedChild !== null) {
            $booksQuery->whereHas('categories', function (Builder $query) use ($selectedChild): void {
                $query->where('categories.id', $selectedChild->id);
            });
        }

        $priceBounds = (clone $booksQuery)
            ->selectRaw('MIN(COALESCE(discount_price, price)) as min_price, MAX(COALESCE(discount_price, price)) as max_price')
            ->first();

        $minPossiblePrice = (int) floor((float) ($priceBounds?->min_price ?? 0));
        $maxPossiblePrice = (int) ceil((float) ($priceBounds?->max_price ?? 0));

        if ($maxPossiblePrice < $minPossiblePrice) {
            $maxPossiblePrice = $minPossiblePrice;
        }

        $minPrice = (int) $request->query('min_price', $minPossiblePrice);
        $maxPrice = (int) $request->query('max_price', $maxPossiblePrice);

        $minPrice = max($minPossiblePrice, $minPrice);
        $maxPrice = min($maxPossiblePrice, $maxPrice);
        if ($minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        if ($maxPossiblePrice > 0) {
            $booksQuery->whereBetween(DB::raw('COALESCE(discount_price, price)'), [$minPrice, $maxPrice]);
        }

        $availableLanguages = (clone $booksQuery)
            ->whereNotNull('language')
            ->where('language', '!=', '')
            ->distinct()
            ->orderBy('language')
            ->pluck('language');

        if ($languageFilter !== '' && $availableLanguages->contains($languageFilter)) {
            $booksQuery->where('language', $languageFilter);
        } else {
            $languageFilter = '';
        }

        if ($stockFilter === 'in_stock') {
            $booksQuery->where('stock_quantity', '>', 0);
        }

        if ($stockFilter === 'out_of_stock') {
            $booksQuery->where(function (Builder $query): void {
                $query->where('stock_quantity', '<=', 0)
                    ->orWhere('status', 'out_of_stock');
            });
        }

        $booksQuery = $this->applyKeywordFilter($booksQuery, $keyword);

        switch ($sortBy) {
            case 'price_asc':
                $booksQuery->orderByRaw('COALESCE(discount_price, price) asc');
                break;
            case 'price_desc':
                $booksQuery->orderByRaw('COALESCE(discount_price, price) desc');
                break;
            case 'title_asc':
                $booksQuery->orderBy('title');
                break;
            default:
                $booksQuery->latest();
                break;
        }

        $books = $booksQuery->paginate(12)->withQueryString();

        return view('catalog.categories', [
            'books' => $books,
            'keyword' => $keyword,
            'heading' => $selectedChild !== null
                ? 'Danh muc con: '.$selectedChild->name
                : ($selectedParent !== null ? 'Danh muc: '.$selectedParent->name : 'Tat ca danh muc sach'),
            'selectedCategory' => $selectedChild,
            'parentCategories' => $parentCategories,
            'selectedParent' => $selectedParent,
            'childCategories' => $childCategories,
            'selectedChild' => $selectedChild,
            'sortBy' => $sortBy,
            'viewMode' => $viewMode,
            'stockFilter' => $stockFilter,
            'languageFilter' => $languageFilter,
            'availableLanguages' => $availableLanguages,
            'minPossiblePrice' => $minPossiblePrice,
            'maxPossiblePrice' => $maxPossiblePrice,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'totalCategories' => Category::count(),
            'totalBooks' => Book::query()->where('status', 'available')->count(),
        ]);
    }

    private function applyKeywordFilter(Builder|BelongsToMany $query, string $keyword): Builder|BelongsToMany
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
            'reviews' => function ($query): void {
                $query->latest()->with('user:id,full_name');
            },
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
