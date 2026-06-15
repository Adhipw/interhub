# Roles & Permissions Matrix - InternHub

## 1. Role Utama
| Role | Deskripsi | Izin Utama |
|---|---|---|
| **USER** | Mahasiswa / Kandidat | Cari lowongan, melamar, absensi, profil. |
| **HR** | Perekrut Perusahaan | Posting lowongan, review kandidat, pipeline. |
| **MENTOR** | Pembimbing Lapangan | Review tugas, feedback harian, evaluasi akhir. |
| **ADMIN** | Moderator Sistem | Moderasi konten, moderasi user dasar. |
| **SUPER ADMIN** | Administrator Penuh | Akses audit log, kelola role, integrasi sistem. |

## 2. Company Scoping
- User dengan role **HR** atau **MENTOR** terikat pada `company_id` tertentu.
- HR tidak dapat melihat lamaran dari perusahaan lain (Strict Scoping).

## 3. Implementasi
- Menggunakan **Spatie Laravel Permission** untuk manajemen permission berbasis database.
- Otorisasi di tingkat kode menggunakan **Laravel Policy** untuk setiap model.
