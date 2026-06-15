# Batch 19 - Activity Log & Audit Hardening

## Overview
Implementasi sistem logging berlapis (Activity, Audit, Security) dengan pengerasan keamanan pada viewer dan otomatisasi via Domain Events.

## Key Deliverables

### 1. Database Schema
- **Activity Log**: Tabel `activity_logs` untuk mencatat aktivitas operasional harian (viewing, searching, dll).
- **Audit Log**: Tabel `audit_logs` (finalized) untuk compliance (siapa mengubah apa, data lama vs baru).
- **Security Event**: Tabel `security_events` untuk insiden keamanan (risk, login failures, policy denied).

### 2. Logging Services
- `ActivityLogger`: Service untuk mencatat aktivitas operasional.
- `AuditLogger`: Service untuk mencatat perubahan data sensitif.
- `SecurityEventLogger`: Service untuk mencatat kejadian berisiko tinggi.

### 3. Event-Driven Pattern
- **Pattern**: `Action -> Domain Event -> Listener -> Activity Log -> Audit Log (jika sensitif) -> Security Event (jika risk) -> Notification`.
- Implementasi `LoggableEvent` interface untuk standarisasi logging lintas domain.
- `ProcessLog` listener otomatis menangani semua event loggable secara asynchronous (ShouldQueue).

### 4. UI/UX (Super Admin)
- **Activity Timeline**: Visualisasi aktivitas dengan timeline vertikal premium.
- **Activity Filters**: Filter berdasarkan Action Type, Search, dan Date.
- **Log Viewer**: Halaman terpusat untuk monitoring sistem.

### 5. Hardening & Security
- **Log Policies**: `LogPolicy` membatasi akses log hanya untuk Super Admin dan Admin (Activity).
- **Rate Limiting**: Throttling pada endpoint log viewer (30 requests/min).
- **Security Alerts**: Notifikasi otomatis (Mail & Database) jika terdeteksi Security Event dengan risiko tinggi.

## Testing Results
- [x] **Activity log created**: Terverifikasi via `LoggingTest`.
- [x] **Audit log created**: Perubahan data sensitif tercatat dengan data lama/baru.
- [x] **Security event created**: Trigger otomatis pada event berisiko.
- [x] **Unauthorized log access denied**: Middleware dan Policy berfungsi mencegah akses ilegal (403 Forbidden).

## Conclusion
Sistem audit siap untuk production compliance dengan hardening yang memastikan integritas log dan keamanan akses.
