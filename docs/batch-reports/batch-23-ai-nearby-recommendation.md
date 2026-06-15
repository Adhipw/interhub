# Batch 23 - AI Nearby Recommendation

## Overview
Integrasi kecerdasan buatan untuk memberikan rekomendasi lowongan magang berdasarkan konteks geografis dan preferensi pengguna.

## Key Deliverables

### 1. AI Nearby Assistant
- **Contextual Recommendation**: AI dapat menyarankan lowongan terbaik dalam radius tertentu dengan mempertimbangkan relevansi skill dan jarak tempuh.
- **Natural Language Query**: Pengguna dapat bertanya seperti "Cari magang IT yang paling dekat dari sini" atau "Magang apa yang ada di radius 5km?".

### 2. AI Safety Rules (Location)
- **Precise Location Protection**: AI dilarang menyebutkan koordinat presisi pengguna dalam responnya.
- **Privacy Awareness**: AI hanya mendapatkan akses ke koordinat yang telah diberi persetujuan (consent) oleh pengguna.
- **Anti-Discrimination**: AI diinstruksikan untuk tidak menggunakan data lokasi sebagai alat diskriminasi (misal: memprioritaskan hanya dari daerah elit).

### 3. Frontend AI Widget
- **Nearby Sidebar**: Widget khusus di halaman pencarian yang memberikan insight cepat tentang lowongan terdekat.
- **Location Context Toggle**: Pengguna dapat mengaktifkan/menonaktifkan penggunaan lokasi sebagai konteks percakapan AI.

### 4. Logging & Analytics
- **AI Usage Log**: Setiap permintaan rekomendasi nearby dicatat untuk analisis performa AI, dengan tetap menjaga privasi data lokasi.

## AI Policy
- **Allowed Data**: Jurusan, Skill, Radius, Work Mode, Koordinat (dengan consent).
- **Forbidden Actions**: Menyimpan lokasi presisi secara permanen di log, membuat lamaran otomatis, mengambil keputusan final tanpa HR.

## Testing Verification
- [x] **Consent requirement**: AI menolak memberikan rekomendasi jika izin lokasi tidak diberikan.
- [x] **Safety guard**: Verifikasi AI tidak membocorkan koordinat mentah dalam chat.
- [x] **Usage log**: Data log tersimpan dengan format konteks lokasi yang dibulatkan.

## Conclusion
Fitur ini menggabungkan kemudahan pencarian berbasis lokasi dengan kecerdasan AI untuk memberikan saran karir yang paling relevan secara spasial.
