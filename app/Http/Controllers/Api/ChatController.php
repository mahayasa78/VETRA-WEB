<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/chats",
     *     tags={"User - Chat"},
     *     summary="Daftar chat room",
     *     description="Mengembalikan semua percakapan (chat rooms) yang dimiliki pengguna. Untuk role user: daftar chat dengan dokter. Untuk role doctor: daftar chat dengan pasien. Terurut dari pesan terbaru.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar chat berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="chats", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="last_message", type="string", example="Apakah kucing saya perlu vaksin?"),
     *                     @OA\Property(property="last_timestamp", type="string", format="date-time"),
     *                     @OA\Property(property="unread_user", type="integer", example=0),
     *                     @OA\Property(property="unread_doctor", type="integer", example=2),
     *                     @OA\Property(property="doctor", type="object"),
     *                     @OA\Property(property="user", type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid")
     * )
     */
    public function index()
    {
        $userId = auth()->id();
        $userRole = auth()->user()->role;

        if ($userRole === 'doctor') {
            $chats = Chat::where('doctor_id', $userId)
                ->with(['user:id,name,profile_pic'])
                ->orderBy('last_timestamp', 'desc')
                ->get();
        } else {
            $chats = Chat::where('user_id', $userId)
                ->with(['doctor:id,name,profile_pic'])
                ->orderBy('last_timestamp', 'desc')
                ->get();
        }

        return response()->json(['chats' => $chats]);
    }

    /**
     * @OA\Post(
     *     path="/api/chats/{doctorId}",
     *     tags={"User - Chat"},
     *     summary="Mulai atau ambil chat dengan dokter",
     *     description="Membuat chat room baru dengan dokter, atau mengembalikan chat room yang sudah ada jika sudah pernah chat sebelumnya.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="doctorId",
     *         in="path",
     *         required=true,
     *         description="ID dokter yang ingin dihubungi",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chat room berhasil dibuat atau diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="chat", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="doctor_id", type="integer"),
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="doctor", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid")
     * )
     */
    public function startOrGet($doctorId)
    {
        $userId = auth()->id();

        $chat = Chat::firstOrCreate(
            ['user_id' => $userId, 'doctor_id' => $doctorId],
            ['last_timestamp' => now()]
        );

        return response()->json(['chat' => $chat->load(['user:id,name,profile_pic', 'doctor:id,name,profile_pic'])]);
    }

    /**
     * @OA\Get(
     *     path="/api/chats/{chatId}/messages",
     *     tags={"User - Chat"},
     *     summary="Daftar pesan dalam chat",
     *     description="Mengembalikan semua pesan dalam sebuah chat room, terurut dari pesan tertua. Hanya dapat diakses oleh user dan dokter yang terlibat dalam chat.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="chatId",
     *         in="path",
     *         required=true,
     *         description="ID chat room",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar pesan berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="messages", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="content", type="string", example="Halo dokter, kucing saya batuk"),
     *                     @OA\Property(property="message_type", type="string", enum={"text","image"}),
     *                     @OA\Property(property="is_read", type="boolean"),
     *                     @OA\Property(property="sender", type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Tidak punya akses ke chat ini"),
     *     @OA\Response(response=404, description="Chat room tidak ditemukan")
     * )
     */
    public function messages($chatId)
    {
        $chat = Chat::findOrFail($chatId);

        if ($chat->user_id !== auth()->id() && $chat->doctor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $chat->messages()
            ->with('sender:id,name,profile_pic')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['messages' => $messages]);
    }

    /**
     * @OA\Post(
     *     path="/api/chats/{chatId}/messages",
     *     tags={"User - Chat"},
     *     summary="Kirim pesan",
     *     description="Mengirim pesan ke chat room. Notifikasi otomatis dikirim ke penerima. Hanya user dan dokter yang terlibat yang bisa mengirim pesan.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="chatId",
     *         in="path",
     *         required=true,
     *         description="ID chat room",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="content", type="string", maxLength=5000, example="Halo dokter, apakah kucing saya perlu vaksin?"),
     *             @OA\Property(property="message_type", type="string", enum={"text","image"}, example="text")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pesan berhasil dikirim",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Pesan berhasil dikirim"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Tidak punya akses ke chat ini"),
     *     @OA\Response(response=422, description="Konten pesan wajib diisi")
     * )
     */
    public function sendMessage(Request $request, $chatId)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'message_type' => 'sometimes|in:text,image',
        ]);

        $chat = Chat::findOrFail($chatId);

        if ($chat->user_id !== auth()->id() && $chat->doctor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'chat_id' => $chatId,
            'sender_id' => auth()->id(),
            'content' => $request->content,
            'message_type' => $request->message_type ?? 'text',
            'is_read' => false,
        ]);

        $chat->update([
            'last_message' => $request->content,
            'last_sender_id' => auth()->id(),
            'last_timestamp' => now(),
            'unread_doctor' => auth()->id() === $chat->user_id ? $chat->unread_doctor + 1 : $chat->unread_doctor,
            'unread_user' => auth()->id() === $chat->doctor_id ? $chat->unread_user + 1 : $chat->unread_user,
        ]);

        $recipientId = auth()->id() === $chat->user_id ? $chat->doctor_id : $chat->user_id;
        Notification::create([
            'user_id' => $recipientId,
            'title' => 'Pesan Baru',
            'body' => substr($request->content, 0, 100),
            'type' => 'chat_message',
            'reference_id' => $chatId,
        ]);

        return response()->json([
            'message' => 'Pesan berhasil dikirim',
            'data' => $message->load('sender:id,name,profile_pic')
        ], 201);
    }

    /**
     * @OA\Patch(
     *     path="/api/chats/{chatId}/read",
     *     tags={"User - Chat"},
     *     summary="Tandai chat sudah dibaca",
     *     description="Menandai semua pesan yang belum dibaca dalam chat room sebagai sudah dibaca, dan mengatur counter unread menjadi 0.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="chatId",
     *         in="path",
     *         required=true,
     *         description="ID chat room",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pesan ditandai sudah dibaca",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Pesan ditandai sudah dibaca")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Tidak punya akses ke chat ini")
     * )
     */
    public function markRead($chatId)
    {
        $chat = Chat::findOrFail($chatId);

        if ($chat->user_id !== auth()->id() && $chat->doctor_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (auth()->id() === $chat->user_id) {
            $chat->update(['unread_user' => 0]);
        } else {
            $chat->update(['unread_doctor' => 0]);
        }

        Message::where('chat_id', $chatId)
            ->where('sender_id', '!=', auth()->id())
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Pesan ditandai sudah dibaca']);
    }
}
