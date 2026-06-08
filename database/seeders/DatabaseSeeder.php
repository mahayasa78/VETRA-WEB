<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{User, DoctorProfile, ClinicProfile, Article};
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Admin VETRA',
            'email' => 'admin@vetra.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Klinik 1
        $klinik1 = User::create([
            'name' => 'Klinik Hewan Sehat',
            'email' => 'klinik@vetra.id',
            'password' => Hash::make('klinik123'),
            'role' => 'clinic',
            'phone' => '0812-0001-0001',
            'is_active' => true,
        ]);
        
        ClinicProfile::create([
            'user_id' => $klinik1->id,
            'address' => 'Jl. Merdeka No. 1, Jakarta Pusat',
            'phone' => '0812-0001-0001',
            'is_open' => true,
            'operational_hours' => [
                'Senin' => ['isOpen' => true, 'open' => '08:00', 'close' => '17:00'],
                'Selasa' => ['isOpen' => true, 'open' => '08:00', 'close' => '17:00'],
                'Rabu' => ['isOpen' => true, 'open' => '08:00', 'close' => '17:00'],
                'Kamis' => ['isOpen' => true, 'open' => '08:00', 'close' => '17:00'],
                'Jumat' => ['isOpen' => true, 'open' => '08:00', 'close' => '15:00'],
                'Sabtu' => ['isOpen' => true, 'open' => '09:00', 'close' => '14:00'],
                'Minggu' => ['isOpen' => false, 'open' => null, 'close' => null],
            ],
        ]);

        // Klinik 2
        $klinik2 = User::create([
            'name' => 'Pet Care Center Bandung',
            'email' => 'petcare@vetra.id',
            'password' => Hash::make('klinik123'),
            'role' => 'clinic',
            'phone' => '0813-0002-0002',
            'is_active' => true,
        ]);
        
        ClinicProfile::create([
            'user_id' => $klinik2->id,
            'address' => 'Jl. Dago No. 88, Bandung',
            'phone' => '0813-0002-0002',
            'is_open' => true,
            'operational_hours' => [
                'Senin' => ['isOpen' => true, 'open' => '09:00', 'close' => '18:00'],
                'Selasa' => ['isOpen' => true, 'open' => '09:00', 'close' => '18:00'],
                'Rabu' => ['isOpen' => true, 'open' => '09:00', 'close' => '18:00'],
                'Kamis' => ['isOpen' => true, 'open' => '09:00', 'close' => '18:00'],
                'Jumat' => ['isOpen' => true, 'open' => '09:00', 'close' => '18:00'],
                'Sabtu' => ['isOpen' => true, 'open' => '10:00', 'close' => '16:00'],
                'Minggu' => ['isOpen' => true, 'open' => '10:00', 'close' => '14:00'],
            ],
        ]);

        // Klinik 3
        $klinik3 = User::create([
            'name' => 'Animal Hospital Surabaya',
            'email' => 'animalhosp@vetra.id',
            'password' => Hash::make('klinik123'),
            'role' => 'clinic',
            'phone' => '0814-0003-0003',
            'is_active' => true,
        ]);
        
        ClinicProfile::create([
            'user_id' => $klinik3->id,
            'address' => 'Jl. HR Muhammad No. 45, Surabaya',
            'phone' => '0814-0003-0003',
            'is_open' => true,
            'operational_hours' => [
                'Senin' => ['isOpen' => true, 'open' => '07:00', 'close' => '19:00'],
                'Selasa' => ['isOpen' => true, 'open' => '07:00', 'close' => '19:00'],
                'Rabu' => ['isOpen' => true, 'open' => '07:00', 'close' => '19:00'],
                'Kamis' => ['isOpen' => true, 'open' => '07:00', 'close' => '19:00'],
                'Jumat' => ['isOpen' => true, 'open' => '07:00', 'close' => '16:00'],
                'Sabtu' => ['isOpen' => true, 'open' => '08:00', 'close' => '17:00'],
                'Minggu' => ['isOpen' => false, 'open' => null, 'close' => null],
            ],
        ]);

        // Dokter 1 - di Klinik 1
        $dokter1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'dokter@vetra.id',
            'password' => Hash::make('dokter123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        
        DoctorProfile::create([
            'user_id' => $dokter1->id,
            'clinic_id' => $klinik1->id,
            'spesialis' => 'Dokter Hewan Umum',
            'experience_years' => 5,
            'bio' => 'Berpengalaman 5 tahun dalam menangani anjing dan kucing.',
            'is_online' => true,
        ]);

        // Dokter 2 - di Klinik 1
        $dokter2 = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@vetra.id',
            'password' => Hash::make('dokter123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        
        DoctorProfile::create([
            'user_id' => $dokter2->id,
            'clinic_id' => $klinik1->id,
            'spesialis' => 'Spesialis Bedah Hewan',
            'experience_years' => 8,
            'bio' => 'Ahli bedah hewan dengan pengalaman lebih dari 8 tahun.',
            'is_online' => false,
        ]);

        // Dokter 3 - di Klinik 2
        $dokter3 = User::create([
            'name' => 'Andi Wijaya',
            'email' => 'andi.dokter@vetra.id',
            'password' => Hash::make('dokter123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        
        DoctorProfile::create([
            'user_id' => $dokter3->id,
            'clinic_id' => $klinik2->id,
            'spesialis' => 'Spesialis Kucing',
            'experience_years' => 6,
            'bio' => 'Fokus pada perawatan dan pengobatan kucing.',
            'is_online' => true,
        ]);

        // Dokter 4 - di Klinik 3
        $dokter4 = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi.dokter@vetra.id',
            'password' => Hash::make('dokter123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        
        DoctorProfile::create([
            'user_id' => $dokter4->id,
            'clinic_id' => $klinik3->id,
            'spesialis' => 'Spesialis Eksotis',
            'experience_years' => 4,
            'bio' => 'Spesialis dalam menangani hewan eksotis seperti reptil dan burung.',
            'is_online' => true,
        ]);

        // Dokter 5 - di Klinik 3
        $dokter5 = User::create([
            'name' => 'Rudi Hermawan',
            'email' => 'rudi.dokter@vetra.id',
            'password' => Hash::make('dokter123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        
        DoctorProfile::create([
            'user_id' => $dokter5->id,
            'clinic_id' => $klinik3->id,
            'spesialis' => 'Dokter Hewan Umum',
            'experience_years' => 3,
            'bio' => 'Dokter hewan muda dengan semangat tinggi untuk melayani.',
            'is_online' => false,
        ]);

        // Dokter 6 - Konsultasi Online (tanpa klinik)
        $dokter6 = User::create([
            'name' => 'Maya Kusuma',
            'email' => 'maya.dokter@vetra.id',
            'password' => Hash::make('dokter123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        
        DoctorProfile::create([
            'user_id' => $dokter6->id,
            'clinic_id' => null,
            'spesialis' => 'Konsultan Gizi Hewan',
            'experience_years' => 7,
            'bio' => 'Spesialis nutrisi dan gizi hewan peliharaan.',
            'is_online' => true,
        ]);

        // User biasa
        $user = User::create([
            'name' => 'Andi Pratama',
            'email' => 'user@vetra.id',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'is_active' => true,
        ]);
        
        // Artikel
        Article::create([
            'author_id' => $admin->id,
            'title' => 'Tips Menjaga Kesehatan Kucing Peliharaan',
            'description' => 'Panduan lengkap cara menjaga kesehatan kucing agar tetap aktif dan bahagia.',
            'content' => 'Kucing adalah hewan yang mandiri, namun tetap membutuhkan perawatan yang baik...',
            'tags' => 'kucing,kesehatan,perawatan',
            'is_published' => true,
        ]);
        
        Article::create([
            'author_id' => $admin->id,
            'title' => 'Mengenal Vaksin Wajib untuk Anjing',
            'description' => 'Jadwal dan jenis vaksin penting yang harus diberikan pada anjing peliharaan Anda.',
            'content' => 'Vaksinasi adalah salah satu cara terpenting untuk menjaga kesehatan anjing...',
            'tags' => 'anjing,vaksin,kesehatan',
            'is_published' => true,
        ]);
        
        $this->command->info('✅ Seeder berhasil! Akun default:');
        $this->command->info('  Admin   : admin@vetra.id / admin123');
        $this->command->info('  Klinik 1: klinik@vetra.id / klinik123');
        $this->command->info('  Klinik 2: petcare@vetra.id / klinik123');
        $this->command->info('  Klinik 3: animalhosp@vetra.id / klinik123');
        $this->command->info('  Dokter 1: dokter@vetra.id / dokter123');
        $this->command->info('  Dokter 2: siti@vetra.id / dokter123');
        $this->command->info('  Dokter 3: andi.dokter@vetra.id / dokter123');
        $this->command->info('  Dokter 4: dewi.dokter@vetra.id / dokter123');
        $this->command->info('  Dokter 5: rudi.dokter@vetra.id / dokter123');
        $this->command->info('  Dokter 6: maya.dokter@vetra.id / dokter123 (Online)');
        $this->command->info('  User    : user@vetra.id / user123');
    }
}
