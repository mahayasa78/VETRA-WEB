<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Booking, Article, DoctorProfile, ClinicProfile};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/stats",
     *     tags={"Admin Panel"},
     *     summary="Statistik platform (Admin)",
     *     description="Mengembalikan ringkasan statistik platform: total users, doctors, clinics, articles, dan bookings.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistik berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_users", type="integer", example=100),
     *             @OA\Property(property="total_doctors", type="integer", example=15),
     *             @OA\Property(property="total_clinics", type="integer", example=8),
     *             @OA\Property(property="total_articles", type="integer", example=25),
     *             @OA\Property(property="total_bookings", type="integer", example=200),
     *             @OA\Property(property="pending_bookings", type="integer", example=10),
     *             @OA\Property(property="done_bookings", type="integer", example=150)
     *         )
     *     ),
     *     @OA\Response(response=403, description="Hanya admin yang dapat mengakses")
     * )
     */
    public function stats()
    {
        return response()->json([
            'total_users' => User::where('role', 'user')->count(),
            'total_doctors' => User::where('role', 'doctor')->count(),
            'total_clinics' => User::where('role', 'clinic')->count(),
            'total_articles' => Article::published()->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::pending()->count(),
            'done_bookings' => Booking::done()->count(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/users",
     *     tags={"Admin Panel"},
     *     summary="Daftar semua pengguna (Admin)",
     *     description="Mengembalikan daftar semua user dengan role user/doctor/clinic. Dapat difilter berdasarkan role. Dipaginasi 20 per halaman.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filter berdasarkan role (user/doctor/clinic). Gunakan 'all' untuk semua.",
     *         @OA\Schema(type="string", enum={"all","user","doctor","clinic"})
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Nomor halaman",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar pengguna berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="total", type="integer"),
     *             @OA\Property(property="current_page", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Hanya admin yang dapat mengakses")
     * )
     */
    public function listUsers(Request $request)
    {
        $query = User::with(['doctorProfile', 'clinicProfile'])
            ->whereIn('role', ['user', 'doctor', 'clinic'])
            ->orderBy('created_at', 'desc');
        
        if ($request->has('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }
        
        $users = $query->paginate(20);
        return response()->json($users);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/users",
     *     tags={"Admin Panel"},
     *     summary="Tambah pengguna baru (Admin)",
     *     description="Admin membuat akun pengguna baru. Untuk role doctor/clinic, profile terkait juga dibuat otomatis.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","role"},
     *             @OA\Property(property="name", type="string", example="Budi Santoso"),
     *             @OA\Property(property="email", type="string", format="email", example="budi@example.com"),
     *             @OA\Property(property="password", type="string", minLength=6, example="password123"),
     *             @OA\Property(property="role", type="string", enum={"user","doctor","clinic"}, example="user"),
     *             @OA\Property(property="phone", type="string", nullable=true),
     *             @OA\Property(property="address", type="string", nullable=true),
     *             @OA\Property(property="spesialis", type="string", nullable=true, description="Khusus role doctor"),
     *             @OA\Property(property="clinic_id", type="integer", nullable=true, description="Khusus role doctor")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User berhasil ditambahkan"),
     *     @OA\Response(response=422, description="Validasi gagal atau email sudah ada")
     * )
     */
    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:user,doctor,clinic',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        if ($request->role === 'doctor') {
            DoctorProfile::create([
                'user_id' => $user->id,
                'spesialis' => $request->spesialis ?? 'Umum',
                'bio' => $request->bio,
                'experience_years' => $request->experience_years ?? 0,
                'clinic_id' => $request->clinic_id,
            ]);
        } elseif ($request->role === 'clinic') {
            ClinicProfile::create([
                'user_id' => $user->id,
                'address' => $request->address,
                'phone' => $request->phone,
            ]);
        }

        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'user' => $user->load(['doctorProfile', 'clinicProfile'])
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/users/{id}",
     *     tags={"Admin Panel"},
     *     summary="Update data pengguna (Admin)",
     *     description="Admin memperbarui data pengguna termasuk profil dokter/klinik jika ada.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID pengguna",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", nullable=true, description="Biarkan kosong jika tidak ingin mengubah password"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="address", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User berhasil diupdate"),
     *     @OA\Response(response=404, description="User tidak ditemukan")
     * )
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::where('id', $id)->whereIn('role', ['user', 'doctor', 'clinic'])->firstOrFail();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $updateData = $request->only(['name', 'email', 'phone', 'address']);
        
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        if ($user->role === 'doctor' && $user->doctorProfile) {
            $user->doctorProfile->update($request->only(['spesialis', 'bio', 'experience_years', 'clinic_id']));
        } elseif ($user->role === 'clinic' && $user->clinicProfile) {
            $user->clinicProfile->update($request->only(['address', 'phone', 'is_open', 'operational_hours']));
        }

        return response()->json([
            'message' => 'User berhasil diupdate',
            'user' => $user->fresh(['doctorProfile', 'clinicProfile'])
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/users/{id}",
     *     tags={"Admin Panel"},
     *     summary="Hapus pengguna (Admin)",
     *     description="Admin menghapus akun pengguna. Perhatian: ini akan menghapus data permanen.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID pengguna",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User berhasil dihapus")
     *         )
     *     ),
     *     @OA\Response(response=404, description="User tidak ditemukan")
     * )
     */
    public function deleteUser($id)
    {
        $user = User::where('id', $id)->whereIn('role', ['user', 'doctor', 'clinic'])->firstOrFail();
        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus']);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/doctors",
     *     tags={"Admin Panel"},
     *     summary="Tambah dokter baru (Admin)",
     *     description="Admin membuat akun dokter baru beserta doctor profile.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","spesialis"},
     *             @OA\Property(property="name", type="string", example="drh. Ani Wulandari"),
     *             @OA\Property(property="email", type="string", format="email", example="ani@klinik.com"),
     *             @OA\Property(property="password", type="string", minLength=6),
     *             @OA\Property(property="spesialis", type="string", example="Bedah Hewan"),
     *             @OA\Property(property="clinic_id", type="integer", nullable=true),
     *             @OA\Property(property="experience_years", type="integer", nullable=true),
     *             @OA\Property(property="bio", type="string", nullable=true),
     *             @OA\Property(property="phone", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Dokter berhasil ditambahkan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function createDoctor(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'spesialis' => 'required|string',
            'clinic_id' => 'nullable|exists:users,id',
            'experience_years' => 'nullable|integer|min:0',
            'bio' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'doctor',
            'phone' => $request->phone,
        ]);
        
        DoctorProfile::create([
            'user_id' => $user->id,
            'clinic_id' => $request->clinic_id,
            'spesialis' => $request->spesialis,
            'bio' => $request->bio,
            'experience_years' => $request->experience_years ?? 0,
        ]);
        
        return response()->json(['message' => 'Dokter berhasil ditambahkan', 'doctor' => $user->load('doctorProfile')], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/doctors/{id}",
     *     tags={"Admin Panel"},
     *     summary="Update data dokter (Admin)",
     *     description="Admin memperbarui data dokter dan profilnya.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="spesialis", type="string"),
     *             @OA\Property(property="bio", type="string"),
     *             @OA\Property(property="experience_years", type="integer"),
     *             @OA\Property(property="clinic_id", type="integer", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Data dokter diperbarui"),
     *     @OA\Response(response=404, description="Dokter tidak ditemukan")
     * )
     */
    public function updateDoctor(Request $request, $id)
    {
        $user = User::where('id', $id)->where('role', 'doctor')->firstOrFail();
        
        $user->update($request->only(['name', 'phone', 'profile_pic']));
        
        if ($user->doctorProfile) {
            $user->doctorProfile->update($request->only(['spesialis', 'bio', 'experience_years', 'clinic_id']));
        }
        
        return response()->json(['message' => 'Data dokter diperbarui', 'doctor' => $user->load('doctorProfile')]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/doctors/{id}",
     *     tags={"Admin Panel"},
     *     summary="Hapus dokter (Admin)",
     *     description="Admin menghapus akun dokter secara permanen.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Dokter berhasil dihapus"),
     *     @OA\Response(response=404, description="Dokter tidak ditemukan")
     * )
     */
    public function deleteDoctor($id)
    {
        $user = User::where('id', $id)->where('role', 'doctor')->firstOrFail();
        $user->delete();
        return response()->json(['message' => 'Dokter berhasil dihapus']);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/clinics",
     *     tags={"Admin Panel"},
     *     summary="Tambah klinik baru (Admin)",
     *     description="Admin membuat akun klinik baru beserta clinic profile.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="Klinik Hewan Maju"),
     *             @OA\Property(property="email", type="string", format="email", example="klinik@example.com"),
     *             @OA\Property(property="password", type="string", minLength=6),
     *             @OA\Property(property="address", type="string", nullable=true),
     *             @OA\Property(property="phone", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Klinik berhasil ditambahkan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function createClinic(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'clinic',
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        
        ClinicProfile::create([
            'user_id' => $user->id,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);
        
        return response()->json(['message' => 'Klinik berhasil ditambahkan', 'clinic' => $user->load('clinicProfile')], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/clinics/{id}",
     *     tags={"Admin Panel"},
     *     summary="Update data klinik (Admin)",
     *     description="Admin memperbarui data klinik dan profilnya.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="is_open", type="boolean"),
     *             @OA\Property(property="operational_hours", type="string", example="08:00-17:00")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Data klinik diperbarui"),
     *     @OA\Response(response=404, description="Klinik tidak ditemukan")
     * )
     */
    public function updateClinic(Request $request, $id)
    {
        $user = User::where('id', $id)->where('role', 'clinic')->firstOrFail();
        $user->update($request->only(['name', 'phone', 'address', 'profile_pic']));
        if ($user->clinicProfile) {
            $user->clinicProfile->update($request->only(['address', 'phone', 'is_open', 'operational_hours']));
        }
        return response()->json(['message' => 'Data klinik diperbarui', 'clinic' => $user->load('clinicProfile')]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/clinics/{id}",
     *     tags={"Admin Panel"},
     *     summary="Hapus klinik (Admin)",
     *     description="Admin menghapus akun klinik secara permanen.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Klinik berhasil dihapus"),
     *     @OA\Response(response=404, description="Klinik tidak ditemukan")
     * )
     */
    public function deleteClinic($id)
    {
        $user = User::where('id', $id)->where('role', 'clinic')->firstOrFail();
        $user->delete();
        return response()->json(['message' => 'Klinik berhasil dihapus']);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/articles",
     *     tags={"Admin Panel"},
     *     summary="Daftar semua artikel (Admin)",
     *     description="Admin melihat semua artikel termasuk yang belum dipublish. Dapat difilter berdasarkan status dan search.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Cari berdasarkan judul atau deskripsi",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter berdasarkan status publish",
     *         @OA\Schema(type="string", enum={"published","draft"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar artikel berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=403, description="Hanya admin yang dapat mengakses")
     * )
     */
    public function listArticles(Request $request)
    {
        $query = Article::with('author:id,name,profile_pic');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(15);
        return response()->json($articles);
    }
}
