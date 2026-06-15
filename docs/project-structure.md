# Project Structure - InternHub

## 1. Root Directory Layout
```text
internhub/
├── backend/            # Laravel 13 Framework
├── frontend/           # Vue 3 + Vite + Tailwind 4 Workspace
├── infrastructure/     # Docker, Nginx, Server Configs
├── docs/               # Documentation & Batch Reports
├── scripts/            # Helper & Setup Scripts
├── .agents/            # Agent Rules & AI Context
├── PRD.md              # Product Requirement Document
├── Architecture.md     # Technical Architecture
└── README.md           # Installation Guide
```

## 2. Backend Structure (`backend/`)
- `app/Http/Controllers/`: Thin controllers.
- `app/Actions/`: Business logic single-responsibility classes.
- `app/Services/`: Complex business logic.
- `database/migrations/`: Skema database `internhub_rekrutmen_2026`.
- `resources/views/app.blade.php`: Entry point Inertia.

## 3. Frontend Structure (`frontend/`)
- `resources/js/Pages/`: Vue components sebagai halaman Inertia.
- `resources/js/Components/`: Reusable UI components (Design System).
- `resources/js/Layouts/`: Base layouts (Auth, Dashboard, Public).

## 4. Disiplin Eksekusi
- **Artisan**: Wajib dijalankan dari folder `backend/`.
- **NPM**: Wajib dijalankan dari folder `frontend/`.
- **Docker**: Wajib dijalankan dari `infrastructure/docker/`.
