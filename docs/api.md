# InternHub API Documentation v1

## Base URL
`/api/v1`

## Standard Responses

### Success (200 OK)
```json
{
  "success": true,
  "message": "Operasi berhasil",
  "data": { ... },
  "meta": {
    "current_page": 1,
    "total": 100
  }
}
```

### Error (4xx/5xx)
```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "email": ["Format email tidak valid"]
  },
  "request_id": "9a1b2c3d-4e5f-6g7h"
}
```

## Key Endpoints

### Auth
- `POST /auth/login`
- `POST /auth/register`
- `GET /auth/me`

### Public
- `GET /public/internships` - List lowongan publik.
- `GET /public/internships/{slug}` - Detail lowongan.

### Candidate
- `GET /candidate/dashboard` - Dashboard kandidat/mahasiswa.
- `POST /candidate/internships/{id}/apply`

### HR
- `GET /hr/dashboard` - Dashboard HR sesuai kontrak Agent.md.
- `GET /hr/internships`

### Mentor
- `GET /mentor/dashboard` - Dashboard mentor sesuai kontrak Agent.md.

### Admin
- `GET /admin/dashboard` - Dashboard admin sesuai kontrak Agent.md.

### Compatibility Aliases
Endpoint dashboard lama masih tersedia sementara agar client lama tidak langsung rusak. Frontend baru harus memakai endpoint role-first di atas.

- `GET /dashboard/user` -> `GET /candidate/dashboard`
- `GET /dashboard/hr` -> `GET /hr/dashboard`
- `GET /dashboard/mentor` -> `GET /mentor/dashboard`
- `GET /dashboard/admin` -> `GET /admin/dashboard`

### Health
- `GET /health` - System health check.
