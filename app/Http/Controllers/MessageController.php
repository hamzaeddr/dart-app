<?php

namespace App\Http\Controllers;

use App\Models\Daret;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request, Daret $daret): JsonResponse
    {
        $user = $request->user();

        if (! $this->userCanAccessDaret($user->id, $daret)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $messages = $daret->messages()
            ->with('user:id,name')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request, Daret $daret): JsonResponse
    {
        $user = $request->user();

        if (! $this->userCanAccessDaret($user->id, $daret)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $message = $daret->messages()->create([
            'user_id' => $user->id,
            'body' => $data['body'],
        ]);

        $message->load('user:id,name');

        return response()->json($message, 201);
    }

    protected function userCanAccessDaret(int $userId, Daret $daret): bool
    {
        if ($daret->owner_id === $userId) {
            return true;
        }

        return $daret->members()->where('user_id', $userId)->exists();
    }
}
