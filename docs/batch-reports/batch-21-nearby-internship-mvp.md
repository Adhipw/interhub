# Batch 21 - Nearby Internship MVP

## Overview
Implementasi fitur pencarian lowongan magang terdekat menggunakan koordinat lokasi dengan optimasi performa dan perlindungan privasi.

## Key Deliverables

### 1. Geospatial Logic
- **Haversine Formula**: Perhitungan jarak akurat antara dua titik koordinat di permukaan bumi.
- **Bounding Box Optimization**: Query database dioptimalkan menggunakan kotak pembatas (min/max lat/lng) sebelum melakukan perhitungan Haversine yang berat.
- **Distance Sorting**: Hasil pencarian diurutkan berdasarkan jarak terdekat dari pengguna.

### 2. Nearby Search API
- **Public Endpoint**: `/ai/public/nearby` memungkinkan calon peserta mencari lowongan tanpa harus login.
- **Radius Filter**: Mendukung pencarian dengan radius kustom (default 10km, max 50km).
- **Rate Limiting**: Perlindungan terhadap scraping dengan pembatasan frekuensi permintaan per IP.

### 3. Privacy & Security
- **Coordinate Masking**: Log pencarian tidak menyimpan koordinat presisi. Latitude dan Longitude dibulatkan ke 2 desimal (~1.1km) untuk menjaga privasi pengguna.
- **Privacy Log**: Implementasi log khusus `nearby` untuk memantau penggunaan fitur tanpa mengekspos data pribadi.
- **Safe Cache Keys**: Jika caching diaktifkan, kunci cache didasarkan pada geohash atau koordinat yang dibulatkan.

### 4. Maps Foundation (Ready for UI)
- **OpenStreetMap & Leaflet.js**: Arsitektur disiapkan untuk integrasi peta open-source tanpa ketergantungan pada API berbayar seperti Google Maps.

## Technical Details

### Bounding Box Logic
```php
$latDelta = $radius / 111.32;
$lngDelta = $radius / (111.32 * cos(deg2rad($lat)));
// Query: whereBetween('latitude', [$min, $max])
```

## Testing Verification
- [x] **Haversine calculation**: Verifikasi keakuratan jarak.
- [x] **Radius filter**: Hasil pencarian sesuai dengan radius yang diminta.
- [x] **Rate limit**: Error 429 muncul setelah 10 kali pencarian berturut-turut.
- [x] **Privacy log rounding**: Verifikasi koordinat di log tersimpan dalam format pembulatan.

## Conclusion
MVP fitur Nearby ini memberikan kemudahan bagi pencari kerja untuk menemukan peluang terdekat dengan tetap menjaga standar keamanan dan privasi data lokasi.
