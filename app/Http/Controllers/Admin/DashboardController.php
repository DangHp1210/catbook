<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Order;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'users' => User::query()->count(),
            'books' => Book::query()->count(),
            'authors' => Author::query()->count(),
            'categories' => Category::query()->count(),
            'publishers' => Publisher::query()->count(),
            'orders' => Order::query()->count(),
            'revenue' => (float) Order::query()
                ->whereIn('order_status', ['confirmed', 'shipping', 'completed'])
                ->sum('total_amount'),
        ];

        $recentOrders = Order::query()
            ->with('user')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function users(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('full_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->withCount('orders')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.users', [
            'users' => $users,
            'q' => $q,
        ]);
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(['customer', 'staff', 'admin'])],
            'status' => ['required', Rule::in(['active', 'blocked', 'pending'])],
        ]);

        $user->update($data);

        return back()->with('success', 'Đã cập nhật người dùng.');
    }

    public function books(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $books = Book::query()
            ->with(['publisher', 'categories:id', 'authors:id,name'])
            ->withCount('categories')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('isbn', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $publishers = Publisher::query()->orderBy('name')->get(['id', 'name']);
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);
        $authors = Author::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.books', [
            'books' => $books,
            'publishers' => $publishers,
            'categories' => $categories,
            'authors' => $authors,
            'q' => $q,
        ]);
    }

    public function searchAuthors(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        $authors = Author::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        $exactMatch = false;
        if ($q !== '') {
            $exactMatch = Author::query()
                ->whereRaw('LOWER(name) = ?', [Str::lower($q)])
                ->exists();
        }

        return response()->json([
            'data' => $authors->map(fn (Author $author) => [
                'id' => $author->id,
                'name' => $author->name,
            ]),
            'can_create' => $q !== '' && ! $exactMatch,
            'query' => $q,
        ]);
    }

    public function storeBook(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:50', Rule::unique('books', 'isbn')],
            'description' => ['nullable', 'string'],
            'cover_image_file' => ['nullable', 'image', 'max:3072'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'language' => ['nullable', 'string', 'max:100'],
            'publication_year' => ['nullable', 'integer', 'min:1900', 'max:' . (now()->year + 1)],
            'status' => ['required', Rule::in(['available', 'hidden', 'out_of_stock'])],
            'publisher_id' => ['nullable', 'integer', Rule::exists('publishers', 'id')],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', Rule::exists('categories', 'id')],
            'author_ids' => ['nullable', 'array'],
            'author_ids.*' => ['integer', Rule::exists('authors', 'id')],
            'author_names' => ['nullable', 'array'],
            'author_names.*' => ['nullable', 'string', 'max:255'],
        ]);

        $title = trim((string) $data['title']);
        $isbnInput = trim((string) ($data['isbn'] ?? ''));
        $coverImagePath = $request->hasFile('cover_image_file')
            ? $request->file('cover_image_file')->store('book-covers', 'public')
            : null;

        $book = Book::query()->create([
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::lower(Str::random(5)),
            'isbn' => $isbnInput !== '' ? $isbnInput : null,
            'description' => $data['description'] ?? null,
            'cover_image' => $coverImagePath,
            'price' => $data['price'],
            'discount_price' => $data['discount_price'] ?? null,
            'stock_quantity' => $data['stock_quantity'],
            'page_count' => $data['page_count'] ?? null,
            'language' => $data['language'] ?? null,
            'publication_year' => $data['publication_year'] ?? null,
            'status' => $data['status'],
            'publisher_id' => $data['publisher_id'] ?? null,
        ]);

        $book->categories()->sync($data['category_ids'] ?? []);
        $book->authors()->sync($this->resolveAuthorIds($data['author_ids'] ?? [], $data['author_names'] ?? []));

        return back()->with('success', 'Đã thêm sách mới.');
    }

    public function updateBook(Request $request, Book $book): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('books', 'slug')->ignore($book->id)],
            'isbn' => ['nullable', 'string', 'max:50', Rule::unique('books', 'isbn')->ignore($book->id)],
            'description' => ['nullable', 'string'],
            'cover_image_file' => ['nullable', 'image', 'max:3072'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'language' => ['nullable', 'string', 'max:100'],
            'publication_year' => ['nullable', 'integer', 'min:1900', 'max:' . (now()->year + 1)],
            'status' => ['required', Rule::in(['available', 'hidden', 'out_of_stock'])],
            'publisher_id' => ['nullable', 'integer', Rule::exists('publishers', 'id')],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', Rule::exists('categories', 'id')],
            'author_ids' => ['nullable', 'array'],
            'author_ids.*' => ['integer', Rule::exists('authors', 'id')],
            'author_names' => ['nullable', 'array'],
            'author_names.*' => ['nullable', 'string', 'max:255'],
        ]);

        $title = trim((string) $data['title']);
        $slugInput = trim((string) ($data['slug'] ?? ''));
        $isbnInput = trim((string) ($data['isbn'] ?? ''));
        $coverImagePath = $book->cover_image;

        if ($request->hasFile('cover_image_file')) {
            if (
                $coverImagePath
                && ! str_starts_with($coverImagePath, 'http://')
                && ! str_starts_with($coverImagePath, 'https://')
                && Storage::disk('public')->exists($coverImagePath)
            ) {
                Storage::disk('public')->delete($coverImagePath);
            }

            $coverImagePath = $request->file('cover_image_file')->store('book-covers', 'public');
        }

        $book->update([
            'title' => $title,
            'slug' => $slugInput !== '' ? Str::slug($slugInput) : Str::slug($title),
            'isbn' => $isbnInput !== '' ? $isbnInput : null,
            'description' => $data['description'] ?? null,
            'cover_image' => $coverImagePath,
            'price' => $data['price'],
            'discount_price' => $data['discount_price'] ?? null,
            'stock_quantity' => $data['stock_quantity'],
            'page_count' => $data['page_count'] ?? null,
            'language' => $data['language'] ?? null,
            'publication_year' => $data['publication_year'] ?? null,
            'status' => $data['status'],
            'publisher_id' => $data['publisher_id'] ?? null,
        ]);

        $book->categories()->sync($data['category_ids'] ?? []);
        $book->authors()->sync($this->resolveAuthorIds($data['author_ids'] ?? [], $data['author_names'] ?? []));

        return back()->with('success', 'Đã cập nhật sách.');
    }

    public function destroyBook(Book $book): RedirectResponse
    {
        if ($book->orderItems()->exists()) {
            return back()->with('error', 'Không thể xóa sách đã phát sinh đơn hàng.');
        }

        if ($book->cartItems()->exists()) {
            return back()->with('error', 'Không thể xóa sách đang có trong giỏ hàng.');
        }

        $book->categories()->detach();
        $book->authors()->detach();
        $book->delete();

        return back()->with('success', 'Đã xóa sách.');
    }

    public function categories(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $allCategories = Category::query()
            ->with('parent:id,name,slug,parent_id')
            ->withCount(['books', 'children'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'parent_id']);

        $categories = $this->buildCategoryTree($allCategories);

        $categoryOptions = Category::query()
            ->with('parent:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);

        return view('admin.categories', [
            'categories' => $categories,
            'allCategories' => $allCategories,
            'categoryOptions' => $categoryOptions,
            'q' => $q,
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')],
            'parent_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
        ]);

        $name = trim((string) $data['name']);
        $slug = trim((string) ($data['slug'] ?? ''));

        Category::query()->create([
            'name' => $name,
            'slug' => $slug !== '' ? $slug : Str::slug($name) . '-' . Str::lower(Str::random(5)),
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        return back()->with('success', 'Đã tạo danh mục mới.');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($category->id)],
            'parent_id' => ['nullable', 'integer', Rule::exists('categories', 'id'), 'not_in:' . $category->id],
        ]);

        $name = trim((string) $data['name']);
        $slug = trim((string) ($data['slug'] ?? ''));

        $category->update([
            'name' => $name,
            'slug' => $slug !== '' ? $slug : Str::slug($name),
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        return back()->with('success', 'Đã cập nhật danh mục.');
    }

    public function destroyCategory(Category $category): RedirectResponse
    {
        if ($category->books()->exists()) {
            return back()->with('error', 'Không thể xóa danh mục đang chứa sách.');
        }

        if ($category->children()->exists()) {
            return back()->with('error', 'Không thể xóa danh mục cha khi còn danh mục con.');
        }

        $category->delete();

        return back()->with('success', 'Đã xóa danh mục.');
    }

    public function publishers(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $publishers = Publisher::query()
            ->withCount('books')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('website', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.publishers', [
            'publishers' => $publishers,
            'q' => $q,
        ]);
    }

    public function storePublisher(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        Publisher::query()->create($data);

        return back()->with('success', 'Đã tạo nhà xuất bản mới.');
    }

    public function updatePublisher(Request $request, Publisher $publisher): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $publisher->update($data);

        return back()->with('success', 'Đã cập nhật nhà xuất bản.');
    }

    public function destroyPublisher(Publisher $publisher): RedirectResponse
    {
        if ($publisher->books()->exists()) {
            return back()->with('error', 'Không thể xóa nhà xuất bản đã có sách.');
        }

        $publisher->delete();

        return back()->with('success', 'Đã xóa nhà xuất bản.');
    }

    public function authors(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $authors = Author::query()
            ->withCount('books')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('bio', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.authors', [
            'authors' => $authors,
            'q' => $q,
        ]);
    }

    public function storeAuthor(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'avatar_file' => ['nullable', 'image', 'max:3072'],
        ]);

        $avatarPath = $request->hasFile('avatar_file')
            ? $request->file('avatar_file')->store('author-avatars', 'public')
            : null;

        Author::query()->create([
            'name' => $data['name'],
            'bio' => $data['bio'] ?? null,
            'avatar_url' => $avatarPath,
        ]);

        return back()->with('success', 'Đã thêm tác giả mới.');
    }

    public function updateAuthor(Request $request, Author $author): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'avatar_file' => ['nullable', 'image', 'max:3072'],
        ]);

        $avatarPath = $author->avatar_url;

        if ($request->hasFile('avatar_file')) {
            if (
                $avatarPath
                && ! str_starts_with($avatarPath, 'http://')
                && ! str_starts_with($avatarPath, 'https://')
                && ! str_starts_with($avatarPath, '/')
                && Storage::disk('public')->exists($avatarPath)
            ) {
                Storage::disk('public')->delete($avatarPath);
            }

            $avatarPath = $request->file('avatar_file')->store('author-avatars', 'public');
        }

        $author->update([
            'name' => $data['name'],
            'bio' => $data['bio'] ?? null,
            'avatar_url' => $avatarPath,
        ]);

        return back()->with('success', 'Đã cập nhật tác giả.');
    }

    public function destroyAuthor(Author $author): RedirectResponse
    {
        if ($author->books()->exists()) {
            return back()->with('error', 'Không thể xóa tác giả đã gắn với sách.');
        }

        $author->delete();

        return back()->with('success', 'Đã xóa tác giả.');
    }

    /**
     * @param array<int, int|string> $authorIds
     * @param array<int, string|null> $authorNames
     * @return array<int, int>
     */
    private function resolveAuthorIds(array $authorIds, array $authorNames): array
    {
        $resolvedIds = collect($authorIds)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values();

        $names = collect($authorNames)
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique(fn ($name) => Str::lower($name))
            ->values();

        foreach ($names as $name) {
            $author = Author::query()
                ->whereRaw('LOWER(name) = ?', [Str::lower($name)])
                ->first();

            if ($author === null) {
                $author = Author::query()->create(['name' => $name]);
            }

            $resolvedIds->push($author->id);
        }

        return $resolvedIds->unique()->values()->all();
    }

    /**
     * @param Collection<int, Category> $categories
     * @return Collection<int, Category>
     */
    private function buildCategoryTree(Collection $categories): Collection
    {
        $grouped = $categories->groupBy(function (Category $category): string {
            return $category->parent_id === null ? '__root__' : (string) $category->parent_id;
        });

        $walk = function (string $parentKey, int $depth) use (&$walk, $grouped): Collection {
            $rows = collect();

            foreach (($grouped->get($parentKey) ?? collect())->sortBy('name') as $category) {
                $category->depth = $depth;
                $category->has_children = ($grouped->get((string) $category->id) ?? collect())->isNotEmpty();
                $rows->push($category);
                $rows = $rows->merge($walk((string) $category->id, $depth + 1));
            }

            return $rows;
        };

        return $walk('__root__', 0);
    }

    public function orders(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('order_code', 'like', "%{$q}%")
                        ->orWhere('recipient_name', 'like', "%{$q}%")
                        ->orWhere('recipient_phone', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.orders', [
            'orders' => $orders,
            'q' => $q,
        ]);
    }

    public function updateOrder(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'order_status' => ['required', Rule::in(['pending', 'confirmed', 'shipping', 'completed', 'cancelled'])],
            'payment_status' => ['required', Rule::in(['unpaid', 'paid', 'refunded'])],
        ]);

        $order->update($data);

        return back()->with('success', 'Đã cập nhật đơn hàng.');
    }

    public function revenue(Request $request): View
    {
        $year = (int) $request->query('year', now()->year);
        $month = $request->query('month');

        $baseQuery = Order::query()
            ->whereIn('order_status', ['confirmed', 'shipping', 'completed'])
            ->whereYear('created_at', $year);

        if ($month !== null && $month !== '') {
            $baseQuery->whereMonth('created_at', (int) $month);
        }

        $totalRevenue = (float) (clone $baseQuery)->sum('total_amount');
        $orderCount = (int) (clone $baseQuery)->count();
        $averageOrderValue = $orderCount > 0 ? $totalRevenue / $orderCount : 0;

        $revenueByPaymentMethod = (clone $baseQuery)
            ->select('payment_method', DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('payment_method')
            ->orderByDesc('revenue')
            ->get();

        $revenueByMonth = Order::query()
            ->whereIn('order_status', ['confirmed', 'shipping', 'completed'])
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        return view('admin.revenue', [
            'year' => $year,
            'month' => $month,
            'totalRevenue' => $totalRevenue,
            'orderCount' => $orderCount,
            'averageOrderValue' => $averageOrderValue,
            'revenueByPaymentMethod' => $revenueByPaymentMethod,
            'revenueByMonth' => $revenueByMonth,
        ]);
    }
}
