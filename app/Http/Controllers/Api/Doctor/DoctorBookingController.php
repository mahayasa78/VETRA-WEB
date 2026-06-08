<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;

class DoctorBookingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/doctor/bookings",
     *     tags={"Doctor Panel"},
     *     summary="Daftar booking dokter",
     *     description="Mengembalikan semua booking yang ditujukan kepada dokter yang sedang login. Dapat difilter berdasarkan status.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter berdasarkan status booking",
     *         @OA\Schema(type="string", enum={"pending","confirmed","rejected","done"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar booking berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                     @OA\Property(property="scheduled_at", type="string", format="date-time"),
     *                     @OA\Property(property="complaint", type="string"),
     *                     @OA\Property(property="user", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="phone", type="string")
     *                     ),
     *                     @OA\Property(property="pet", type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid"),
     *     @OA\Response(response=403, description="Hanya dokter yang dapat mengakses")
     * )
     */
    public function index(Request $request)
    {
        $doctorId = auth()->id();
        $status = $request->query('status');
        
        $query = Booking::where('doctor_id', $doctorId)
            ->with(['user:id,name,profile_pic,phone', 'clinic:id,name', 'pet:id,name,species'])
            ->orderBy('scheduled_at');
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return response()->json($query->paginate(15));
    }

    /**
     * @OA\Patch(
     *     path="/api/doctor/bookings/{id}/status",
     *     tags={"Doctor Panel"},
     *     summary="Update status booking",
     *     description="Dokter mengubah status booking pasien (confirm, reject, atau mark as done). Notifikasi otomatis dikirim ke pasien.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID booking",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"confirmed","rejected","done"}, example="confirmed"),
     *             @OA\Property(property="doctor_notes", type="string", nullable=true, example="Berikan obat 2x sehari")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status booking berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Status booking diperbarui"),
     *             @OA\Property(property="booking", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Booking tidak ditemukan atau bukan milik dokter ini"),
     *     @OA\Response(response=422, description="Status tidak valid")
     * )
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,rejected,done',
            'doctor_notes' => 'nullable|string|max:1000',
        ]);
        
        $booking = Booking::where('id', $id)
            ->where('doctor_id', auth()->id())
            ->firstOrFail();
        
        $booking->update([
            'status' => $request->status,
            'doctor_notes' => $request->doctor_notes,
        ]);
        
        $statusMessages = [
            'confirmed' => ['Booking Dikonfirmasi! 🎉', 'Booking Anda telah disetujui oleh dokter.'],
            'rejected' => ['Booking Ditolak ⚠️', 'Booking Anda tidak dapat diproses.'],
            'done' => ['Konsultasi Selesai 🏥', 'Konsultasi selesai. Mohon berikan ulasan!'],
        ];
        
        [$title, $body] = $statusMessages[$request->status];
        
        Notification::create([
            'user_id' => $booking->user_id,
            'title' => $title,
            'body' => $body,
            'type' => 'booking_update',
            'reference_id' => $booking->id,
        ]);
        
        return response()->json(['message' => 'Status booking diperbarui', 'booking' => $booking]);
    }

    /**
     * @OA\Get(
     *     path="/api/doctor/dashboard",
     *     tags={"Doctor Panel"},
     *     summary="Dashboard statistik dokter",
     *     description="Mengembalikan statistik booking dokter (total confirmed, pending, bulan ini, done) beserta 5 booking mendatang.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Data dashboard berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="stats", type="object",
     *                 @OA\Property(property="total_confirmed", type="integer", example=15),
     *                 @OA\Property(property="total_pending", type="integer", example=3),
     *                 @OA\Property(property="total_this_month", type="integer", example=8),
     *                 @OA\Property(property="total_done", type="integer", example=42)
     *             ),
     *             @OA\Property(property="upcoming_bookings", type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Hanya dokter yang dapat mengakses")
     * )
     */
    public function dashboard()
    {
        $doctorId = auth()->id();
        
        $allBookings = Booking::where('doctor_id', $doctorId);
        
        $stats = [
            'total_confirmed' => (clone $allBookings)->where('status', 'confirmed')->count(),
            'total_pending' => (clone $allBookings)->where('status', 'pending')->count(),
            'total_this_month' => (clone $allBookings)->whereMonth('booking_date', now()->month)->count(),
            'total_done' => (clone $allBookings)->where('status', 'done')->count(),
        ];
        
        $upcoming = Booking::where('doctor_id', $doctorId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['user:id,name,profile_pic', 'pet:id,name,species'])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();
        
        return response()->json(['stats' => $stats, 'upcoming_bookings' => $upcoming]);
    }
}
