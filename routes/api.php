<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\{
    ApiKeyController,
    BookingController,
    ReviewController,
    ChatbotController,
    ArticleController,
    DoctorController,
    ClinicController,
    ChatController,
    ContactMessageController
};
use App\Http\Controllers\Api\Doctor\DoctorBookingController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\User\{UserController, PetController, NotificationController};

// ─── PUBLIC ROUTES ───────────────────────────────────────────────────────────

// Auth
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google', [AuthController::class, 'googleLogin']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
});

// Public content
Route::get('/doctors', [DoctorController::class, 'index']);
Route::get('/doctors/{id}', [DoctorController::class, 'show']);
Route::get('/doctors/{id}/reviews', [ReviewController::class, 'forDoctor']);
Route::get('/clinics', [ClinicController::class, 'index']);
Route::get('/clinics/{id}', [ClinicController::class, 'show']);
Route::get('/clinics/{id}/reviews', [ReviewController::class, 'forClinic']);
Route::get('/clinics/{id}/doctors', [ClinicController::class, 'doctors']);
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);

// Contact messages (public)
Route::post('/contact', [ContactMessageController::class, 'store']);

// ─── AUTHENTICATED ROUTES (JWT) ───────────────────────────────────────────────

Route::middleware('jwt.auth')->group(function () {

    // Auth utilities
    Route::prefix('auth')->group(function () {
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // User profile & data
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'update']);
        Route::post('/profile/picture', [UserController::class, 'uploadProfilePicture']);
        Route::delete('/profile/picture', [UserController::class, 'deleteProfilePicture']);
        Route::get('/pets', [PetController::class, 'index']);
        Route::post('/pets', [PetController::class, 'store']);
        Route::put('/pets/{id}', [PetController::class, 'update']);
        Route::delete('/pets/{id}', [PetController::class, 'destroy']);
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::patch('/notifications/read', [NotificationController::class, 'markAllRead']);
    });

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::delete('/bookings/{id}', [BookingController::class, 'cancel']);

    // Reviews (authenticated write)
    Route::post('/reviews', [ReviewController::class, 'store']);

    // Chat
    Route::prefix('chats')->group(function () {
        Route::get('/', [ChatController::class, 'index']);
        Route::post('/{doctorId}', [ChatController::class, 'startOrGet']);
        Route::get('/{chatId}/messages', [ChatController::class, 'messages']);
        Route::post('/{chatId}/messages', [ChatController::class, 'sendMessage']);
        Route::patch('/{chatId}/read', [ChatController::class, 'markRead']);
    });

    // Chatbot
    Route::post('/chatbot/ask', [ChatbotController::class, 'ask']);

    // API Keys management
    Route::prefix('keys')->group(function () {
        Route::get('/', [ApiKeyController::class, 'index']);
        Route::post('/', [ApiKeyController::class, 'generate']);
        Route::delete('/{id}', [ApiKeyController::class, 'revoke']);
    });

    // ─── DOCTOR ROUTES ────────────────────────────────────────────────────────
    Route::middleware('role:doctor')->prefix('doctor')->group(function () {
        Route::get('/dashboard', [DoctorBookingController::class, 'dashboard']);
        Route::get('/bookings', [DoctorBookingController::class, 'index']);
        Route::patch('/bookings/{id}/status', [DoctorBookingController::class, 'updateStatus']);
        Route::patch('/profile', [DoctorController::class, 'updateProfile']);
        Route::post('/profile/picture', [DoctorController::class, 'uploadProfilePicture']);
        Route::delete('/profile/picture', [DoctorController::class, 'deleteProfilePicture']);
        Route::patch('/online-status', [DoctorController::class, 'toggleOnline']);
    });

    // ─── CLINIC ROUTES ────────────────────────────────────────────────────────
    Route::middleware('role:clinic')->prefix('clinic')->group(function () {
        Route::get('/dashboard', [ClinicController::class, 'dashboard']);
        Route::get('/bookings', [ClinicController::class, 'bookings']);
        Route::get('/doctors', [ClinicController::class, 'myDoctors']);
        Route::post('/doctors', [ClinicController::class, 'addDoctor']);
        Route::put('/doctors/{id}', [ClinicController::class, 'updateDoctor']);
        Route::delete('/doctors/{id}', [ClinicController::class, 'removeDoctor']);
        Route::post('/profile/picture', [ClinicController::class, 'uploadProfilePicture']);
        Route::delete('/profile/picture', [ClinicController::class, 'deleteProfilePicture']);
    });

    // ─── ADMIN ROUTES ─────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'stats']);
        
        // General user CRUD
        Route::get('/users', [AdminController::class, 'listUsers']);
        Route::post('/users', [AdminController::class, 'createUser']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        
        // Contact messages management
        Route::get('/messages', [ContactMessageController::class, 'index']);
        Route::get('/messages/stats', [ContactMessageController::class, 'stats']);
        Route::get('/messages/{id}', [ContactMessageController::class, 'show']);
        Route::post('/messages/{id}/reply', [ContactMessageController::class, 'reply']);
        Route::delete('/messages/{id}', [ContactMessageController::class, 'destroy']);
        
        // Legacy doctor-specific endpoints (can be removed if not used)
        Route::post('/doctors', [AdminController::class, 'createDoctor']);
        Route::put('/doctors/{id}', [AdminController::class, 'updateDoctor']);
        Route::delete('/doctors/{id}', [AdminController::class, 'deleteDoctor']);
        
        // Legacy clinic-specific endpoints (can be removed if not used)
        Route::post('/clinics', [AdminController::class, 'createClinic']);
        Route::put('/clinics/{id}', [AdminController::class, 'updateClinic']);
        Route::delete('/clinics/{id}', [AdminController::class, 'deleteClinic']);
        
        // Articles CRUD (kept for future use, but removed from menu)
        Route::get('/articles', [AdminController::class, 'listArticles']);
        Route::post('/articles', [ArticleController::class, 'store']);
        Route::put('/articles/{id}', [ArticleController::class, 'update']);
        Route::delete('/articles/{id}', [ArticleController::class, 'destroy']);
    });
});

// ─── API KEY ROUTES (alternatif auth via X-API-Key header) ───────────────────
Route::middleware('api.key')->prefix('v2')->group(function () {
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/clinics', [ClinicController::class, 'index']);
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::post('/chatbot/ask', [ChatbotController::class, 'ask']);
});
