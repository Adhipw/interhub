# AI Module & Assistant - InternHub

## 1. Lingkup AI
- **Smart Discovery**: Membantu mahasiswa mencari lowongan magang berdasarkan skill dan lokasi.
- **Candidate Screening Assistant**: Membantu HR menganalisis kecocokan kandidat (Hanya sebagai pemberi rekomendasi, bukan pengambil keputusan).

## 2. Prinsip "Human in the Loop"
- AI tidak boleh menolak lamaran secara otomatis.
- Skor kecocokan AI harus disertai penjelasan transparan bagi HR.
- User memiliki hak untuk diberitahu bahwa mereka sedang berinteraksi dengan AI.

## 3. Implementasi Teknik
- Menggunakan **Gemini API** (atau Local AI jika tersedia) melalui lapisan abstraksi Service di Laravel.
- Data sensitif tidak dikirim ke AI tanpa anonimisasi.
