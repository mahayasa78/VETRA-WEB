<?php

use Illuminate\Support\Facades\Route;

// Landing Page — passes counts for hero stats
Route::get('/', function () {
    $doctorCount  = 0;
    $clinicCount  = 0;
    $articleCount = 0;
    try {
        $doctorCount  = \App\Models\DoctorProfile::count();
        $clinicCount  = \App\Models\ClinicProfile::count();
        $articleCount = \App\Models\Article::count();
    } catch (\Exception $e) {}
    return view('pages.home', compact('doctorCount', 'clinicCount', 'articleCount'));
})->name('home');

// Auth Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Public Pages
Route::get('/klinik', function () {
    try {
        // Ambil data klinik dengan profil dan dokter
        $clinics = \App\Models\User::where('role', 'clinic')
            ->where('is_active', true)
            ->with(['clinicProfile'])
            ->get()
            ->map(function($clinic) {
                $profile = $clinic->clinicProfile;
                
                // Ambil dokter yang bekerja di klinik ini
                $clinicDoctors = \App\Models\DoctorProfile::where('clinic_id', $clinic->id)
                    ->with('user')
                    ->get();
                
                // Format jam operasional
                $jamOperasional = 'Senin - Jumat: 08:00 - 17:00';
                if ($profile && $profile->operational_hours && is_array($profile->operational_hours)) {
                    $days = [];
                    foreach ($profile->operational_hours as $day => $hours) {
                        if (isset($hours['isOpen']) && $hours['isOpen']) {
                            $days[] = $day . ': ' . $hours['open'] . ' - ' . $hours['close'];
                        }
                    }
                    if (!empty($days)) {
                        $jamOperasional = implode(', ', $days);
                    }
                }
                
                return (object)[
                    'id' => $clinic->id,
                    'nama_klinik' => $clinic->name ?? 'Klinik',
                    'alamat' => $profile->address ?? '-',
                    'no_hp' => $profile->phone ?? null,
                    'jam_operasional' => $jamOperasional,
                    'deskripsi' => $clinic->bio ?? null,
                    'doctors' => $clinicDoctors
                ];
            });

        // Ambil data dokter dengan profil dan klinik
        $doctors = \App\Models\DoctorProfile::with(['user', 'clinic.clinicProfile'])
            ->get()
            ->map(function($docProfile) {
                $clinicData = null;
                if ($docProfile->clinic_id) {
                    $clinic = $docProfile->clinic;
                    if ($clinic && $clinic->clinicProfile) {
                        $clinicData = (object)[
                            'id' => $clinic->id,
                            'nama_klinik' => $clinic->name ?? 'Klinik',
                            'alamat' => $clinic->clinicProfile->address ?? '-',
                            'no_hp' => $clinic->clinicProfile->phone ?? null,
                        ];
                    }
                }
                return (object)[
                    'id' => $docProfile->id,
                    'name' => $docProfile->user->name ?? 'Dokter Hewan',
                    'email' => $docProfile->user->email ?? null,
                    'spesialis' => $docProfile->spesialis ?? 'Umum',
                    'clinic' => $clinicData
                ];
            });

        // Format ke JSON untuk JavaScript
        $clinicsJson = $clinics->map(function($c) {
            return [
                'id' => $c->id,
                'nama_klinik' => $c->nama_klinik,
                'alamat' => $c->alamat,
                'no_hp' => $c->no_hp,
                'jam_operasional' => $c->jam_operasional,
                'deskripsi' => $c->deskripsi,
                'doctors' => $c->doctors->map(function($d) {
                    return [
                        'id' => $d->id,
                        'name' => $d->user->name ?? 'Dokter',
                        'spesialis' => $d->spesialis ?? 'Umum'
                    ];
                })
            ];
        });

        $doctorsJson = $doctors;

    } catch (\Exception $e) {
        $clinics = collect([]);
        $doctors = collect([]);
        $clinicsJson = [];
        $doctorsJson = [];
    }

    return view('pages.klinik', compact('clinics', 'doctors', 'clinicsJson', 'doctorsJson'));
})->name('klinik');

Route::get('/artikel', function () {
    // Untuk halaman daftar artikel (server-side render)
    $articles = \App\Models\Article::published()->with('author:id,name,profile_pic')
        ->orderBy('created_at', 'desc')
        ->paginate(12);

    return view('pages.artikel', compact('articles'));
})->name('artikel');

Route::get('/artikel/{id}', function ($id) {
    $article = \App\Models\Article::published()->with('author:id,name,profile_pic')
        ->findOrFail($id);

    $related = \App\Models\Article::published()
        ->where('id', '!=', $article->id)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->with('author:id,name,profile_pic')
        ->get();

    return view('pages.artikel-detail', compact('article', 'related'));
})->name('artikel.detail');

Route::get('/kontak', function () {
    return view('pages.kontak');
})->name('kontak');

// POST contact form
Route::post('/kontak', function (Illuminate\Http\Request $request) {
    $request->validate([
        'nama' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subjek' => 'required|string|max:255',
        'pesan' => 'required|string|max:5000',
    ]);
    
    // Save to database
    \App\Models\ContactMessage::create([
        'name' => $request->nama,
        'email' => $request->email,
        'phone' => $request->phone ?? null,
        'subject' => $request->subjek,
        'message' => $request->pesan,
        'status' => 'unread',
    ]);
    
    return back()->with('success', 'Terima kasih! Pesan Anda telah kami terima. Tim kami akan segera menghubungi Anda.');
})->name('kontak.send');

// User Pages (memerlukan login - hanya view, proteksi via JS)
Route::get('/profile', function () {
    return view('user.profile');
})->name('user.profile');

Route::get('/my-pets', function () {
    return view('user.pets');
})->name('user.pets');

Route::get('/my-bookings', function () {
    return view('user.bookings');
})->name('user.bookings');

Route::get('/booking', function () {
    return view('booking');
})->name('booking');

Route::get('/chat', function () {
    return view('user.chat');
})->name('user.chat');

Route::get('/chatbot', function () {
    return view('user.chatbot');
})->name('user.chatbot');

// Doctor Pages
Route::get('/doctor/dashboard', function () {
    return view('doctor.dashboard');
})->name('doctor.dashboard');

Route::get('/doctor/schedules', function () {
    return view('doctor.schedules');
})->name('doctor.schedules');

Route::get('/doctor/chat', function () {
    return view('doctor.chat');
})->name('doctor.chat');

// Clinic Pages
Route::get('/clinic/dashboard', function () {
    return view('clinic.dashboard');
})->name('clinic.dashboard');

Route::get('/clinic/bookings', function () {
    return view('clinic.bookings');
})->name('clinic.bookings');

Route::get('/clinic/doctors', function () {
    return view('clinic.doctors');
})->name('clinic.doctors');

// Admin Pages
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/users', function () {
    return view('admin.users');
})->name('admin.users');

Route::get('/admin/messages', function () {
    return view('admin.messages');
})->name('admin.messages');

// Legacy: Keep articles route but remove from menu
Route::get('/admin/articles', function () {
    return view('admin.articles');
})->name('admin.articles');

// Legacy redirects
Route::get('/doctors',  fn() => redirect()->route('klinik'));
Route::get('/clinics',  fn() => redirect()->route('klinik'));
Route::get('/articles', fn() => redirect()->route('artikel'));
