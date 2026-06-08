<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/profile",
     *     tags={"User - Profile"},
     *     summary="Get profil pengguna",
     *     description="Mengembalikan data profil lengkap pengguna yang login beserta relasi doctorProfile, clinicProfile, dan pets.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profil berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Budi Santoso"),
     *                 @OA\Property(property="email", type="string", example="budi@example.com"),
     *                 @OA\Property(property="role", type="string", example="user"),
     *                 @OA\Property(property="phone", type="string", example="081234567890"),
     *                 @OA\Property(property="address", type="string", nullable=true),
     *                 @OA\Property(property="profile_pic", type="string", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid")
     * )
     */
    public function profile()
    {
        $user = auth()->user()->load(['doctorProfile', 'clinicProfile', 'pets']);
        return response()->json(['user' => $user]);
    }

    /**
     * @OA\Put(
     *     path="/api/user/profile",
     *     tags={"User - Profile"},
     *     summary="Update profil pengguna",
     *     description="Memperbarui data profil pengguna (name, phone, address). Field yang tidak disertakan tidak akan berubah.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Budi Santoso"),
     *             @OA\Property(property="phone", type="string", example="081234567890"),
     *             @OA\Property(property="address", type="string", example="Jl. Sudirman No. 10, Jakarta")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profil berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profil berhasil diperbarui"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
            'profile_pic' => 'sometimes|url',
        ]);

        $user = auth()->user();
        $user->update($request->only(['name', 'phone', 'address', 'profile_pic']));

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'user' => $user
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/profile/photo",
     *     tags={"User - Profile"},
     *     summary="Upload foto profil",
     *     description="Upload gambar foto profil pengguna. Foto lama akan dihapus otomatis. Format: jpeg, png, jpg, gif. Maks: 2MB.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"profile_pic"},
     *                 @OA\Property(property="profile_pic", type="string", format="binary", description="File gambar (jpeg/png/jpg/gif, max 2MB)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Foto berhasil diupload",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Foto profil berhasil diupload"),
     *             @OA\Property(property="profile_pic", type="string", example="http://localhost:8000/storage/profile_pictures/profile_1_1234567890.jpg")
     *         )
     *     ),
     *     @OA\Response(response=422, description="File tidak valid (format tidak didukung atau ukuran terlalu besar)")
     * )
     */
    public function uploadProfilePicture(Request $request)
    {
        $request->validate([
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
        ]);

        $user = auth()->user();

        // Hapus foto lama jika ada
        if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
        }

        // Upload foto baru
        $file = $request->file('profile_pic');
        $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('profile_pictures', $filename, 'public');

        // Update user
        $user->update(['profile_pic' => $path]);

        return response()->json([
            'message' => 'Foto profil berhasil diupload',
            'profile_pic' => asset('storage/' . $path),
            'user' => $user
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/user/profile/photo",
     *     tags={"User - Profile"},
     *     summary="Hapus foto profil",
     *     description="Menghapus foto profil pengguna dari storage dan mengatur profile_pic menjadi null.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Foto berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Foto profil berhasil dihapus")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid")
     * )
     */
    public function deleteProfilePicture()
    {
        $user = auth()->user();

        if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
            Storage::disk('public')->delete($user->profile_pic);
        }

        $user->update(['profile_pic' => null]);

        return response()->json([
            'message' => 'Foto profil berhasil dihapus'
        ]);
    }
}
