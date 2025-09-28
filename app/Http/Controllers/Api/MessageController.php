<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
     // Send a message
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $msg = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
        ]);

        return response()->json(['success' => true, 'message' => $msg], 201);
    }

    // Get all messages between two users
    public function conversation($userId)
    {
        $messages = Message::where(function ($q) use ($userId) {
                $q->where('sender_id', Auth::id())
                  ->where('receiver_id', $userId);
            })
            ->orWhere(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // Get inbox (last messages from all conversations)
    public function inbox()
    {
        $messages = Message::where('receiver_id', Auth::id())
            ->orWhere('sender_id', Auth::id())
            ->latest()
            ->get();

        return response()->json($messages);
    }

    // Mark as read
    public function markAsRead($id)
    {
        $msg = Message::where('id', $id)
            ->where('receiver_id', Auth::id())
            ->firstOrFail();

        $msg->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
