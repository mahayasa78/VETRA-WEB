<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/doctors",
     *     tags={"Public - Doctors"},
     *     summary="Daftar semua dokter hewan",
     *     description="Mengembalikan daftar dokter hewan aktif. Dapat difilter berdasarkan spesialisasi dan status online. Dipaginasi 15 per halaman.",
     *     @OA\Parameter(
     *         name="spesialis",
     *         in="query",
     *         description="Filter berdasarkan spesialisasi (pencarian parsial)",
     *         @OA\Schema(type="string", example="Bedah")
     *     ),
     *     @OA\Parameter(
     *         name="is_online",
     *         in="query",
     *         description="Filter dokter yang sedang online (1=online, 0=offline)",
     *         @OA\Schema(type="integer", enum={0,1}, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Nomor halaman",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar dokter berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="drh. Siti Rahayu"),
     *                     @OA\Property(property="profile_pic", type="string", nullable=true),
     *                     @OA\Property(property="doctor_profile", type="object",
     *                         @OA\Property(property="spesialis", type="string", example="Bedah Hewan"),
     *                         @OA\Property(property="experience_years", type="integer", example=5),
     *                         @OA\Property(property="is_online", type="boolean", example=true)
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="total", type="integer", example=20),
     *             @OA\Property(property="current_page", type="integer", example=1)
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'doctor')
            ->where('is_active', true)
            ->with(['doctorProfile']);

        if ($request->has('spesialis')) {
            $query->whereHas('doctorProfile', function($q) use ($request) {
                $q->where('spesialis', 'like', '%' . $request->spesialis . '%');
            });
        }

        if ($request->has('is_online')) {
            $query->whereHas('doctorProfile', function($q) use ($request) {
                $q->where('is_online', $request->is_online);
            });
        }

        $doctors = $query->paginate(15);

        return response()->json($doctors);
    }

    /**
     * @OA\Get(
     *     path="/api/doctors/{id}",
     *     tags={"Public - Doctors"},
     *     summary="Detail dokter hewan",
     *     description="Mengembalikan detail lengkap seorang dokter hewan beserta profil dan informasi klinik tempatnya bekerja.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dokter (user ID dengan role doctor)",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail dokter berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="doctor", type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="drh. Siti Rahayu"),
     *                 @OA\Property(property="doctor_profile", type="object",
     *                     @OA\Property(property="spesialis", type="string", example="Bedah Hewan"),
     *                     @OA\Property(property="bio", type="string"),
     *                     @OA\Property(property="experience_years", type="integer"),
     *                     @OA\Property(property="license_number", type="string"),
     *                     @OA\Property(property="clinic", type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Dokter tidak ditemukan")
     * )
     */
    public function show($id)
    {
        $doctor = User::where('id', $id)
            ->where('role', 'doctor')
            ->with(['doctorProfile', 'doctorProfile.clinic'])
            ->firstOrFail();

        return response()->json(['doctor' => $doctor]);
    }

    /**
     * @OA\Put(
     *     path="/api/doctor/profile",
     *     tags={"Doctor Panel"},
     *     summary="Update profil dokter",
     *     description="Dokter memperbarui data profilnya sendiri (bio, spesialisasi, pengalaman).",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="bio", type="string", example="Saya dokter hewan berpengalaman..."),
     *             @OA\Property(property="spesialis", type="string", example="Bedah Hewan"),
     *             @OA\Property(property="experience_years", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profil dokter berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profil dokter berhasil diperbarui"),
     *             @OA\Property(property="profile", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Hanya dokter yang dapat mengakses endpoint ini")
     * )
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        if ($user->role !== 'doctor') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'bio' => 'nullable|string',
            'spesialis' => 'nullable|string',
            'experience_years' => 'nullable|integer',
        ]);

        if ($user->doctorProfile) {
            $user->doctorProfile->update($request->only(['bio', 'spesialis', 'experience_years']));
        }

        return response()->json([
            'message' => 'Profil dokter berhasil diperbarui',
            'profile' => $user->load('doctorProfile')
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/doctor/photo",
     *     tags={"Doctor Panel"},
     *     summary="Upload foto profil dokter",
     *     description="Dokter mengupload foto profil. Foto lama akan dihapus otomatis. Format: jpeg/png/jpg/gif, maks 2MB.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"profile_pic"},
     *                 @OA\Property(property="profile_pic", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Foto berhasil diupload"),
     *     @OA\Response(response=403, description="Hanya dokter yang dapat mengakses")
     * )
     */
    public function uploadProfilePicture(Request $request)
    {
        $user = auth()->user();
        
        if ($user->role !== 'doctor') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
        }

        $file = $request->file('profile_pic');
        $filename = 'doctor_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('profile_pictures', $filename, 'public');

        $user->update(['profile_pic' => $path]);

        return response()->json([
            'message' => 'Foto profil berhasil diupload',
            'profile_pic' => asset('storage/' . $path),
            'user' => $user
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/doctor/photo",
     *     tags={"Doctor Panel"},
     *     summary="Hapus foto profil dokter",
     *     description="Menghapus foto profil dokter dari storage.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Foto berhasil dihapus"),
     *     @OA\Response(response=403, description="Hanya dokter yang dapat mengakses")
     * )
     */
    public function deleteProfilePicture()
    {
        $user = auth()->user();
        
        if ($user->role !== 'doctor') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
        }

        $user->update(['profile_pic' => null]);

        return response()->json([
            'message' => 'Foto profil berhasil dihapus'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/doctor/toggle-online",
     *     tags={"Doctor Panel"},
     *     summary="Toggle status online dokter",
     *     description="Mengubah status online/offline dokter. Jika saat ini online menjadi offline, dan sebaliknya.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Status online berhasil diubah",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Status online berhasil diubah"),
     *             @OA\Property(property="is_online", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=403, description="Hanya dokter yang dapat mengakses")
     * )
     */
    public function toggleOnline(Request $request)
    {
        $user = auth()->user();
        
        if ($user->role !== 'doctor' || !$user->doctorProfile) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user->doctorProfile->update([
            'is_online' => !$user->doctorProfile->is_online
        ]);

        return response()->json([
            'message' => 'Status online berhasil diubah',
            'is_online' => $user->doctorProfile->is_online
        ]);
    }
}
