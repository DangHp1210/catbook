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

        return response()->json([
            'ok' => true,
            ...$this->chatBotService->history($request->user(), $validated['session_token'] ?? null),
        ]);
    }

    public function message(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_token' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        return response()->json([
            'ok' => true,
            ...$this->chatBotService->sendMessage(
                $request->user(),
                $validated['session_token'] ?? null,
                $validated['message'],
            ),
        ]);
    }
}
