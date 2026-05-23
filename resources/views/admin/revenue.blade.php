@extends('layouts.admin', ['title' => 'Thống kê doanh thu'])

@section('content')

<style>
/* ─── Design tokens ───────────────────────────────────── */
:root {
    --cb-bg:           #f8f6f1;
    --cb-border:       #e8e3d8;
    --cb-text:         #1a1a1a;
    --cb-muted:        #777;
    --cb-white:        #ffffff;
    --cb-accent:       #2d6a4f;
    --cb-accent-dark:  #1b4332;
    --cb-accent-light: #d8f3dc;
    --cb-serif:        'Playfair Display', Georgia, serif;
    --cb-sans:         'DM Sans', system-ui, sans-serif;
}
html, body {
    background: var(--cb-bg);
    margin: 0;
}

/* ─── Page header ─────────────────────────────────────── */
.rv-header {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; padding: 20px 26px;
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 20px; flex-wrap: wrap; margin-bottom: 16px;
    position: relative; overflow: hidden;
}
.rv-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #10b981, #0ea5e9);
}
.rv-header-title {
    font-family: var(--cb-serif); font-size: 22px; font-weight: 900;
    color: #0d1b10; letter-spacing: -.5px; margin: 0 0 3px;
}
.rv-header-sub { font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted); }

/* Filter form */
.rv-filter {
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
}
.rv-input, .rv-select {
    font-family: var(--cb-sans); font-size: 13px;
    padding: 9px 14px; border: 1.5px solid var(--cb-border);
    border-radius: 9px; background: var(--cb-white); color: var(--cb-text);
    outline: none; transition: border-color .2s; appearance: none;
}
.rv-input { width: 96px; }
.rv-select {
    width: 140px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' viewBox='0 0 24 24'%3E%3Cpolyline points='6 9 12 15 18 9' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 10px center; padding-right: 30px;
}
.rv-input:focus, .rv-select:focus { border-color: var(--cb-accent); }
.rv-filter-btn {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 9px 20px; border-radius: 9px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px; transition: background .2s;
}
.rv-filter-btn:hover { background: var(--cb-accent); }

/* ─── Stats strip ─────────────────────────────────────── */
.rv-stats {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 16px;
}
@media (max-width: 900px) { .rv-stats { grid-template-columns: repeat(2, 1fr); } }

.rv-stat {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 16px; padding: 18px 20px; position: relative; overflow: hidden;
}
.rv-stat::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
}
.rv-stat-revenue::before  { background: #10b981; }
.rv-stat-profit::before   { background: #0ea5e9; }
.rv-stat-orders::before   { background: #f59e0b; }
.rv-stat-avg::before      { background: #8b5cf6; }

.rv-stat-lbl {
    font-family: var(--cb-sans); font-size: 11px; font-weight: 600;
    letter-spacing: 1px; text-transform: uppercase; color: #b0a898; margin-bottom: 8px;
    display: flex; align-items: center; gap: 6px;
}
.rv-stat-val {
    font-family: var(--cb-serif); font-size: 24px; font-weight: 900;
    line-height: 1; letter-spacing: -.3px;
}
.rv-stat-revenue .rv-stat-val  { color: #059669; }
.rv-stat-profit  .rv-stat-val  { color: #0284c7; }
.rv-stat-orders  .rv-stat-val  { color: var(--cb-text); }
.rv-stat-avg     .rv-stat-val  { color: #7c3aed; }

/* ─── Chart + table grid ──────────────────────────────── */
.rv-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;
}
@media (max-width: 900px) { .rv-grid { grid-template-columns: 1fr; } }

.rv-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden;
}
.rv-card-head {
    padding: 16px 22px; border-bottom: 1px solid var(--cb-border);
    display: flex; align-items: center; justify-content: space-between;
}
.rv-card-title {
    font-family: var(--cb-sans); font-size: 14px; font-weight: 600; color: var(--cb-text);
}
.rv-card-body { padding: 16px 22px; }

/* ─── Bar chart ───────────────────────────────────────── */
.rv-chart-wrap { position: relative; height: 200px; margin-bottom: 8px; }
.rv-chart-canvas { width: 100% !important; height: 100% !important; }

/* ─── Table ───────────────────────────────────────────── */
.rv-table { width: 100%; border-collapse: collapse; font-family: var(--cb-sans); }
.rv-table thead tr { border-bottom: 1px solid var(--cb-border); }
.rv-table th {
    padding: 9px 0; font-size: 11px; font-weight: 700;
    letter-spacing: 1.1px; text-transform: uppercase; color: #b0a898; text-align: left;
}
.rv-table th:last-child { text-align: right; }
.rv-table tbody tr { border-bottom: 1px solid var(--cb-border); transition: background .15s; }
.rv-table tbody tr:last-child { border-bottom: none; }
.rv-table tbody tr:hover { background: #fdfcfa; margin: 0 -22px; }
.rv-table td { padding: 11px 0; font-size: 13px; color: var(--cb-text); vertical-align: middle; }
.rv-table td:last-child { text-align: right; font-weight: 600; }

/* Method badge */
.rv-method {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 999px;
    background: var(--cb-bg); border: 1px solid var(--cb-border); color: var(--cb-muted);
}

/* Month label */
.rv-month-lbl {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; border-radius: 8px;
    background: var(--cb-bg); border: 1px solid var(--cb-border);
    font-size: 12px; font-weight: 700; color: var(--cb-muted);
}

/* Revenue bar (inline in table) */
.rv-bar-wrap {
    display: flex; align-items: center; gap: 10px;
}
.rv-bar-track {
    flex: 1; height: 5px; background: var(--cb-bg);
    border-radius: 999px; overflow: hidden; min-width: 60px;
}
.rv-bar-fill {
    height: 100%; border-radius: 999px;
    background: linear-gradient(90deg, #10b981, #34d399);
    transition: width .4s ease;
}

/* Empty state */
.rv-empty {
    padding: 32px; text-align: center;
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted);
}
</style>

{{-- ── Page header + filter ─────────────────────────────── --}}
<div class="rv-header">
    <div>
        <h1 class="rv-header-title">Thống kê doanh thu</h1>
        <p class="rv-header-sub">Dựa trên đơn hàng đã xác nhận, đang giao và hoàn tất.</p>
    </div>
    <form method="GET" class="rv-filter">
        <input type="number" name="year" value="{{ $year }}"
               min="2020" max="2100" class="rv-input" placeholder="Năm">
        <select name="month" class="rv-select">
            <option value="">Cả năm</option>
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" @selected((string)$month === (string)$m)>Tháng {{ $m }}</option>
            @endfor
        </select>
        <button type="submit" class="rv-filter-btn">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="14" y2="12"/><line x1="4" y1="18" x2="18" y2="18"/>
            </svg>
            Lọc
        </button>
    </form>
</div>

{{-- ── Stats strip ──────────────────────────────────────── --}}
@php
    $paymentMethodNames = [
        'cod'           => 'COD',
        'bank_transfer' => 'Chuyển khoản',
        'momo'          => 'MoMo',
        'vnpay'         => 'VNPay',
    ];
    $maxMonthRevenue = $revenueByMonth->max('revenue') ?: 1;
@endphp

<div class="rv-stats">
    <div class="rv-stat rv-stat-revenue">
        <p class="rv-stat-lbl">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
            Tổng doanh thu
        </p>
        <p class="rv-stat-val">{{ number_format($totalRevenue, 0, ',', '.') }}đ</p>
    </div>
    <div class="rv-stat rv-stat-profit">
        <p class="rv-stat-lbl">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
            </svg>
            Tiền lãi
        </p>
        <p class="rv-stat-val">{{ number_format($totalProfit, 0, ',', '.') }}đ</p>
    </div>
    <div class="rv-stat rv-stat-orders">
        <p class="rv-stat-lbl">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
            </svg>
            Số đơn hàng
        </p>
        <p class="rv-stat-val">{{ number_format($orderCount) }}</p>
    </div>
    <div class="rv-stat rv-stat-avg">
        <p class="rv-stat-lbl">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Trung bình / đơn
        </p>
        <p class="rv-stat-val">{{ number_format($averageOrderValue, 0, ',', '.') }}đ</p>
    </div>
</div>

{{-- ── Chart + tables grid ───────────────────────────────── --}}
<div class="rv-grid">

    {{-- Monthly bar chart --}}
    <div class="rv-card">
        <div class="rv-card-head">
            <span class="rv-card-title">Doanh thu theo tháng — {{ $year }}</span>
        </div>
        <div class="rv-card-body">
            <div class="rv-chart-wrap">
                <canvas id="rv-monthly-chart" class="rv-chart-canvas"></canvas>
            </div>

            {{-- Table below chart --}}
            @if($revenueByMonth->isNotEmpty())
                <table class="rv-table" style="margin-top:8px">
                    <thead>
                        <tr>
                            <th>Tháng</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenueByMonth as $item)
                            @php $pct = $maxMonthRevenue > 0 ? round($item->revenue / $maxMonthRevenue * 100) : 0; @endphp
                            <tr>
                                <td>
                                    <span class="rv-month-lbl">{{ (int)$item->month }}</span>
                                </td>
                                <td>
                                    <div class="rv-bar-wrap">
                                        <div class="rv-bar-track">
                                            <div class="rv-bar-fill" data-width="{{ $pct }}"></div>
                                        </div>
                                        <span style="font-size:12px;white-space:nowrap;color:var(--cb-text);font-weight:600">
                                            {{ number_format($item->revenue, 0, ',', '.') }}đ
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="rv-empty">Chưa có dữ liệu tháng nào.</div>
            @endif
        </div>
    </div>

    {{-- Payment method table + donut --}}
    <div class="rv-card">
        <div class="rv-card-head">
            <span class="rv-card-title">Doanh thu theo phương thức thanh toán</span>
        </div>
        <div class="rv-card-body">
            <div class="rv-chart-wrap">
                <canvas id="rv-method-chart" class="rv-chart-canvas"></canvas>
            </div>

            @if($revenueByPaymentMethod->isNotEmpty())
                <table class="rv-table" style="margin-top:8px">
                    <thead>
                        <tr>
                            <th>Phương thức</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenueByPaymentMethod as $item)
                            <tr>
                                <td>
                                    <span class="rv-method">
                                        {{ $paymentMethodNames[$item->payment_method] ?? $item->payment_method }}
                                    </span>
                                </td>
                                <td>{{ number_format($item->revenue, 0, ',', '.') }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="rv-empty">Chưa có dữ liệu.</div>
            @endif
        </div>
    </div>

</div>

{{-- ── Chart.js ─────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script type="application/json" id="revenue-data">
{
    "monthLabels": {!! json_encode($revenueByMonth->pluck('month')->map(fn($m) => 'T'.(int)$m)->toArray()) !!},
    "monthRevenues": {!! json_encode($revenueByMonth->pluck('revenue')->map(fn($r) => (float)$r)->toArray()) !!},
    "methodLabels": {!! json_encode($revenueByPaymentMethod->map(fn($i) => $paymentMethodNames[$i->payment_method] ?? $i->payment_method)->toArray()) !!},
    "methodRevenues": {!! json_encode($revenueByPaymentMethod->pluck('revenue')->map(fn($r) => (float)$r)->toArray()) !!}
}
</script>

<script>
(function () {
    const revenueData = JSON.parse(document.getElementById('revenue-data').textContent);
    const { monthLabels, monthRevenues, methodLabels, methodRevenues } = revenueData;

    /* ── Color palette ── */
    const accent  = '#2d6a4f';
    const accentL = '#86efac';
    const isDark  = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const gridClr = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.06)';
    const lblClr  = isDark ? '#a09890' : '#b0a898';
    const textClr = isDark ? '#e0dbd0' : '#1a1a1a';

    Chart.defaults.font.family = "'DM Sans', system-ui, sans-serif";
    Chart.defaults.font.size   = 12;
    Chart.defaults.color       = lblClr;

    /* ── Monthly bar chart ── */
    const monthCtx = document.getElementById('rv-monthly-chart');
    if (monthCtx && monthRevenues.length > 0) {
        new Chart(monthCtx, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Doanh thu',
                    data: monthRevenues,
                    backgroundColor: accent + 'cc',
                    borderColor: accent,
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + Number(ctx.raw).toLocaleString('vi-VN') + 'đ'
                        }
                    }
                },
                scales: {
                    x: { grid: { color: gridClr }, ticks: { color: lblClr } },
                    y: {
                        grid: { color: gridClr }, ticks: {
                            color: lblClr,
                            callback: v => (v >= 1e6 ? (v/1e6).toFixed(0)+'M' : v >= 1e3 ? (v/1e3).toFixed(0)+'K' : v) + 'đ'
                        },
                        beginAtZero: true,
                    }
                }
            }
        });
    }

    /* ── Payment method donut ── */
    const methodCtx = document.getElementById('rv-method-chart');
    if (methodCtx && methodRevenues.length > 0) {
        const palette = ['#2d6a4f','#10b981','#0ea5e9','#8b5cf6','#f59e0b','#ef4444'];
        new Chart(methodCtx, {
            type: 'doughnut',
            data: {
                labels: methodLabels,
                datasets: [{
                    data: methodRevenues,
                    backgroundColor: palette.slice(0, methodRevenues.length),
                    borderColor: isDark ? '#1a1a1a' : '#fff',
                    borderWidth: 3,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: lblClr, padding: 14, font: { size: 12 }, boxWidth: 12 }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ' + Number(ctx.raw).toLocaleString('vi-VN') + 'đ'
                        }
                    }
                }
            }
        });
    }
})();

// Set bar widths from data attributes
document.querySelectorAll('.rv-bar-fill[data-width]').forEach(el => {
    el.style.width = el.dataset.width + '%';
});
</script>

@endsection