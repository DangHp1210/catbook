@extends('layouts.app')

@section('title','Thêm địa chỉ')

@section('content')
        <div class="max-w-2xl mx-auto p-6">
            <h1 class="text-2xl font-black mb-4">Thêm địa chỉ mới</h1>

            @if($errors->any())
                <div class="mb-4 p-3 rounded bg-rose-50 border border-rose-100 text-rose-700">
                    <ul class="list-disc pl-4">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('account.addresses.store') }}">
                @csrf
                <div class="grid gap-3">
                    <input name="receiver_name" placeholder="Tên người nhận" value="{{ old('receiver_name') }}" class="border rounded px-3 py-2" required>
                    <input name="receiver_phone" placeholder="Số điện thoại" value="{{ old('receiver_phone') }}" class="border rounded px-3 py-2" required>
                    <textarea name="address_line" placeholder="Địa chỉ cụ thể" class="border rounded px-3 py-2" required>{{ old('address_line') }}</textarea>
                    <input name="ward" placeholder="Phường/Xã" value="{{ old('ward') }}" class="border rounded px-3 py-2">
                    <input name="district" placeholder="Quận/Huyện" value="{{ old('district') }}" class="border rounded px-3 py-2">
                    <input name="province" placeholder="Tỉnh/Thành phố" value="{{ old('province') }}" class="border rounded px-3 py-2">
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="set_default"> Đặt làm địa chỉ mặc định</label>
                    <div class="flex gap-2">
                        <button class="cb-btn-solid">Lưu</button>
                        <a href="{{ route('account.addresses.index') }}" class="cb-btn-ghost">Hủy</a>
                    </div>
                </div>
            </form>
        </div>
@endsection
