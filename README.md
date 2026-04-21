# 🏫 E-Absensi Siswa

**E-Absensi Siswa** adalah aplikasi berbasis web modern untuk manajemen presensi siswa sekolah yang efisien, transparan, dan real-time. Aplikasi ini memanfaatkan QR Code untuk mempercepat proses absensi, serta fitur notifikasi WhatsApp otomatis kepada orang tua, dan rekap laporan kehadiran yang komprehensif.

## 🚀 Fitur Unggulan

-   **✨ Landing Page Modern (SaaS Style)**: Tampilan depan profesional, responsif, dan elegan dengan tema Dark/Gradient serta animasi halus. Informasi sekolah (Footer/Hero) dinamis mengikuti pengaturan aplikasi.
-   **📥 Import Data Guru via Excel**: Fitur import masal data guru kini mendukung file **Excel (.xlsx)** yang lebih mudah diedit dibanding CSV. Dilengkapi panduan pengisian dan validasi data otomatis.
-   **📱 Scan QR Code Cepat**: Absensi siswa dilakukan hanya dalam hitungan detik dengan memindai Kartu Pelajar ber-QR Code menggunakan kamera laptop/PC sekolah.
-   **💬 Notifikasi WhatsApp Gateway**: Orang tua otomatis menerima pesan WhatsApp saat anak melakukan absensi Masuk dan Pulang (Real-time).
-   **📄 Laporan PDF Otomatis**: Guru/Admin dapat mengunduh rekap absensi harian, bulanan, hingga semester dalam format PDF siap cetak.
-   **👥 Manajemen Pengguna & Hak Akses**:
    -   **Admin**: Akses penuh ke seluruh sistem, manajemen data master (Siswa, Guru, Kelas, Jurusan), dan setting sekolah.
    -   **Wali Kelas**: Memantau kehadiran siswa di kelasnya, mencetak laporan, dan mengelola izin/sakit.
    -   **Operator/Guru Piket**: Khusus untuk melakukan scanning absensi harian.
-   **📊 Dashboard Informatif**: Statistik kehadiran harian ditampilkan dalam grafik dan angka yang mudah dipahami.
-   **🎨 UI/UX Responsif**: Dibangun dengan Tailwind CSS dan Alpine.js untuk pengalaman pengguna yang mulus di perangkat desktop maupun mobile.
-   **🔧 Pengaturan Sekolah Dinamis**: Logo sekolah, nama sekolah, kepala sekolah, alamat, dan sosial media dapat diatur langsung dari menu Settings tanpa menyentuh kodingan.

## 🛠️ Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan stack teknologi modern untuk performa dan kemudahan pengembangan:

-   **Backend**: [Laravel 12](https://laravel.com) (PHP Entity Framework)
-   **Frontend**:
    -   [Tailwind CSS](https://tailwindcss.com) (Styling)
    -   [Alpine.js](https://alpinejs.dev) (Interaktivitas Ringan)
    -   [Blade Templates](https://laravel.com/docs/blade)
-   **Database**: MySQL
-   **Authentication**: Laravel Breeze
-   **Library Pendukung**:
    -   `maatwebsite/excel`: Untuk export/import data Excel.
    -   `barryvdh/laravel-dompdf`: Untuk generate laporan PDF.
    -   `simplesoftwareio/simple-qrcode`: Untuk generate QR Code siswa.
    -   `aos`: Animate On Scroll untuk efek visual landing page.
    -   `chart.js`: Untuk grafik statistik di dashboard.

## 📊 Entity Relationship Diagram (ERD)

Berikut adalah diagram relasi antar tabel dalam database **E-Absensi Siswa**:

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string nip
        string email UK
        string password
        enum role "super_admin, wali_kelas, orang_tua, guru, siswa, kepala_sekolah"
        boolean is_approved
        boolean is_demo
        string photo
    }

    classes {
        bigint id PK
        string name
        string grade
        string major
        text description
        enum status "active, inactive"
    }

    students {
        bigint id PK
        string nisn
        string nis
        string name
        string email
        enum gender "L, P"
        bigint class_id FK
        string phone_number
        string address
        string birth_place
        date birth_date
        string photo
        enum status "active, inactive"
        string barcode_data UK
    }

    homeroom_teachers {
        bigint id PK
        bigint user_id FK
        bigint class_id FK
    }

    parents {
        bigint id PK
        bigint user_id FK
        string name
        string phone_number
        string relation_status
    }

    parent_student {
        bigint parent_id FK
        bigint student_id FK
    }

    absences {
        bigint id PK
        bigint student_id FK
        datetime attendance_time
        datetime checkout_time
        enum status "hadir, terlambat, alpha, sakit, izin"
        integer late_duration
        text reason
        bigint recorded_by FK
        decimal latitude
        decimal longitude
        string ip_address
    }

    izin_requests {
        bigint id PK
        bigint student_id FK
        date request_date
        enum type "sakit, izin"
        text reason
        string attachment_path
        enum status "pending, approved, rejected"
        bigint approved_by FK
    }

    settings {
        bigint id PK
        string key UK
        text value
        string description
    }

    announcements {
        bigint id PK
        string title
        text content
        enum target_type "all, class"
        bigint target_id
        boolean is_active
    }

    subjects {
        bigint id PK
        string name
        string code
    }

    schedules {
        bigint id PK
        bigint class_id FK
        bigint subject_id FK
        bigint teacher_id FK
        string day
        time start_time
        time end_time
    }

    teaching_journals {
        bigint id PK
        bigint schedule_id FK
        bigint teacher_id FK
        date date
        time start_time
        time end_time
        string topic
        text notes
    }

    subject_attendances {
        bigint id PK
        bigint teaching_journal_id FK
        bigint student_id FK
        string status
        text notes
    }

    teacher_attendances {
        bigint id PK
        bigint user_id FK
        date date
        time clock_in
        time clock_out
        enum status "present, late, permission, sick, alpha"
        decimal latitude
        decimal longitude
        string photo
    }

    %% === RELATIONSHIPS ===
    users ||--o| homeroom_teachers : "wali kelas"
    users ||--o| parents : "orang tua"
    users ||--o{ schedules : "mengajar"
    users ||--o{ teacher_attendances : "absensi guru"
    users ||--o{ teaching_journals : "jurnal"
    users ||--o{ izin_requests : "approver"

    classes ||--o{ students : "memiliki"
    classes ||--o| homeroom_teachers : "wali kelas"
    classes ||--o{ schedules : "jadwal"
    announcements }o--o| classes : "target"

    students ||--o{ absences : "absensi"
    students ||--o{ izin_requests : "izin"
    students ||--o{ subject_attendances : "absensi mapel"

    parents ||--o{ parent_student : ""
    students ||--o{ parent_student : ""

    subjects ||--o{ schedules : "jadwal"
    schedules ||--o{ teaching_journals : "jurnal"
    teaching_journals ||--o{ subject_attendances : "absensi"
```

### Ringkasan Tabel

| Domain | Tabel | Keterangan |
|--------|-------|------------|
| **Auth** | `users` | Semua pengguna (6 role) |
| **Akademik** | `classes`, `students`, `subjects`, `schedules` | Data master sekolah |
| **Absensi Siswa** | `absences`, `izin_requests`, `subject_attendances` | Kehadiran harian & per mapel |
| **Absensi Guru** | `teacher_attendances` | Clock-in/out guru dengan GPS |
| **Wali Kelas** | `homeroom_teachers` | Pivot user ↔ kelas |
| **Orang Tua** | `parents`, `parent_student` | Data ortu & relasi M:N ke siswa |
| **Mengajar** | `teaching_journals` | Jurnal harian guru |
| **Lainnya** | `announcements`, `settings` | Pengumuman & konfigurasi |

## ⚙️ Persyaratan Sistem

Pastikan server Anda memenuhi persyaratan berikut:

-   PHP >= 8.3
-   Composer
-   MySQL / MariaDB
-   Node.js & NPM (untuk compile asset)

## 📥 Cara Instalasi

Ikuti langkah-langkah berikut untuk menjalankan aplikasi di lokal (Localhost):

1.  **Clone Repository**
    ```bash
    git clone https://github.com/username/e-absensi-siswa.git
    cd e-absensi-siswa
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    npm install
    ```

3.  **Konfigurasi Environment**
    Salin file `.env.example` menjadi `.env` dan atur koneksi database Anda.
    ```bash
    cp .env.example .env
    ```
    Buka file `.env` dan sesuaikan DB_DATABASE, DB_USERNAME, dan DB_PASSWORD.

4.  **Generate App Key**
    ```bash
    php artisan key:generate
    ```

5.  **Migrasi & Seeding Database**
    Jalankan perintah ini untuk membuat tabel dan mengisi data awal (Akun Admin Default).
    ```bash
    php artisan migrate --seed
    ```

6.  **Jalankan Server**
    Buka dua terminal terpisah untuk menjalankan server Laravel dan Vite (Asset Bundling).
    
    *Terminal 1:*
    ```bash
    php artisan serve
    ```
    
    *Terminal 2:*
    ```bash
    npm run dev
    ```

7.  **Akses Aplikasi**
    Buka browser dan kunjungi `http://localhost:8000`.

## 🔐 Akun Default (Seeder)

Jika menggunakan `db:seed`, Anda bisa login dengan akun berikut:

-   **Admin**
    -   Email: `admin@admin.com`
    -   Password: `password`

## 🤝 Kontribusi

Kontribusi selalu terbuka! Jika Anda ingin memperbaiki bug atau menambahkan fitur, silakan buat Pull Request.

## 📄 Lisensi

Aplikasi ini bersifat open-source di bawah lisensi [MIT](https://opensource.org/licenses/MIT).
