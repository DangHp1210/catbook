<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class UserAddressController extends Controller
{
    public function index(Request $request): View
    {
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->latest('id')->get();

        return view('account.addresses.index', [
            'addresses' => $addresses,
        ]);
    }

    public function create(): View
    {
        return view('account.addresses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'string', 'max:20'],
            'address_line' => ['required', 'string', 'max:500'],
            'ward' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'set_default' => ['nullable', 'in:on,1'],
        ]);

        $user = $request->user();

        $isDefault = ($user->addresses()->count() === 0) || ($request->filled('set_default'));

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create([
            'receiver_name' => $validated['receiver_name'],
            'receiver_phone' => $validated['receiver_phone'],
            'address_line' => $validated['address_line'],
            'ward' => $validated['ward'] ?? null,
            'district' => $validated['district'] ?? null,
            'province' => $validated['province'] ?? null,
            'is_default' => $isDefault,
        ]);

        return redirect()->route('account.addresses.index')->with('success', 'Đã thêm địa chỉ.');
    }

    public function edit(Request $request, UserAddress $address): View
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        return view('account.addresses.edit', [
            'address' => $address,
        ]);
    }

    public function update(Request $request, UserAddress $address): RedirectResponse
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }
        $validated = $request->validate([
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'string', 'max:20'],
            'address_line' => ['required', 'string', 'max:500'],
            'ward' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'set_default' => ['nullable', 'in:on,1'],
        ]);

        if ($request->filled('set_default')) {
            $request->user()->addresses()->update(['is_default' => false]);
            $address->is_default = true;
        }

        $address->update([
            'receiver_name' => $validated['receiver_name'],
            'receiver_phone' => $validated['receiver_phone'],
            'address_line' => $validated['address_line'],
            'ward' => $validated['ward'] ?? null,
            'district' => $validated['district'] ?? null,
            'province' => $validated['province'] ?? null,
        ]);

        $address->save();

        return redirect()->route('account.addresses.index')->with('success', 'Cập nhật địa chỉ thành công.');
    }

    public function destroy(Request $request, UserAddress $address): RedirectResponse
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = $request->user()->addresses()->latest('id')->first();
            if ($next) {
                $next->is_default = true;
                $next->save();
            }
        }

        return redirect()->route('account.addresses.index')->with('success', 'Đã xóa địa chỉ.');
    }

    public function setDefault(Request $request, UserAddress $address): RedirectResponse
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->user()->addresses()->update(['is_default' => false]);
        $address->is_default = true;
        $address->save();

        return redirect()->route('account.addresses.index')->with('success', 'Đã đặt địa chỉ mặc định.');
    }
}
