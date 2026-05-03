<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
}
