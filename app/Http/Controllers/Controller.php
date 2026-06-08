<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="VETRA API",
 *     version="2.0.0",
 *     description="API Platform Kesehatan & Booking Klinik Hewan Peliharaan VETRA. Mendukung autentikasi JWT Bearer Token dan API Key (X-API-Key header).",
 *     @OA\Contact(
 *         email="admin@vetra.id",
 *         name="VETRA Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="VETRA API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Masukkan JWT Token dari endpoint /api/auth/login. Format: Bearer {token}"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="apiKeyAuth",
 *     type="apiKey",
 *     in="header",
 *     name="X-API-Key",
 *     description="API Key untuk akses endpoint v2. Generate via /api/keys"
 * )
 *
 * @OA\Tag(name="Authentication", description="Register, Login, Logout, Token Management")
 * @OA\Tag(name="Public - Doctors", description="Daftar dokter hewan (publik, tanpa autentikasi)")
 * @OA\Tag(name="Public - Clinics", description="Daftar klinik hewan (publik, tanpa autentikasi)")
 * @OA\Tag(name="Public - Articles", description="Artikel kesehatan hewan (publik)")
 * @OA\Tag(name="Public - Reviews", description="Ulasan dokter & klinik (publik)")
 * @OA\Tag(name="User - Profile", description="Manajemen profil pengguna")
 * @OA\Tag(name="User - Pets", description="Manajemen hewan peliharaan")
 * @OA\Tag(name="User - Bookings", description="Pembuatan & manajemen booking")
 * @OA\Tag(name="User - Chat", description="Chat & konsultasi dengan dokter")
 * @OA\Tag(name="User - Chatbot", description="AI Chatbot berbasis Gemini")
 * @OA\Tag(name="User - Notifications", description="Notifikasi pengguna")
 * @OA\Tag(name="Doctor Panel", description="Dashboard & manajemen booking untuk dokter")
 * @OA\Tag(name="Clinic Panel", description="Dashboard & manajemen dokter untuk klinik")
 * @OA\Tag(name="Admin Panel", description="Manajemen penuh platform (Admin only)")
 * @OA\Tag(name="API Key Management", description="Generate & revoke API Key")
 * @OA\Tag(name="API v2 (API Key)", description="Endpoint v2 dengan autentikasi X-API-Key")
 */
abstract class Controller
{
    //
}

