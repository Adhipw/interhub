# InternHub 2026 - Architecture Design

## 1. Technology Stack
- **Backend:** Laravel 13, PHP 8.3/8.4.
- **Frontend:** Vue 3, Inertia.js, TypeScript, Tailwind CSS 4.
- **Database:** PostgreSQL (Primary), Redis (Cache & Queue).
- **Messaging:** Laravel Reverb (WebSockets).
- **Storage:** Cloudflare R2 (Production), MinIO (Dev).
- **Mailing:** Resend API.
- **Testing:** Pest.

## 2. Project Structure
```text
internhub/
├── backend/            # Laravel Application
├── frontend/           # Vue 3 / Inertia Application
├── infrastructure/     # Docker, Nginx, Configs
├── docs/               # API Docs, PRD, Guides
└── scripts/            # Automation Scripts
```

## 3. Backend Architecture Pattern
- **Service Layer Pattern:** Logic resides in `app/Services`.
- **Action Pattern:** Single-purpose classes in `app/Actions`.
- **DTO:** Data Transfer Objects for structured data flow.
- **API Resources:** Standardized JSON responses.
- **Policies:** Centralized authorization.

## 4. API Standards
- **Version:** `/api/v1`
- **Response Format:**
  - Success: `{ success: true, message: "...", data: {}, meta: {} }`
  - Error: `{ success: false, message: "...", errors: {}, request_id: "..." }`

## 5. Security Strategy
- **Sanctum:** Session-based for Web, Token-based for API.
- **Audit Logs:** Log every mutation on sensitive models.
- **Security Events:** Tracking failed logins, role changes, and unauthorized access.
- **Rate Limiting:** Throttle key endpoints (Auth, Search).

## 6. Infrastructure (Docker)
- `app`: PHP-FPM 8.3.
- `web`: Nginx.
- `db`: PostgreSQL 16.
- `redis`: Redis 7.
- `worker`: Laravel Queue Worker.
- `scheduler`: Laravel Cron.
- `reverb`: Reverb Server.
```
