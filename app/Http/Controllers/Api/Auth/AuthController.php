<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Authentication"},
     *     summary="Registrasi pengguna baru",
     *     description="Membuat akun pengguna baru dengan role 'user' dan mengembalikan JWT token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Budi Santoso"),
     *             @OA\Property(property="email", type="string", format="email", example="budi@example.com"),
     *             @OA\Property(property="password", type="string", format="password", minLength=6, example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registrasi berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registrasi berhasil"),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validasi gagal - email sudah digunakan atau password tidak cocok")
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'is_active' => true,
        ]);
        
        $token = JWTAuth::fromUser($user);
        
        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Authentication"},
     *     summary="Login pengguna",
     *     description="Autentikasi pengguna dengan email dan password. Mengembalikan JWT token untuk digunakan pada endpoint yang membutuhkan autentikasi.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@vetra.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login berhasil"),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Budi Santoso"),
     *                 @OA\Property(property="email", type="string", example="user@vetra.com"),
     *                 @OA\Property(property="role", type="string", enum={"user","doctor","clinic","admin"}, example="user")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Email atau password salah"),
     *     @OA\Response(response=403, description="Akun dinonaktifkan oleh admin")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }
        
        $user = auth()->user();
        
        if (!$user->is_active) {
            JWTAuth::invalidate($token);
            return response()->json(['message' => 'Akun Anda dinonaktifkan'], 403);
        }
        
        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user->load(['doctorProfile', 'clinicProfile']),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh JWT token",
     *     description="Memperbarui JWT token yang sudah atau hampir kadaluarsa.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token berhasil diperbarui",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci..."),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak dapat di-refresh (sudah kadaluarsa atau tidak valid)")
     * )
     */
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token tidak dapat di-refresh'], 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Authentication"},
     *     summary="Logout pengguna",
     *     description="Invalidasi JWT token saat ini dan mengeluarkan pengguna dari sesi.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout berhasil")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid atau sudah kadaluarsa")
     * )
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Logout berhasil']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Logout berhasil'], 200);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     tags={"Authentication"},
     *     summary="Get data pengguna saat ini",
     *     description="Mengembalikan data profil lengkap pengguna yang sedang login beserta relasi doctorProfile, clinicProfile, dan pets.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Data pengguna berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Budi Santoso"),
     *                 @OA\Property(property="email", type="string", example="user@vetra.com"),
     *                 @OA\Property(property="role", type="string", example="user"),
     *                 @OA\Property(property="phone", type="string", example="081234567890"),
     *                 @OA\Property(property="profile_pic", type="string", nullable=true, example=null),
     *                 @OA\Property(property="is_active", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token tidak valid atau tidak ditemukan")
     * )
     */
    public function me()
    {
        $user = auth()->user()->load(['doctorProfile', 'clinicProfile', 'pets']);
        return response()->json(['user' => $user]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/google",
     *     tags={"Authentication"},
     *     summary="Login dengan Google OAuth",
     *     description="Autentikasi menggunakan Google ID Token dari aplikasi mobile/web. Jika akun belum ada, akan dibuat otomatis.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id_token"},
     *             @OA\Property(property="id_token", type="string", example="eyJhbGciOiJSUzI1NiIsImtpZCI6Ij...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login Google berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login dengan Google berhasil"),
     *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1Qi..."),
     *             @OA\Property(property="token_type", type="string", example="bearer")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token Google tidak valid"),
     *     @OA\Response(response=500, description="Kesalahan koneksi Google OAuth")
     * )
     */
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);
        
        try {
            $client = new \Google\Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->id_token);
            
            if (!$payload) {
                return response()->json(['message' => 'Token Google tidak valid'], 401);
            }
            
            $googleId = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'];
            $photo = $payload['picture'] ?? null;
            
            $user = User::where('google_id', $googleId)
                ->orWhere('email', $email)
                ->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'profile_pic' => $photo,
                    'role' => 'user',
                    'password' => null,
                    'is_active' => true,
                ]);
            } else {
                $user->update(['google_id' => $googleId]);
            }
            
            $token = JWTAuth::fromUser($user);
            
            return response()->json([
                'message' => 'Login dengan Google berhasil',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Login Google gagal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/forgot-password",
     *     tags={"Authentication"},
     *     summary="Permintaan reset password",
     *     description="Mengirim permintaan reset password. Saat ini mengembalikan pesan informasi untuk menghubungi administrator.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permintaan diterima",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Fitur reset password akan segera tersedia.")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Email tidak ditemukan dalam sistem")
     * )
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'message' => 'Email tidak ditemukan dalam sistem kami.'
            ], 404);
        }

        // TODO: Implement password reset email functionality
        // For now, just return a success message
        return response()->json([
            'message' => 'Fitur reset password akan segera tersedia. Silakan hubungi administrator untuk bantuan.'
        ], 200);
    }
}
