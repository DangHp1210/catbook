<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    public function updateAvatar(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'avatar_file' => ['required', 'image', 'max:3072'],
        ]);

        $user = $request->user();
        $avatarPath = $user->avatar_url;

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

            $avatarPath = $request->file('avatar_file')->store('user-avatars', 'public');
        }

        $user->forceFill([
            'avatar_url' => $avatarPath,
        ])->save();

        return back()->with('success', 'Đã cập nhật ảnh đại diện.');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $request->user()->update($data);

        return back()->with('success', 'Đã cập nhật thông tin cá nhân.');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'new_password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $request->user()->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return back()->with('success', 'Đã cập nhật mật khẩu.');
    }
}
