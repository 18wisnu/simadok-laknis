# SIMADOK LAKNIS - Blueprint Assembly Instructions

Karena keterbatasan terminal di playground, saya telah menyediakan seluruh struktur inti (Models, Migrations, Controllers, & Views) sebagai blueprint. 

## Cara Menggunakan Blueprint Ini:

1.  **Pindahkan ke Workspace Baru**: Salin seluruh isi folder `drifting-pioneer` ke folder proyek Laravel Anda yang baru.
2.  **Inisialisasi Laravel**: Di folder tujuan, jalankan:
    ```bash
    composer install
    npm install
    cp .env.example .env
    php artisan key:generate
    ```
3.  **Database**: Sesuaikan `.env` dengan database Anda, lalu jalankan:
    ```bash
    php artisan migrate
    ```
4.  **Google OAuth**: 
    - Install Laravel Socialite: `composer require laravel/socialite`
    - Tambahkan kredensial Google di `config/services.php` dan `.env`.
5.  **WhatsApp Notification**:
    - Anda bisa menggunakan provider seperti **Fonnte** atau **Twilio**. 
    - Gunakan fitur Laravel Events/Notifications untuk mengirim pesan saat peminjaman terjadi.
6.  **QR Scanning**:
    - Gunakan library JS seperti `html5-qrcode` untuk mengaktifkan kamera HP di view dashboard.

Blueprint ini sudah dirancang untuk **Mobile-First** dengan CSS kustom di `public/css/app-mobile.css`.

Selamat membangun SIMADOK LAKNIS!
