<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/keys",
     *     tags={"API Key Management"},
     *     summary="Daftar API Key milik user",
     *     description="Mengembalikan semua API Key yang dimiliki pengguna yang sedang login. Key hash tidak ditampilkan demi keamanan, hanya prefix yang terlihat.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar API Key berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="api_keys", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Mobile App Key"),
     *                     @OA\Property(property="key_prefix", type="string", example="vtr_abc1", description="8 karakter pertama key untuk identifikasi"),
     *                     @OA\Property(property="last_used_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid")
     * )
     */
    public function index()
    {
        $keys = auth()->user()->apiKeys()
            ->select(['id', 'name', 'key_prefix', 'last_used_at', 'expires_at', 'is_active', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json(['api_keys' => $keys]);
    }

    /**
     * @OA\Post(
     *     path="/api/keys",
     *     tags={"API Key Management"},
     *     summary="Generate API Key baru",
     *     description="Membuat API Key baru dengan format 'vtr_{4_chars}_{32_chars}'. Key ditampilkan HANYA SEKALI pada response ini. Setelah itu hanya prefix yang tersimpan.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=100, example="Mobile App Key"),
     *             @OA\Property(property="expires_at", type="string", format="date-time", nullable=true, description="Tanggal kadaluarsa (opsional). Contoh: 2026-12-31T23:59:59")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="API Key berhasil dibuat",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="API Key berhasil dibuat. Simpan key ini, tidak akan ditampilkan lagi!"),
     *             @OA\Property(property="api_key", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Mobile App Key"),
     *                 @OA\Property(property="key", type="string", example="vtr_abc1_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", description="SIMPAN KEY INI! Tidak akan ditampilkan lagi"),
     *                 @OA\Property(property="prefix", type="string", example="vtr_abc1"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validasi gagal - nama wajib diisi atau tanggal tidak valid")
     * )
     */
    public function generate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'expires_at' => 'nullable|date|after:now',
        ]);
        
        $rawKey = 'vtr_' . Str::random(4) . '_' . Str::random(32);
        $prefix = substr($rawKey, 0, 8);
        $hash = hash('sha256', $rawKey);
        
        $apiKey = ApiKey::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'key_hash' => $hash,
            'key_prefix' => $prefix,
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);
        
        return response()->json([
            'message' => 'API Key berhasil dibuat. Simpan key ini, tidak akan ditampilkan lagi!',
            'api_key' => [
                'id' => $apiKey->id,
                'name' => $apiKey->name,
                'key' => $rawKey,
                'prefix' => $prefix,
                'expires_at' => $apiKey->expires_at,
                'created_at' => $apiKey->created_at,
            ],
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/keys/{id}",
     *     tags={"API Key Management"},
     *     summary="Revoke (nonaktifkan) API Key",
     *     description="Menonaktifkan sebuah API Key. Key tidak dihapus dari database, hanya di-set is_active=false sehingga tidak dapat digunakan lagi.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID API Key yang akan dinonaktifkan",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="API Key berhasil dinonaktifkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="API Key berhasil dinonaktifkan")
     *         )
     *     ),
     *     @OA\Response(response=404, description="API Key tidak ditemukan atau bukan milik user ini")
     * )
     */
    public function revoke($id)
    {
        $apiKey = ApiKey::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        $apiKey->update(['is_active' => false]);
        
        return response()->json(['message' => 'API Key berhasil dinonaktifkan']);
    }
}
