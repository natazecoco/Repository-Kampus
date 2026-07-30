# 28 JULI 2026

# Software Design & Development Progress
**Project:** Repositori Ilmiah & Sistem Rekomendasi (Skripsi)
**Author:** Radhwa Radinka Wiranata
**Tech Stack:** Laravel, FilamentPHP (Admin Panel), React (Frontend)
**Core Algorithm:** TF-IDF + Cosine Similarity (Content-Based Recommendation)

---

## 1. Maksud & Tujuan (Project Objective)
Sistem ini dibangun untuk mendigitalisasi dan mengelola dokumen ilmiah mahasiswa (skripsi, artikel, buku). Tujuan utamanya adalah membangun **Sistem Rekomendasi berbasis Konten (Content-based)** menggunakan TF-IDF dan Cosine Similarity agar mahasiswa dapat menemukan referensi literatur secara kontekstual. 

Fokus pengembangan saat ini adalah menstabilkan fondasi *repository*, manajemen hak akses berkas, dan kurasi taksonomi (topik) yang bersih untuk mendukung *Knowledge-based Recommendation* di masa depan.

---

## 2. Riwayat Pengembangan (Development History)

### Fase 1: Inisiasi, Environment, & Perbaikan Inti (24 Mei - 21 Juli 2026)
Fase ini berfokus pada penyelesaian kendala lingkungan (*environment*), perbaikan *bug* fatal bawaan, dan pemisahan arsitektur pengguna.
* **24 Mei 2026:** Inisiasi awal proyek repositori ilmiah.
* **Penyesuaian Lingkungan:** Memastikan aplikasi berjalan stabil di Node.js 24 (untuk build Vite 8 tanpa ketergantungan Bunny Fonts) dan PHP 8.5 Laragon di VS Code.
* **Perbaikan Autentikasi:** Memperbaiki *fatal error* Filament 5, menyamakan *role* akun menjadi `admin` dan `student`. Memisahkan alur login secara tegas (Mahasiswa via `/login` dengan NPM; Admin via `/admin/login` dengan Email).
* **21 Juli 2026:** Finalisasi *blueprint* sistem rekomendasi awal dan penulisan dokumentasi README. Memastikan semua *file* PHP utama lolos *linting* dan *test* Laravel lulus.

### Fase 2: Manajemen Dokumen & Hak Akses Berkas (24 Juli - 26 Juli 2026)
Membangun fitur inti repositori, pengamanan berkas fisik, dan sanitasi input.
* **24 Juli 2026:** Pembaruan logika hak akses. Publikasi kini dipisah berdasarkan jenis (*container*/jurnal/universitas) dan visibilitas (*public* vs *restricted*).
* **Keamanan Tambahan:** Memperbaiki keamanan *output highlight* abstrak agar sistem kebal dari sisipan HTML berbahaya (*Cross-Site Scripting*).
* **26 Juli 2026:** Menyelaraskan alur `DocumentController` dengan `FileAccessController` untuk memastikan izin unduh PDF berjalan sesuai hak akses. Menambahkan *soft completeness check* pada form Filament.

### Fase 3: Arsitektur Taksonomi & Fitur Panel Admin (26 Juli 2026)
*Fase masif perbaikan manajemen data backend.*
* **Keamanan Panel:** Membatasi akses panel Filament murni hanya untuk akun dengan *role* admin (`TopicPolicy`).
* **Migrasi Database:** Pembuatan tabel `topics`, *pivot table*, *factory*, dan eksekusi `TopicSeeder`. Menambahkan/memeriksa migrasi untuk normalisasi akun lama.
* **Fitur Kurasi Admin:** Implementasi sistem *merge topic* secara massal (*bulk*), *toggle active*, dan fitur *reorderable*.
* **Audit Trail:** Menambahkan metadata *backup* (`merged_by`, `merged_at`) untuk mencatat rekam jejak admin saat menggabungkan topik, lengkap dengan fitur **Undo exact behavior**.

### Fase 4: Refactoring Antarmuka & Mesin Rekomendasi (27 Juli 2026)
Menyempurnakan antarmuka pengguna dan memastikan sistem rekomendasi bekerja di latar belakang.
* **UI Mahasiswa:** Rombak total antarmuka pencarian dan repositori ke tema **Editorial Minimalist** agar nyaman untuk membaca.
* **UI Admin:** Menambahkan dasbor statistik dan menyesuaikan skema warna *admin panel*.
* **Background Processing:** Memastikan *queue* dan mesin rekomendasi (TF-IDF + Cosine Similarity) sudah berjalan sukses secara asinkron (rekomendasi terhitung di *background* tanpa membuat web melambat).

### Fase 5: Penyempurnaan & Bug Fixing (28 Juli 2026)
* **28 Juli 2026 (Perbaikan):** Resolusi konflik sinkronisasi pada fitur *auto-tagging* di form Filament. Memindahkan logika eksekusi agar *tag* yang otomatis di-*generate* bisa tersimpan sempurna tanpa *error*.

---

## 3. Status Fitur Inti (Current State)
Saat ini, proyek berada dalam posisi stabil dengan fitur inti yang sudah beroperasi penuh:
- [x] Repositori publikasi (artikel, skripsi, buku).
- [x] Manajemen berkas (Public & Restricted PDF).
- [x] Autentikasi Mahasiswa & Admin terpisah.
- [x] Pencarian metadata & abstraksi yang aman dari eksploitasi HTML.
- [x] Rekomendasi *Content-based* (TF-IDF + Cosine Similarity) menggunakan pemrosesan *Background Queue*.

---

## 4. Langkah Selanjutnya (Next Target)
- **Modifikasi Taksonomi Parent-Child:** Memodifikasi model `Topic` dan `AutoTaggingService` untuk mendukung hierarki *knowledge-based* (misal: pengelompokan topik turunan di bawah topik induk).
- **Pengayaan Metadata:** Menambahkan isian wajib seperti DOI/URL sumber, bidang studi, dan metode penelitian pada form publikasi.

## 29 JULI 2026
PROGRES SEJAUH INI:

Menganalisis kodinganmu dibandingkan dengan *roadmap* awal, progres pengerjaan repositori ini sangat impresif. Banyak fitur krusial yang sudah berhasil dieksekusi dengan *clean code*, meskipun ada beberapa penyesuaian cerdas di tengah jalan.

Berikut adalah rincian status pengembangan webmu saat ini:

### ✅ Yang Sudah Selesai & Berjalan Baik

**1. Knowledge-Based Recommendation (Fase 2) - *Excellent Progress***
Ini adalah pencapaian terbesarmu sejauh ini. Fitur rekomendasi yang awalnya hanya konsep di *roadmap* sudah sepenuhnya terwujud di `PublicationController@show`:

* **Taxonomy Topik:** Sistem hierarki dengan model *Parent-Child* sudah jalan (berkat `parent_id` di tabel `topics`). `TopicSeeder` juga sudah merepresentasikan hierarki yang rapi (Ilmu Komputer > Sistem Informasi > Sistem Rekomendasi).
* **5 Kategori Rekomendasi:** Kamu sudah berhasil mengelompokkan rekomendasi menjadi 5 bagian persis seperti rencana:
* *Dokumen paling mirip* (`similarRecommendations` via TF-IDF).
* *Bacaan pelengkap* (`complementaryRecommendations` via *knowledge overlap*).
* *Konsep dasar* (`basicConcepts` ditarik dari `parent_id`).
* *Metode/Tipe serupa* (`similarMethods`).
* *Bacaan lanjutan* (`advancedReadings` ditarik dari `child_id`).



**2. Rapikan Jenis Publikasi (Fase 1)**
Jenis karya ilmiah sudah dikunci menggunakan *Enum* pada database (`thesis`, `scientific_paper`, `article`, `book`, `proceeding`, `report`). Validasi otomatis per bagian file (seperti Cover, Bab I, Daftar Pustaka) juga sudah berjalan lewat unit test `PublicationRequiredSectionsTest`.

**3. Fitur Bookmark Dasar (Fase 1)**
Sistem *bookmark* untuk menyimpan daftar bacaan (`BookmarkController` dan `bookmarks.index`) sudah berfungsi penuh dan terintegrasi dengan preferensi topik *user*.

---

### ⏳ Yang Belum Dikerjakan

**1. Metadata Ekstra (Fase 1)**
Tabel `publications` saat ini baru menampung metadata dasar (Tahun, Abstrak, Penulis, *Keywords*, Kontainer). Kolom khusus untuk **Bahasa**, **Metode Penelitian** (secara spesifik), **Lisensi**, dan **DOI/URL Sumber** belum ditambahkan ke file migrasi database.

**2. Tag Pribadi pada Bookmark (Fase 1)**
Fungsi untuk menambahkan catatan/tag personal saat menyimpan *bookmark* (seperti melabeli: "teori", "metode", "pembanding") belum ada. Saat ini *user* hanya bisa mem-favoritkan publikasi tanpa memberi *notes* tambahan.

**3. Tahap Evaluasi Skripsi (Fase 4)**
Pengujian fungsionalitas algoritma (*Precision@3*) dan pengujian kepuasan pengguna (*System Usability Scale* / SUS) belum dilakukan karena membutuhkan data publikasi yang lebih masif dan responden mahasiswa/dosen yang nyata.

**4. Skala Jangka Panjang (Fase 5)**
Rencana untuk menambahkan *feedback loop* (tombol relevan/tidak relevan), *Collaborative Filtering*, dan *Chat AI* berbasis dokumen internal belum diimplementasikan.

---

### 🔄 Yang Berbeda dari Rencana Awal (Pivot Cerdas)

**1. Pendekatan Semantic Search (Fase 3)**
Di *roadmap*, kamu merencanakan *semantic search/embedding*. Pada implementasinya (`PublicationController@expandSearchTerms`), kamu tidak menggunakan *Machine Learning Vector Embeddings* (seperti OpenAI atau Pinecone) yang berat. Sebagai gantinya, kamu menggunakan teknik **Query Expansion** melalui kamus sinonim di `config/topic_dictionary.php` dan menarik hierarki taksonomi.
*Keuntungan:* Ini adalah pivot yang sangat cemerlang untuk level skripsi. Sistem tetap bisa mengenali bahwa "AI" dan "Kecerdasan Buatan" adalah hal yang sama tanpa membebani *server* kampus dengan komputasi model AI yang berat.

**2. Alasan Rekomendasi (Fase 1)**
Rencana awal adalah mengubah "skor relevansi" menjadi teks deskriptif (misal: "Topik dan kata kunci serupa"). Di kodingan saat ini, kamu belum memunculkan teks alasan spesifik per kartu dokumen, melainkan menyiasatinya dengan memecah dokumen langsung ke dalam blok-blok UI yang jelas (Blok "Konsep Dasar", Blok "Bacaan Pelengkap"). Pendekatan UI yang sekarang justru jauh lebih bersih dan tidak membuat halaman terlihat sesak dengan teks.