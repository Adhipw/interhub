# Batch 18 - Advanced Recruitment Pipeline

## Overview
Implementasi sistem pipeline rekrutmen yang fleksibel (customizable) dengan visualisasi Kanban, pelacakan histori stage, dan sistem penilaian kandidat berbasis AI yang adil (Fairness-First).

## Key Deliverables

### 1. Custom Recruitment Pipeline
- **Flexible Stages**: Setiap lowongan magang (`Internship`) dapat memiliki tahapan rekrutmen yang berbeda-beda.
- **Stage Types**: Mendukung tipe stage standar seperti `initial`, `interview`, `test`, `hired`, dan `rejected`.
- **Order Management**: Urutan tahapan dapat diatur secara dinamis untuk menyesuaikan workflow masing-masing perusahaan.

### 2. Kanban HR Pipeline
- **Visual Interface**: API endpoint `getKanbanData` menyediakan struktur data untuk tampilan Kanban per internship.
- **Drag & Drop Ready**: Sistem mendukung perpindahan aplikasi antar stage secara instan melalui `updateStage`.
- **Application Context**: Setiap kartu di Kanban menampilkan skor kandidat dan detail dasar untuk mempermudah screening cepat.

### 3. Stage Transition & History
- **Audit Trail**: Setiap perpindahan stage dicatat dalam `application_stage_histories`.
- **Stage Notes**: HR dapat menambahkan catatan (notes) saat memindahkan kandidat ke stage baru.
- **SLA Tracking**: Sistem menghitung durasi kandidat berada di suatu stage (dalam menit) untuk memantau efisiensi rekrutmen.

### 4. Screening Rubric & Scoring
- **Dynamic Rubric**: HR dapat menentukan kriteria penilaian (name, weight, description) yang unik untuk setiap internship.
- **AI-Assisted Scoring**: AI memberikan skor (0-100) berdasarkan rubrik, data profil, dan cover letter.
- **Fairness Guard**: Prompt AI dirancang khusus untuk mengabaikan faktor diskriminatif (gender, usia, ras, agama).
- **Transparency**: Output AI mencakup "Factors Used" dan "Factors Ignored" untuk transparansi audit.

### 5. Human-in-the-Loop
- **AI as Advisor**: Skor AI hanya bersifat saran (`is_ai_suggested`).
- **HR Review Mandatory**: Status `human_reviewed` memastikan setiap skor telah diverifikasi atau dikoreksi oleh HR sebelum menjadi final.

## Technical Details

### Models & Database
- `RecruitmentStage`: Menyimpan definisi tahapan rekrutmen.
- `ApplicationStageHistory`: Menyimpan log perpindahan stage.
- `ScreeningRubric`: Menyimpan kriteria penilaian per lowongan.
- `ApplicationScore`: Menyimpan skor akhir (AI + Human Review).

### Access Control
- Pengecekan `company_scope` memastikan HR hanya dapat memindahkan kandidat pada lowongan milik perusahaannya.
- Integrasi dengan `AuditService` untuk mencatat aktivitas sensitif.

## Testing Verification
- [x] Custom pipeline creation per internship.
- [x] Kanban data fetching with application scores.
- [x] Stage transition with history recording.
- [x] AI Scoring with fairness guard output.
- [x] Manual HR review of AI scores.

## Conclusion
Batch 18 memberikan fondasi yang kuat bagi HR untuk mengelola ribuan kandidat secara efisien dengan bantuan AI, namun tetap mempertahankan kontrol manusia dan standar keadilan yang tinggi.
