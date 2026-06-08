# 🐾 VETRA - Veterinary & Pet Care Application

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/JWT-Auth-000000?style=for-the-badge&logo=json-web-tokens&logoColor=white" alt="JWT">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

<p align="center">
  <strong>Platform kesehatan hewan peliharaan digital yang menghubungkan pemilik hewan, dokter hewan, dan klinik hewan.</strong>
</p>

---

## 🚀 Quick Start

```bash
# 1. Install dependencies
composer install

# 2. Setup environment
cp .env.example .env
php artisan key:generate
php artisan jwt:secret --force

# 3. Configure database in .env
DB_DATABASE=vetra_db
DB_USERNAME=root
DB_PASSWORD=

# 4. Run migrations & seed
php artisan migrate:fresh --seed

# 5. Start server
php artisan serve
```

**Server running at:** `http://localhost:8000`

**Default Accounts:**
- Admin: `admin@vetra.id` / `admin123`
- Doctor: `dokter@vetra.id` / `dokter123`
- Clinic: `klinik@vetra.id` / `klinik123`
- User: `user@vetra.id` / `user123`

---

## 📚 Complete Documentation

**Start here:** [INDEX.md](INDEX.md) - Documentation navigation hub

### Quick Links
- 🚀 **[QUICK_START.md](QUICK_START.md)** - Get started in 5 minutes
- 🔧 **[SETUP_GUIDE.md](SETUP_GUIDE.md)** - Detailed setup instructions
- 📖 **[README_VETRA.md](README_VETRA.md)** - Complete API documentation
- 📝 **[IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)** - What's implemented
- 📁 **[PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md)** - Code organization
- ✅ **[FINAL_CHECKLIST.md](FINAL_CHECKLIST.md)** - Verification checklist

### Testing Resources
- 📮 **[VETRA_Postman_Collection.json](VETRA_Postman_Collection.json)** - Import to Postman
- 🧪 **[test-api.ps1](test-api.ps1)** - PowerShell test script

---

## ✨ Key Features

### 🔐 Authentication & Authorization
- JWT Authentication (tymon/jwt-auth)
- API Key Authentication
- Google OAuth Login
- Role-based Access Control (user, doctor, clinic, admin)

### 👥 User Management
- User registration & profile management
- Pet management (CRUD)
- Notification system

### 🏥 Booking System
- Create & manage bookings
- Doctor confirmation workflow
- Automatic notifications
- Status tracking (pending → confirmed → done)

### 💬 Chat & Consultation
- Real-time chat between users and doctors
- Message history
- Unread counter
- Support text & image messages

### ⭐ Review & Rating
- Review doctors and clinics
- 5-star rating system
- Average rating calculation

### 📚 Articles
- Educational articles about pet health
- Search & filter
- Admin CRUD operations

### 🤖 AI Chatbot
- Powered by Google Gemini AI
- 24/7 pet health consultation
- Indonesian language support

### 🔑 API Key Management
- Generate API keys for third-party apps
- SHA-256 hashing
- Expiration & tracking

---

## 📊 Technical Stack

| Layer | Technology |
|-------|------------|
| **Framework** | Laravel 11 |
| **Language** | PHP 8.3 |
| **Database** | MySQL / PostgreSQL |
| **Authentication** | JWT (tymon/jwt-auth) |
| **OAuth** | Google OAuth (google/apiclient) |
| **AI** | Google Gemini API |
| **API Design** | RESTful |

---

## 📡 API Overview

**Total Endpoints:** 61 routes

### Public Endpoints (8)
- Authentication (register, login, google)
- Doctors list & details
- Clinics list & details
- Articles list & details

### Authenticated Endpoints (35)
- User profile & pets
- Bookings CRUD
- Chat & messages
- Reviews
- Notifications
- API key management
- AI Chatbot

### Role-Specific Endpoints (18)
- **Doctor (5):** Dashboard, manage bookings, update profile
- **Clinic (3):** Dashboard, view bookings, manage doctors
- **Admin (10):** Statistics, CRUD users/doctors/clinics/articles

---

## 🗄️ Database Schema

**13 Tables:**
- `users` - User accounts with roles
- `doctor_profiles` - Doctor information
- `clinic_profiles` - Clinic information
- `pets` - User's pets
- `bookings` - Appointments
- `reviews` - Ratings & reviews
- `articles` - Educational content
- `chats` - Chat rooms
- `messages` - Chat messages
- `notifications` - User notifications
- `api_keys` - API authentication keys
- `cache` & `jobs` - System tables

---

## 🧪 Testing

### Import Postman Collection
```bash
# Import VETRA_Postman_Collection.json to Postman
# Set base_url = http://localhost:8000/api
# Run requests in order (Login will auto-save token)
```

### Run PowerShell Test
```powershell
.\test-api.ps1
```

### Manual Test
```bash
# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@vetra.id","password":"user123"}'

# Get Profile (use token from login)
curl http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## 🔒 Security Features

- ✅ JWT token-based authentication
- ✅ Password hashing (Bcrypt)
- ✅ API key hashing (SHA-256)
- ✅ Role-based access control
- ✅ Input validation
- ✅ SQL injection protection (Eloquent ORM)
- ✅ CORS support
- ✅ Rate limiting ready

---

## 📦 Installation Requirements

- PHP >= 8.3
- Composer
- MySQL >= 5.7 or PostgreSQL
- Extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON

---

## 🚀 Deployment

See [FINAL_CHECKLIST.md](FINAL_CHECKLIST.md#ready-for-production) for production deployment checklist.

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🤝 Contributing

Contributions are welcome! Please read the documentation before submitting pull requests.

---

## 📞 Support

- 📖 Read the [complete documentation](INDEX.md)
- 🔧 Check [troubleshooting guide](SETUP_GUIDE.md#troubleshooting)
- 📝 Review [implementation summary](IMPLEMENTATION_SUMMARY.md)

---

<p align="center">
  <strong>Built with ❤️ using Laravel 11 + JWT Auth + Google OAuth + Gemini AI</strong>
</p>

<p align="center">
  <strong>VETRA - Your Pet's Health, Our Priority 🐾</strong>
</p>
