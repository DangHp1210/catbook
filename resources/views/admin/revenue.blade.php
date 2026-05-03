@extends('layouts.admin', ['title' => 'Thống kê doanh thu'])

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Thống kê doanh thu</h1>
                <p class="text-sm text-slate-500">Dựa trên các đơn ở trạng thái confirmed, shipping và completed.</p>
            </div>

            <form method="GET" class="flex flex-wrap gap-2">
                <input type="number" name="year" value="{{ $year }}" min="2020" max="2100" class="w-28 rounded-xl border border-slate-300 px-3 py-2 text-sm" />
                <select name="month" class="w-36 rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Cả năm</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected((string) $month === (string) $m)>Tháng {{ $m }}</option>
                    @endfor
                </select>
                <button class="rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-white">Lọc</button>
            </form>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Tổng doanh thu</p>
            <p class="mt-2 text-2xl font-bold text-emerald-600">{{ number_format($totalRevenue, 0, ',', '.') }} đ</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Số đơn hàng</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($orderCount) }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Giá trị trung bình / đơn</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($averageOrderValue, 0, ',', '.') }} đ</p>
        </article>
    </div>

    <div class="grid gap-5 xl:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Doanh thu theo phương thức thanh toán</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-3 py-2">Phương thức</th>
                            <th class="px-3 py-2">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($revenueByPaymentMethod as $item)
                            <tr class="border-b border-slate-100">
                                <td class="px-3 py-2 text-slate-700">{{ $item->payment_method }}</td>
                                <td class="px-3 py-2 font-semibold text-slate-900">{{ number_format($item->revenue, 0, ',', '.') }} đ</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-3 py-4 text-center text-slate-500">Chưa có dữ liệu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Doanh thu theo tháng ({{ $year }})</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-3 py-2">Tháng</th>
                            <th class="px-3 py-2">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($revenueByMonth as $item)
                            <tr class="border-b border-slate-100">
                                <td class="px-3 py-2 text-slate-700">{{ (int) $item->month }}</td>
                                <td class="px-3 py-2 font-semibold text-slate-900">{{ number_format($item->revenue, 0, ',', '.') }} đ</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-3 py-4 text-center text-slate-500">Chưa có dữ liệu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
