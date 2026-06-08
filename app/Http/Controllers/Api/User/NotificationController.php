<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/notifications",
     *     tags={"User - Notifications"},
     *     summary="Daftar notifikasi pengguna",
     *     description="Mengembalikan semua notifikasi milik pengguna yang sedang login, terurut dari terbaru. Dipaginasi 20 per halaman.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Nomor halaman",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar notifikasi berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Booking Dikonfirmasi! 🎉"),
     *                     @OA\Property(property="body", type="string", example="Booking Anda telah disetujui oleh dokter."),
     *                     @OA\Property(property="type", type="string", enum={"booking_update","new_booking","chat_message"}, example="booking_update"),
     *                     @OA\Property(property="reference_id", type="integer", example=5),
     *                     @OA\Property(property="is_read", type="boolean", example=false),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="total", type="integer", example=10),
     *             @OA\Property(property="current_page", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid")
     * )
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return response()->json($notifications);
    }

    /**
     * @OA\Patch(
     *     path="/api/user/notifications/read-all",
     *     tags={"User - Notifications"},
     *     summary="Tandai semua notifikasi sudah dibaca",
     *     description="Mengubah status is_read semua notifikasi pengguna yang belum dibaca menjadi true.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Semua notifikasi berhasil ditandai",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Semua notifikasi ditandai sudah dibaca")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid")
     * )
     */
    public function markAllRead()
    {
        auth()->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json(['message' => 'Semua notifikasi ditandai sudah dibaca']);
    }
}
