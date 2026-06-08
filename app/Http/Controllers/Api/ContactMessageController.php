<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends Controller
{
    /**
     * Store a new contact message (public endpoint)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $contactMessage = ContactMessage::create($request->all());

        return response()->json([
            'message' => 'Pesan berhasil dikirim. Kami akan segera menghubungi Anda!',
            'data' => $contactMessage
        ], 201);
    }

    /**
     * Get all messages (admin only)
     */
    public function index(Request $request)
    {
        $query = ContactMessage::with('repliedBy:id,name')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(20);

        return response()->json($messages);
    }

    /**
     * Get a single message (admin only)
     */
    public function show($id)
    {
        $message = ContactMessage::with('repliedBy:id,name')->findOrFail($id);
        
        // Mark as read if unread
        if ($message->status === 'unread') {
            $message->markAsRead();
        }

        return response()->json(['message' => $message]);
    }

    /**
     * Reply to a message (admin only)
     */
    public function reply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reply' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $message = ContactMessage::findOrFail($id);
        
        $message->markAsReplied(auth()->id(), $request->reply);

        // Send email notification to user
        try {
            $admin = auth()->user();
            \Illuminate\Support\Facades\Mail::to($message->email)
                ->send(new \App\Mail\ContactReplyMail($message->fresh(), $admin->name));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send reply email: ' . $e->getMessage());
            // Continue even if email fails - reply is still saved
        }

        return response()->json([
            'message' => 'Balasan berhasil dikirim ke email ' . $message->email,
            'data' => $message->fresh('repliedBy')
        ]);
    }

    /**
     * Delete a message (admin only)
     */
    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return response()->json(['message' => 'Pesan berhasil dihapus']);
    }

    /**
     * Get message statistics (admin only)
     */
    public function stats()
    {
        return response()->json([
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::unread()->count(),
            'read' => ContactMessage::read()->count(),
            'replied' => ContactMessage::replied()->count(),
        ]);
    }
}
