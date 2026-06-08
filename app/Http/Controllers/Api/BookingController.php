<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/bookings",
     *     tags={"User - Bookings"},
     *     summary="Daftar booking milik user",
     *     description="Mengembalikan semua booking yang dibuat oleh pengguna yang sedang login, terurut dari terbaru. Hasil dipaginasi 10 per halaman.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Nomor halaman untuk paginasi",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar booking berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="status", type="string", enum={"pending","confirmed","rejected","done"}, example="pending"),
     *                     @OA\Property(property="scheduled_at", type="string", format="date-time", example="2025-07-01T09:00:00"),
     *                     @OA\Property(property="complaint", type="string", example="Kucing saya demam"),
     *                     @OA\Property(property="doctor", type="object"),
     *                     @OA\Property(property="clinic", type="object"),
     *                     @OA\Property(property="pet", type="object")
     *                 )
     *             ),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="total", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid")
     * )
     */
    public function index(Request $request)
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['doctor:id,name,profile_pic', 'clinic:id,name', 'pet:id,name'])
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10);
        return response()->json($bookings);
    }

    /**
     * @OA\Post(
     *     path="/api/bookings",
     *     tags={"User - Bookings"},
     *     summary="Buat booking baru",
     *     description="Membuat booking konsultasi baru ke klinik/dokter. Notifikasi otomatis dikirim ke dokter (jika dipilih) atau klinik.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"clinic_id","scheduled_at"},
     *             @OA\Property(property="clinic_id", type="integer", description="ID klinik yang dituju", example=5),
     *             @OA\Property(property="doctor_id", type="integer", nullable=true, description="ID dokter (opsional)", example=2),
     *             @OA\Property(property="pet_id", type="integer", nullable=true, description="ID hewan peliharaan (opsional)", example=1),
     *             @OA\Property(property="scheduled_at", type="string", format="date-time", description="Tanggal & waktu booking (tidak boleh masa lalu)", example="2025-07-01T09:00:00"),
     *             @OA\Property(property="notes", type="string", nullable=true, description="Keluhan/catatan", example="Kucing saya demam dan tidak mau makan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Booking berhasil dibuat",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Booking berhasil dibuat. Menunggu konfirmasi."),
     *             @OA\Property(property="booking", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validasi gagal - clinic_id tidak valid atau tanggal tidak valid")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'clinic_id' => 'required|exists:users,id',
            'doctor_id' => 'nullable|exists:users,id',
            'pet_id' => 'nullable|exists:pets,id',
            'scheduled_at' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $scheduledAt = \Carbon\Carbon::parse($request->scheduled_at);
        
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'clinic_id' => $request->clinic_id,
            'doctor_id' => $request->doctor_id,
            'pet_id' => $request->pet_id,
            'complaint' => $request->notes,
            'booking_date' => $scheduledAt->toDateString(),
            'booking_time' => $scheduledAt->toTimeString(),
            'scheduled_at' => $scheduledAt,
            'status' => 'pending',
        ]);
        
        Notification::create([
            'user_id' => auth()->id(),
            'title' => 'Booking Berhasil Dibuat',
            'body' => 'Booking Anda sedang menunggu konfirmasi.',
            'type' => 'booking_update',
            'reference_id' => $booking->id,
        ]);
        
        if ($request->doctor_id) {
            Notification::create([
                'user_id' => $request->doctor_id,
                'title' => 'Booking Baru',
                'body' => 'Anda mendapat booking baru. Segera konfirmasi!',
                'type' => 'new_booking',
                'reference_id' => $booking->id,
            ]);
        }
        
        if (!$request->doctor_id) {
            Notification::create([
                'user_id' => $request->clinic_id,
                'title' => 'Booking Baru',
                'body' => 'Ada booking baru yang perlu ditentukan dokternya.',
                'type' => 'new_booking',
                'reference_id' => $booking->id,
            ]);
        }
        
        return response()->json([
            'message' => 'Booking berhasil dibuat. Menunggu konfirmasi.',
            'booking' => $booking->load(['doctor:id,name', 'clinic:id,name', 'pet:id,name']),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/bookings/{id}",
     *     tags={"User - Bookings"},
     *     summary="Detail booking",
     *     description="Mengembalikan detail lengkap sebuah booking milik pengguna yang login.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID booking",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail booking berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="booking", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="complaint", type="string"),
     *                 @OA\Property(property="doctor_notes", type="string", nullable=true),
     *                 @OA\Property(property="scheduled_at", type="string", format="date-time"),
     *                 @OA\Property(property="doctor", type="object"),
     *                 @OA\Property(property="clinic", type="object"),
     *                 @OA\Property(property="pet", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Booking tidak ditemukan atau bukan milik user")
     * )
     */
    public function show($id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', auth()->id())
            ->with(['doctor:id,name,profile_pic', 'clinic:id,name', 'pet'])
            ->firstOrFail();
        return response()->json(['booking' => $booking]);
    }

    /**
     * @OA\Delete(
     *     path="/api/bookings/{id}",
     *     tags={"User - Bookings"},
     *     summary="Batalkan booking",
     *     description="Membatalkan booking yang masih berstatus 'pending'. Booking yang sudah dikonfirmasi tidak dapat dibatalkan melalui endpoint ini.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID booking",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Booking berhasil dibatalkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Booking berhasil dibatalkan")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Booking tidak ditemukan, bukan milik user, atau sudah tidak berstatus pending")
     * )
     */
    public function cancel($id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();
        
        $booking->update(['status' => 'rejected']);
        
        return response()->json(['message' => 'Booking berhasil dibatalkan']);
    }
}
