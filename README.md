# 📚 Sistem Repositori Ilmiah dengan AI Recommendation

Sistem informasi manajemen repositori publikasi ilmiah (skripsi/jurnal) yang dilengkapi dengan fitur **Content-Based Recommendation**. Sistem ini menggunakan algoritma **TF-IDF (Term Frequency - Inverse Document Frequency)** dan **Cosine Similarity** untuk menyajikan rekomendasi dokumen terkait secara otomatis berdasarkan kedekatan teks pada Judul, Kata Kunci, dan Abstrak.

## 🚀 Fitur Utama
- **Manajemen Publikasi:** CRUD data skripsi/jurnal dengan panel admin yang elegan menggunakan **FilamentPHP**.
- **Smart Recommendation Engine:** Mencari 3-5 dokumen paling relevan menggunakan perhitungan jarak vektor (Cosine Similarity).
- **Text Preprocessing (NLP):** Menggunakan library **Sastrawi** untuk *Case Folding*, *Punctuation Removal*, *Stopword Removal*, dan *Stemming* teks berbahasa Indonesia.
- **Weighted Fields:** Pemberian bobot ganda pada Judul (3x) dan Kata Kunci (2x) untuk meningkatkan akurasi *Information Retrieval*.
- **Background Jobs (Queues):** Perhitungan matriks algoritma berjalan secara asinkron di belakang layar agar antarmuka pengguna tetap sangat cepat (*zero lag*).

## 🛠️ Teknologi yang Digunakan
- **Framework:** Laravel 11.x
- **Admin Panel:** Filament v3
- **NLP Library:** Sastrawi (Indonesian Stemmer)
- **Database:** MySQL
- **Styling:** Tailwind CSS

## 📋 Metode Pengembangan (Prototyping)
Penelitian dan pengembangan sistem ini menggunakan metode **Prototyping**, dengan tahapan iterasi:
1. **Pengumpulan Kebutuhan:** Analisis kebutuhan pengelolaan arsip skripsi dan kelemahan pencarian manual.
2. **Membangun Prototipe:** Pembuatan rancangan antarmuka (UI/UX) dan arsitektur *database*.
3. **Evaluasi Prototipe:** Pengujian fungsionalitas awal bersama pengguna/pakar.
4. **Pengkodean Sistem:** Implementasi logika *backend*, panel Filament, dan algoritma TF-IDF.
5. **Pengujian Sistem:** Evaluasi metrik algoritma menggunakan *Precision, Recall,* dan *F1-Score*.

## ⚙️ Cara Instalasi (Local Development)

1. Clone repositori ini:
    ```bash
    git clone [https://github.com/username-kamu/nama-repo-kamu.git](https://github.com/username-kamu/nama-repo-kamu.git)
   
    1. Masuk ke direktori dan install dependensi PHP:
    cd nama-repo-kamu
    composer install
    Copy file .env.example menjadi .env dan sesuaikan koneksi database:

    2. Copy file .env.example menjadi .env dan sesuaikan koneksi database:
    cp .env.example .env
    php artisan key:generate
    Jalankan migrasi database:

    3. Jalankan migrasi database:
    php artisan migrate

    4. Jalankan lokal server dan Queue Worker (untuk background jobs algoritma):
    php artisan serve

    5. Buka terminal baru, lalu jalankan:
    php artisan queue:work

2. Proses Upload ke GitHub via Terminal
    ```bash
    1. Buka akun GitHub kamu di *browser*, buat repositori baru (klik tombol **New**). Beri nama (misalnya `repo-ilmiah-tfidf`), biarkan opsi "Add a README file" **tidak tercentang** (karena kita sudah buat sendiri).
    2. Buka terminal/CMD di dalam folder *project* Laravel kamu.
    3. Jalankan perintah ini secara berurutan:
    # 1. Inisialisasi git di folder project
    git init

    # 2. Tambahkan semua file (termasuk README.md) ke keranjang git
    git add .

    # 3. Beri pesan komit pertama
    git commit -m "Initial commit: Finalisasi sistem TF-IDF, Queue, dan Filament"

    # 4. Ubah nama branch utama menjadi 'main'
    git branch -M main

    # 5. Hubungkan ke repositori GitHub kamu (GANTI URL DI BAWAH DENGAN URL REPO-MU)
    git remote add origin https://github.com/username-kamu/repo-ilmiah-tfidf.git

    # 6. Dorong (push) kodenya ke GitHub
    git push -u origin main
