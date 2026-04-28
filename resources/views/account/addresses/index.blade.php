<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý địa chỉ</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style></style>
</head>
<body class="cb-site text-slate-800">
    <x-navbar />

    <main class="cb-page">
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-black">Quản lý địa chỉ</h1>
                <a href="{{ route('account.addresses.create') }}" class="cb-btn-solid">Thêm địa chỉ mới</a>
            </div>

            @if(session('success'))
                <div class="mb-4 rounded p-3 bg-emerald-50 border border-emerald-100 text-emerald-800">{{ session('success') }}</div>
            @endif

            @forelse($addresses as $address)
                <div class="mb-4 rounded-xl border border-slate-200 p-4 bg-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-semibold text-slate-900">{{ $address->receiver_name }} @if($address->is_default)<span class="text-xs text-emerald-600">(Mặc định)</span>@endif</div>
                            <div class="text-sm text-slate-600">{{ $address->receiver_phone }}</div>
                            <div class="mt-2 text-sm text-slate-700">{{ $address->address_line }}</div>
                            <div class="text-xs text-slate-500">{{ collect([$address->ward, $address->district, $address->province])->filter()->implode(', ') }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            @unless($address->is_default)
                                <form method="POST" action="{{ route('account.addresses.set_default', $address) }}">
                                    @csrf
                                    <button class="cb-btn-ghost" type="submit">Đặt mặc định</button>
                                </form>
                            @endunless
                            <a href="{{ route('account.addresses.edit', $address) }}" class="cb-btn-ghost">Sửa</a>
                            <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" onsubmit="return confirm('Xác nhận xóa địa chỉ này?')">
                                @csrf
                                @method('DELETE')
                                <button class="cb-btn-ghost text-rose-600" type="submit">Xóa</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded p-4 bg-yellow-50 border border-yellow-100 text-yellow-800">Chưa có địa chỉ nào. Hãy thêm địa chỉ để dùng khi thanh toán.</div>
            @endforelse
        </div>
    </main>
</body>
</html>
