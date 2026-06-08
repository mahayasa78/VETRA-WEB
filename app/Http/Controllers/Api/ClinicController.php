<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/clinics",
     *     tags={"Public - Clinics"},
     *     summary="Daftar semua klinik hewan",
     *     description="Mengembalikan daftar klinik hewan yang aktif beserta profil klinik. Dipaginasi 15 per halaman.",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Nomor halaman",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar klinik berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=5),
     *                     @OA\Property(property="name", type="string", example="Klinik Hewan Sehat"),
     *                     @OA\Property(property="profile_pic", type="string", nullable=true),
     *                     @OA\Property(property="clinic_profile", type="object",
     *                         @OA\Property(property="address", type="string", example="Jl. Sudirman No. 5"),
     *                         @OA\Property(property="phone", type="string", example="021-1234567"),
     *                         @OA\Property(property="is_open", type="boolean", example=true)
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'clinic')
            ->where('is_active', true)
            ->with(['clinicProfile']);

        $clinics = $query->paginate(15);

        return response()->json($clinics);
    }

    /**
     * @OA\Get(
     *     path="/api/clinics/{id}",
     *     tags={"Public - Clinics"},
     *     summary="Detail klinik hewan",
     *     description="Mengembalikan detail lengkap sebuah klinik hewan beserta profil klinik.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID klinik (user ID dengan role clinic)",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail klinik berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="clinic", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="clinic_profile", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Klinik tidak ditemukan")
     * )
     */
    public function show($id)
    {
        $clinic = User::where('id', $id)
            ->where('role', 'clinic')
            ->with(['clinicProfile'])
            ->firstOrFail();

        return response()->json(['clinic' => $clinic]);
    }

    /**
     * @OA\Get(
     *     path="/api/clinics/{id}/doctors",
     *     tags={"Public - Clinics"},
     *     summary="Daftar dokter di klinik",
     *     description="Mengembalikan daftar semua dokter yang terdaftar di sebuah klinik.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID klinik",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar dokter klinik berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="doctors", type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function doctors($id)
    {
        $doctors = DoctorProfile::where('clinic_id', $id)
            ->with('user')
            ->get();

        return response()->json(['doctors' => $doctors]);
    }

    /**
     * @OA\Get(
     *     path="/api/clinic/dashboard",
     *     tags={"Clinic Panel"},
     *     summary="Dashboard statistik klinik",
     *     description="Mengembalikan statistik booking klinik (total, pending, confirmed, jumlah dokter) beserta 10 booking terbaru.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Data dashboard berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="stats", type="object",
     *                 @OA\Property(property="total_bookings", type="integer", example=50),
     *                 @OA\Property(property="pending_bookings", type="integer", example=5),
     *                 @OA\Property(property="confirmed_bookings", type="integer", example=20),
     *                 @OA\Property(property="total_doctors", type="integer", example=3)
     *             ),
     *             @OA\Property(property="recent_bookings", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=403, description="Hanya klinik yang dapat mengakses")
     * )
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        if ($user->role !== 'clinic') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $allBookings = Booking::where('clinic_id', $user->id);

        $stats = [
            'total_bookings' => (clone $allBookings)->count(),
            'pending_bookings' => (clone $allBookings)->where('status', 'pending')->count(),
            'confirmed_bookings' => (clone $allBookings)->where('status', 'confirmed')->count(),
            'total_doctors' => DoctorProfile::where('clinic_id', $user->id)->count(),
        ];

        $recentBookings = Booking::where('clinic_id', $user->id)
            ->with([
                'user:id,name,email,profile_pic,phone', 
                'doctor:id,name,email', 
                'pet:id,name,species,breed,age'
            ])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'recent_bookings' => $recentBookings
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/clinic/bookings",
     *     tags={"Clinic Panel"},
     *     summary="Daftar semua booking klinik",
     *     description="Mengembalikan semua booking yang masuk ke klinik. Dapat difilter berdasarkan status.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter berdasarkan status. Gunakan 'all' untuk semua.",
     *         @OA\Schema(type="string", enum={"all","pending","confirmed","rejected","done"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar booking klinik berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="count", type="integer", example=25)
     *         )
     *     ),
     *     @OA\Response(response=403, description="Hanya klinik yang dapat mengakses")
     * )
     */
    public function bookings(Request $request)
    {
        $user = auth()->user();
        
        if ($user->role !== 'clinic') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = Booking::where('clinic_id', $user->id)
            ->with([
                'user:id,name,email,profile_pic,phone', 
                'doctor:id,name,email', 
                'pet:id,name,species,breed,age'
            ])
            ->orderBy('scheduled_at', 'desc');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $bookings = $query->get();

        return response()->json([
            'success' => true,
            'data' => $bookings,
            'count' => $bookings->count()
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/clinic/doctors",
     *     tags={"Clinic Panel"},
     *     summary="Daftar dokter milik klinik",
     *     description="Mengembalikan semua dokter yang terdaftar di klinik yang sedang login.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Daftar dokter berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="doctors", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=403, description="Hanya klinik yang dapat mengakses")
     * )
     */
    public function myDoctors()
    {
        $user = auth()->user();
        
        if ($user->role !== 'clinic') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $doctors = DoctorProfile::where('clinic_id', $user->id)
            ->with('user')
            ->get();

        return response()->json(['doctors' => $doctors]);
    }

    /**
     * @OA\Post(
     *     path="/api/clinic/doctors",
     *     tags={"Clinic Panel"},
     *     summary="Tambah dokter ke klinik",
     *     description="Klinik menambahkan dokter baru. Sistem akan membuat akun user dengan role 'doctor' dan membuat doctor profile yang terhubung ke klinik ini.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="drh. Ahmad Fauzi"),
     *             @OA\Property(property="email", type="string", format="email", example="ahmad.fauzi@klinik.com"),
     *             @OA\Property(property="phone", type="string", nullable=true, example="081122334455"),
     *             @OA\Property(property="password", type="string", minLength=6, example="password123"),
     *             @OA\Property(property="spesialis", type="string", nullable=true, example="Dokter Hewan Umum"),
     *             @OA\Property(property="experience_years", type="integer", nullable=true, example=3),
     *             @OA\Property(property="license_number", type="string", nullable=true, example="SIP-1234-2024"),
     *             @OA\Property(property="bio", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Dokter berhasil ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dokter berhasil ditambahkan"),
     *             @OA\Property(property="doctor", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Email sudah digunakan atau validasi gagal"),
     *     @OA\Response(response=403, description="Hanya klinik yang dapat mengakses")
     * )
     */
    public function addDoctor(Request $request)
    {
        $user = auth()->user();
        
        if ($user->role !== 'clinic') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'spesialis' => 'nullable|string|max:100',
            'experience_years' => 'nullable|integer|min:0',
            'license_number' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:1000',
        ]);

        $doctor = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $doctorProfile = DoctorProfile::create([
            'user_id' => $doctor->id,
            'clinic_id' => $user->id,
            'spesialis' => $request->spesialis ?? 'Dokter Hewan Umum',
            'experience_years' => $request->experience_years ?? 0,
            'license_number' => $request->license_number,
            'bio' => $request->bio,
            'is_online' => false,
        ]);

        return response()->json([
            'message' => 'Dokter berhasil ditambahkan',
            'doctor' => $doctorProfile->load('user')
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/clinic/doctors/{id}",
     *     tags={"Clinic Panel"},
     *     summary="Update data dokter klinik",
     *     description="Klinik memperbarui data dokter yang terdaftar di kliniknya. ID yang digunakan adalah ID doctor_profile.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID doctor profile",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="spesialis", type="string"),
     *             @OA\Property(property="experience_years", type="integer"),
     *             @OA\Property(property="license_number", type="string"),
     *             @OA\Property(property="bio", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Data dokter berhasil diperbarui"),
     *     @OA\Response(response=404, description="Dokter tidak ditemukan di klinik ini"),
     *     @OA\Response(response=403, description="Hanya klinik yang dapat mengakses")
     * )
     */
    public function updateDoctor(Request $request, $id)
    {
        $user = auth()->user();
        
        if ($user->role !== 'clinic') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $doctorProfile = DoctorProfile::where('id', $id)
            ->where('clinic_id', $user->id)
            ->with('user')
            ->firstOrFail();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $doctorProfile->user_id,
            'phone' => 'nullable|string|max:20',
            'spesialis' => 'nullable|string|max:100',
            'experience_years' => 'nullable|integer|min:0',
            'license_number' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:1000',
        ]);

        if ($request->has('name') || $request->has('email') || $request->has('phone')) {
            $doctorProfile->user->update([
                'name' => $request->name ?? $doctorProfile->user->name,
                'email' => $request->email ?? $doctorProfile->user->email,
                'phone' => $request->phone ?? $doctorProfile->user->phone,
            ]);
        }

        $doctorProfile->update([
            'spesialis' => $request->spesialis ?? $doctorProfile->spesialis,
            'experience_years' => $request->experience_years ?? $doctorProfile->experience_years,
            'license_number' => $request->license_number ?? $doctorProfile->license_number,
            'bio' => $request->bio ?? $doctorProfile->bio,
        ]);

        return response()->json([
            'message' => 'Data dokter berhasil diperbarui',
            'doctor' => $doctorProfile->load('user')
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/clinic/doctors/{id}",
     *     tags={"Clinic Panel"},
     *     summary="Hapus dokter dari klinik",
     *     description="Menghapus dokter dari klinik. Dokter yang masih memiliki booking pending/confirmed tidak dapat dihapus. Akun dokter dinonaktifkan (tidak dihapus) untuk menjaga histori booking.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID doctor profile",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Dokter berhasil dihapus dari klinik"),
     *     @OA\Response(response=400, description="Dokter masih memiliki booking aktif"),
     *     @OA\Response(response=404, description="Dokter tidak ditemukan di klinik ini")
     * )
     */
    public function removeDoctor($id)
    {
        $user = auth()->user();
        
        if ($user->role !== 'clinic') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $doctorProfile = DoctorProfile::where('id', $id)
            ->where('clinic_id', $user->id)
            ->firstOrFail();

        $hasActiveBookings = \App\Models\Booking::where('doctor_id', $doctorProfile->user_id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasActiveBookings) {
            return response()->json([
                'error' => 'Tidak dapat menghapus dokter yang masih memiliki booking aktif'
            ], 400);
        }

        $doctorProfile->delete();
        $doctorProfile->user->update(['is_active' => false]);

        return response()->json([
            'message' => 'Dokter berhasil dihapus dari klinik'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/clinic/photo",
     *     tags={"Clinic Panel"},
     *     summary="Upload foto profil klinik",
     *     description="Klinik mengupload foto profil. Foto lama dihapus otomatis. Format: jpeg/png/jpg/gif, maks 2MB.",
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
     *     @OA\Response(response=200, description="Foto profil berhasil diupload"),
     *     @OA\Response(response=422, description="File tidak valid"),
     *     @OA\Response(response=403, description="Hanya klinik yang dapat mengakses")
     * )
     */
    public function uploadProfilePicture(Request $request)
    {
        $user = auth()->user();
        
        if ($user->role !== 'clinic') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'profile_pic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($user->profile_pic && \Storage::disk('public')->exists($user->profile_pic)) {
            \Storage::disk('public')->delete($user->profile_pic);
        }

        $file = $request->file('profile_pic');
        $filename = 'clinic_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
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
     *     path="/api/clinic/photo",
     *     tags={"Clinic Panel"},
     *     summary="Hapus foto profil klinik",
     *     description="Menghapus foto profil klinik dari storage.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Foto profil berhasil dihapus"),
     *     @OA\Response(response=403, description="Hanya klinik yang dapat mengakses")
     * )
     */
    public function deleteProfilePicture()
    {
        $user = auth()->user();
        
        if ($user->role !== 'clinic') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($user->profile_pic && \Storage::disk('public')->exists($user->profile_pic)) {
            \Storage::disk('public')->delete($user->profile_pic);
        }

        $user->update(['profile_pic' => null]);

        return response()->json([
            'message' => 'Foto profil berhasil dihapus'
        ]);
    }
}
