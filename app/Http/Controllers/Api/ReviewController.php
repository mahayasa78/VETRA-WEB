<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/doctors/{doctorId}/reviews",
     *     tags={"Public - Reviews"},
     *     summary="Ulasan untuk dokter",
     *     description="Mengembalikan semua ulasan yang diberikan untuk seorang dokter beserta rata-rata rating.",
     *     @OA\Parameter(
     *         name="doctorId",
     *         in="path",
     *         required=true,
     *         description="ID dokter",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ulasan dokter berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="average_rating", type="number", format="float", example=4.5),
     *             @OA\Property(property="total_reviews", type="integer", example=12),
     *             @OA\Property(property="reviews", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5),
     *                     @OA\Property(property="comment", type="string", example="Dokter sangat baik dan sabar"),
     *                     @OA\Property(property="user", type="object")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function forDoctor($doctorId)
    {
        $reviews = Review::where('target_id', $doctorId)
            ->where('target_type', 'doctor')
            ->with('user:id,name,profile_pic')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $avgRating = $reviews->avg('rating') ?? 0;
        
        return response()->json([
            'average_rating' => round($avgRating, 1),
            'total_reviews' => $reviews->count(),
            'reviews' => $reviews,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/clinics/{clinicId}/reviews",
     *     tags={"Public - Reviews"},
     *     summary="Ulasan untuk klinik",
     *     description="Mengembalikan semua ulasan yang diberikan untuk sebuah klinik beserta rata-rata rating.",
     *     @OA\Parameter(
     *         name="clinicId",
     *         in="path",
     *         required=true,
     *         description="ID klinik",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ulasan klinik berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="average_rating", type="number", format="float", example=4.2),
     *             @OA\Property(property="total_reviews", type="integer", example=25),
     *             @OA\Property(property="reviews", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function forClinic($clinicId)
    {
        $reviews = Review::where('target_id', $clinicId)
            ->where('target_type', 'clinic')
            ->with('user:id,name,profile_pic')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $avgRating = $reviews->avg('rating') ?? 0;
        
        return response()->json([
            'average_rating' => round($avgRating, 1),
            'total_reviews' => $reviews->count(),
            'reviews' => $reviews,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/reviews",
     *     tags={"User - Bookings"},
     *     summary="Buat atau update ulasan",
     *     description="User memberikan ulasan untuk dokter atau klinik. Jika sudah pernah memberi ulasan untuk target yang sama, ulasan lama akan diperbarui (upsert).",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"target_id","target_type","rating"},
     *             @OA\Property(property="target_id", type="integer", description="ID dokter atau klinik", example=2),
     *             @OA\Property(property="target_type", type="string", enum={"doctor","clinic"}, example="doctor"),
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5),
     *             @OA\Property(property="comment", type="string", nullable=true, maxLength=1000, example="Dokter sangat sabar dan profesional"),
     *             @OA\Property(property="image_url", type="string", format="url", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ulasan berhasil disimpan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ulasan berhasil disimpan"),
     *             @OA\Property(property="review", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validasi gagal - rating harus antara 1-5"),
     *     @OA\Response(response=401, description="Token tidak valid")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'target_id' => 'required|exists:users,id',
            'target_type' => 'required|in:doctor,clinic',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
            'image_url' => 'nullable|url',
        ]);
        
        $review = Review::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'target_id' => $request->target_id,
                'target_type' => $request->target_type,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
                'image_url' => $request->image_url,
            ]
        );
        
        return response()->json(['message' => 'Ulasan berhasil disimpan', 'review' => $review], 201);
    }
}
