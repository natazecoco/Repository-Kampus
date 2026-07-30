# Development Log & Roadmap Proyek Skripsi

**Proyek:** Sistem Repositori Ilmiah & Rekomendasi Literatur
**Stack:** Laravel, FilamentPHP, React
**Metode Inti:** Information Retrieval, NLP (Sastrawi), TF-IDF + Cosine Similarity, Knowledge-Based (Taxonomy)

## 📌 Arsitektur & Posisi Sistem Saat Ini
Sistem ini dirancang bukan sekadar sebagai tempat penyimpanan digital, melainkan **Sistem Rekomendasi Literatur berbasis Konten (Content-based)** dan **Pengetahuan Domain (Knowledge-based)**. 

Metode **TF-IDF dan Cosine Similarity** dipilih sebagai *baseline* yang solid dan transparan. Pendekatan ini secara sadar dipilih untuk mengatasi *cold-start problem*, mengingat sistem baru belum memiliki data riwayat interaksi pengguna yang cukup untuk menjalankan *Collaborative Filtering*.

---

## ✅ Yang Sudah Diselesaikan (Changelog)

### Fondasi & Konfigurasi Lingkungan
* Memperbaiki fatal error Filament 5 dan membatasi akses panel hanya untuk admin.
* Menyamakan *role* akun (admin & student) dengan alur login terpisah secara aman.
* Memperbaiki konfigurasi Vite (tanpa ketergantungan Bunny Fonts) dan penyesuaian *environment* (Node.js 24, PHP 8.5 Laragon).
* Normalisasi keamanan, termasuk sanitasi output *highlight* abstrak agar kebal dari injeksi HTML berbahaya.

### Fitur Inti Repositori
* CRUD Publikasi terstruktur (jurnal, skripsi, buku) dengan dukungan visibilitas *file public* dan *restricted*.
* Integrasi antarmuka pencarian metadata dengan autentikasi yang stabil.
* **Rekomendasi Content-based:** Implementasi algoritma TF-IDF + Cosine Similarity yang diproses melalui *background queue* agar performa UI pengguna tetap cepat dan responsif.

### Arsitektur Taksonomi & Manajemen Topik
* Migrasi database untuk tabel `topics` dan *pivot table* publikasi.
* **Fitur Kurasi Admin:** Membangun fitur *bulk merge topic*, *toggle active*, *sorting*, dan pencatatan *audit trail* (`merged_by`, `merged_at`) untuk mendukung fitur **Undo**.
* Menerapkan `TopicPolicy` untuk menjaga integritas keamanan taksonomi.
* Memperbaiki konflik sinkronisasi *auto-tagging* di dalam *lifecycle hooks* form Filament.
* Merombak UI halaman mahasiswa menjadi tema *Editorial Minimalist* yang fokus pada kenyamanan membaca.

---

## 🚀 Roadmap Pengembangan Lanjutan

### Fase 1: Perkuat Fondasi Saat Ini (Sedang Berjalan)
* Merapikan jenis publikasi (artikel jurnal, skripsi, tesis, buku, bab buku, prosiding).
* Menambahkan metadata krusial: DOI/URL sumber, bidang, bahasa, volume, metode penelitian, dan lisensi.
* Mengubah tampilan "skor relevansi" menjadi metrik yang *user-friendly* (contoh: label "Topik dan kata kunci serupa").
* Menambahkan fitur *bookmark* (daftar bacaan pribadi) dan *tagging* kustom (teori, metode, pembanding).

### Fase 2: Implementasi Knowledge-Based Recommendation (Target Terdekat)
* Membangun taksonomi topik secara hierarkis (Parent-Child) agar sistem mengenali struktur ilmu.
* Menghubungkan relasi antar konsep (contoh: Web Dev -> Framework -> Laravel).
* Membagi output rekomendasi per kategori kontekstual: 
  - Dokumen paling mirip
  - Konsep dasar
  - Bacaan pelengkap
  - Metode serupa

### Fase 3: Peningkatan Pemahaman Makna (Semantic)
* Penambahan fitur *semantic search / embedding* multibahasa.
* Menggabungkan hasil *embedding* dengan TF-IDF agar sistem memahami konteks istilah bersinonim (contoh: "kesehatan mental" ≈ "kesehatan psikologis").

### Fase 4: Evaluasi Skripsi (Validasi Metodologi)
* **Pengujian Fungsional:** *Black-box testing* untuk seluruh alur utama sistem.
* **Kualitas Rekomendasi:** Evaluasi metrik *Precision@3* untuk mengukur akurasi dari *top 3* rekomendasi dokumen.
* **Usability Testing:** Evaluasi menggunakan tugas pencarian spesifik oleh mahasiswa yang diukur dengan kuesioner SUS (*System Usability Scale*).
* Komparasi hasil antara pencarian teks biasa vs pencarian yang dibantu sistem rekomendasi.

### Fase 5: Future Development (Pasca-Skripsi / Big Data)
* Implementasi *feedback* eksplisit "relevan/tidak relevan" dari pengguna.
* Transisi ke algoritma *Collaborative Filtering* (jika *volume* interaksi dan histori pengguna sudah memadai).
* Integrasi *Chat AI* (LLM) berbasis dokumen internal repositori, dengan sumber dan sitasi yang selalu dapat diverifikasi.