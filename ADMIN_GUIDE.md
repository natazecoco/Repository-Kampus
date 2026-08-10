# Panduan Admin Repository

Panduan ini menjelaskan pekerjaan admin yang paling penting: mengelola publikasi, kamus topik, kamus metode riset, dan proses scan ulang.

## 1. Mengelola Publikasi

Buka menu **Repository > Publikasi** di panel `/admin`.

### Pengisian metadata

- **Container**: pilih universitas, jurnal, atau penerbit tempat dokumen berada.
- **Jenis publikasi**: pilih jenis dokumen yang sesuai. Pilihan ini disimpan pada kolom `type` dan menentukan template berkas.
- **Judul, penulis, tahun**: isi sesuai dokumen sumber.
- **Abstrak dan kata kunci**: isi selengkap mungkin karena dipakai untuk pencarian, auto-tagging, dan deteksi metode.
- **Metode riset**: sistem mengisi otomatis jika menemukan istilah dari kamus. Admin tetap dapat mengoreksi nilai ini secara manual.

### Kelengkapan berkas

Status **Lengkap** berarti bagian wajib sesuai jenis publikasi sudah tersedia. Jika status **Perlu Dilengkapi**, buka publikasi dan tambahkan bagian yang ditunjukkan oleh pemeriksaan template.

## 2. Kamus Topik dan Kamus Metode

Keduanya berbeda:

- **Kamus Topik** menghubungkan kata kunci ke topik repository dan dipakai oleh `AutoTaggingService`.
- **Kamus Metode Riset** menghubungkan istilah publikasi ke label metode dan dipakai oleh `ResearchMethodDetector`.

Kamus topik tersedia pada **Repository > Kamus Topik**. Kamus metode tersedia pada **Repository > Kamus Metode Riset**.

### Cara kerja pembaruan topik

Saat publikasi dibuat atau diedit, sistem membaca tiga sumber teks:

1. Judul.
2. Kata kunci.
3. Abstrak.

Bobot pencocokannya adalah judul `10`, keyword `7`, dan abstrak `3`. Jika skor topik mencapai ambang konfigurasi, sistem menambahkan topik tersebut beserta parent taxonomy-nya.

Untuk menerapkan perubahan kamus topik ke publikasi lama, jalankan:

```bash
php artisan repo:scan-ulang
```

Perintah ini memperbarui topik otomatis. Topik yang dipilih manual oleh admin tetap dipertahankan, sedangkan topik otomatis lama yang tidak lagi cocok dapat dilepas.

## 3. Cara Mengisi Kamus Metode

### Nama metode

Isi label baku yang ingin disimpan ke publikasi.

Contoh:

```text
Rapid Application Development (RAD)
```

### Alias

Alias adalah variasi istilah yang mungkin muncul pada judul, keyword, atau abstrak. Satu metode dapat memiliki beberapa alias.

Contoh RAD:

```text
RAD
rapid application development
metode RAD
metode rapid application development
```

Gunakan istilah yang spesifik. Jangan memakai alias tunggal seperti `data`, `sistem`, `metode`, atau `website`, karena kata tersebut terlalu umum dan dapat menghasilkan false positive.

### Kategori

Kategori membantu admin memahami peran istilah:

- **Pendekatan penelitian**: Kualitatif, Kuantitatif, Fenomenologi.
- **Pengembangan sistem**: RAD, Waterfall, Agile, Prototyping.
- **Pengujian**: UAT, Black Box Testing, SUS, UEQ.
- **Analisis / keputusan**: AHP, TOPSIS, Regresi, Min-Max.
- **Teknologi pendukung**: Machine Learning, CNN, Laravel, IoT.

Kategori adalah informasi pengelompokan. Penentuan hasil deteksi terutama dipengaruhi oleh alias, lokasi kemunculan, dan prioritas.

### Prioritas

Prioritas adalah angka relatif, bukan persentase dan bukan skor kepastian.

Rentang yang tersedia adalah `0` sampai `1000`. Rekomendasi awal:

| Peran istilah | Contoh | Prioritas |
|---|---|---:|
| Metode penelitian atau pengembangan utama | RAD, Waterfall, Kualitatif | 100 |
| Pengujian atau analisis | UAT, UEQ, AHP | 90 |
| Teknologi pendukung | Machine Learning, CNN | 40 |

Semakin tinggi angka, semakin besar peluang istilah tersebut dipilih jika beberapa istilah cocok. Nilai `99` dan `100` hanya berbeda satu poin; tidak berarti 99% dan 100%.

### Status aktif

Matikan status aktif jika istilah sering menghasilkan deteksi keliru. Data tidak perlu dihapus dan dapat diaktifkan kembali setelah diperbaiki.

## 4. Cara Kerja Deteksi Metode

Sistem membaca tiga sumber:

1. Judul publikasi.
2. Kata kunci.
3. Abstrak.

Sistem mencocokkan alias sebagai kata utuh. Bonus lokasi saat ini adalah:

- judul: `100`
- keyword: `50`
- abstrak: `20`

Skor kandidat secara sederhana:

```text
skor = prioritas + bonus lokasi kemunculan
```

Karena judul mendapat bonus paling besar, istilah metode yang disebut jelas pada judul biasanya mengalahkan istilah teknologi yang hanya muncul pada abstrak.

## 5. Scan Ulang Publikasi Lama

Setelah kamus diubah, publikasi lama belum otomatis berubah sampai dilakukan scan ulang. Jalankan dari folder project:

```bash
php artisan cache:clear
php artisan repo:scan-ulang --force-method
```

- `cache:clear` memastikan kamus terbaru dibaca.
- `repo:scan-ulang` memproses semua publikasi.
- `--force-method` mengizinkan nilai `research_method` lama ditimpa oleh hasil deteksi terbaru.

Scan juga mengirim proses pembuatan rekomendasi ke queue. Pastikan worker berjalan:

```bash
php artisan queue:work
```

## 6. Pemeriksaan Setelah Scan

Setelah scan selesai:

1. Filter publikasi berdasarkan metode di tabel admin.
2. Periksa istilah yang hasilnya terasa tidak sesuai.
3. Koreksi alias, prioritas, atau status aktif di kamus.
4. Jalankan scan ulang lagi jika perubahan kamus harus diterapkan ke data lama.

Sistem bersifat semi-otomatis: deteksi dilakukan sistem, tetapi penambahan istilah baru dan pemeriksaan hasil tetap dikendalikan admin.

## 7. Batasan Sistem

Detektor berbasis kamus dan skor, bukan pemahaman bahasa seperti manusia. Karena itu:

- istilah baru harus ditambahkan ke kamus,
- istilah yang ambigu perlu diberi prioritas hati-hati,
- hasil otomatis tetap perlu ditinjau pada data penting,
- satu publikasi hanya menyimpan satu `research_method` utama.

Topik otomatis dan metode riset tidak sama: publikasi dapat memiliki banyak topik, tetapi field metode riset menyimpan satu label utama.

## 8. Checklist Sebelum Demo atau Sidang

- Publikasi penting memiliki abstrak dan keyword.
- Metode utama sudah diperiksa.
- Alias yang terlalu umum tidak aktif.
- Scan ulang sudah dijalankan setelah perubahan kamus.
- Queue worker aktif jika rekomendasi perlu diperbarui.
- Status kelengkapan berkas sudah diperiksa.
- Admin memahami perbedaan kamus topik dan kamus metode.
