# InternHub Web Service Architecture

InternHub follows the architecture defined in `Agent.md`: a hybrid Inertia + Vue web application backed by a Laravel REST API service layer.

## Runtime Layout

- `backend/`: Laravel application, web routes, API routes, controllers, services, actions, jobs, notifications, policies, requests, resources, and persistence.
- `frontend/`: Vue 3, Inertia-oriented pages/components, TypeScript, Tailwind CSS, and frontend tooling.
- `infrastructure/`: Docker, Nginx, PostgreSQL, Redis, Supervisor, and deployment/runtime configuration.
- `docs/`: architecture, API, batch reports, operational docs, and ADRs.
- `scripts/`: helper scripts.

## Web And API Boundary

Web pages use Inertia + Vue as the primary website experience. Laravel owns web routing and initial page context through `routes/web.php`.

REST endpoints under `/api/v1` are used for:

- Dashboard async data and actions.
- Search and filtering.
- Uploads and document workflows.
- Notifications and realtime-supporting actions.
- Reporting and health checks.
- Mobile-ready endpoints and future external clients.

The target architecture is not an API-only SPA. Vue Router usage that exists today is compatibility debt and should be migrated carefully toward the documented hybrid model.

## Backend Flow

Backend features should follow this flow:

`Route -> Controller -> Form Request -> Policy/Gate -> Action/Service -> DTO -> Model -> Event/Job/Notification`

Controllers should stay thin. Business rules belong in Actions, Services, DTOs, Jobs, Listeners, Policies, QueryFilters, or domain services.

## API Contract

All service endpoints should use the `/api/v1` prefix and standard response shape.

Success:

```json
{
  "success": true,
  "message": "Berhasil",
  "data": {},
  "meta": {}
}
```

Error:

```json
{
  "success": false,
  "message": "Terjadi kesalahan",
  "errors": {},
  "request_id": "..."
}
```

## Migration Rule

When fixing mixed frontend bugs, do not push the project further into full Vue Router SPA. Prefer:

- Inertia for page ownership and initial page context.
- `/api/v1` for async service interactions.
- Incremental migration with typecheck and production build after each step.
