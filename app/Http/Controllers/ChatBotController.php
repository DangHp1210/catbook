<?php

namespace App\Http\Controllers;

use App\Services\ChatBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    public function __construct(private readonly ChatBotService $chatBotService)
    {
    }

    public function session(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_token' => ['nullable', 'string', 'max:100'],
        ]);
        try {
            return response()->json([
                'ok' => true,
                ...$this->chatBotService->history($request->user(), $validated['session_token'] ?? null),
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['ok' => false, 'message' => 'Lỗi máy chủ khi tải lịch sử chat.'], 500);
        }
    }

    public function message(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_token' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:2000'],
        ]);
        try {
            return response()->json([
                'ok' => true,
                ...$this->chatBotService->sendMessage(
                    $request->user(),
                    $validated['session_token'] ?? null,
                    $validated['message'],
                ),
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['ok' => false, 'message' => 'Lỗi máy chủ khi xử lý tin nhắn.'], 500);
        }
    }
}
