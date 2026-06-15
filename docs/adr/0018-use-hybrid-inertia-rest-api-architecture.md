# 0018 Use Hybrid Inertia and REST API Architecture

## Status

Accepted

## Context

`Agent.md` defines InternHub as a full web service with a hybrid frontend/backend architecture:

- Laravel backend in `backend/`.
- Vue 3 + Inertia + TypeScript + Tailwind frontend in `frontend/`.
- REST API under `/api/v1` for service layer, integrations, dashboard async actions, upload, search, notification, reporting, health checks, mobile-ready endpoints, and future external clients.

The codebase currently contains both Vue Router SPA-style pages and Inertia-oriented pages. That mix is a source of bugs when pages assume different navigation, props, and data-loading contracts.

## Decision

InternHub follows the architecture in `Agent.md`: hybrid Inertia + Vue for the main web experience, with REST API `/api/v1` for service and asynchronous workflows.

Frontend rules:

- Main web pages should be treated as Inertia/Vue pages.
- REST API calls are valid for async dashboard data, search, upload, notification, reporting, and external/mobile-ready flows.
- New work must not move the project toward an API-only SPA architecture.
- Existing Vue Router usage is legacy compatibility during migration, not the target architecture.
- Inertia page props should be used for initial page context where the route is a web page.
- API responses should use the standard `/api/v1` response format for service endpoints.

Backend rules:

- `routes/web.php` serves web pages through Inertia controllers/responses.
- `routes/api.php` serves `/api/v1` endpoints.
- Business logic stays in Action/Service/DTO layers, with thin controllers.
- Sensitive endpoints require Form Request validation, Policy/Gate authorization, and activity/audit/security logging where relevant.

## Consequences

Architecture cleanup should migrate pages toward the documented hybrid model instead of continuing a full SPA direction.

Priority cleanup areas:

- Replace page-level Vue Router-only contracts with Inertia web routes where appropriate.
- Keep `/api/v1` for dashboard async widgets and service actions.
- Remove or isolate legacy code that assumes `@inertiajs/vue3` is available inside pure Vue Router-only mounts.
- Keep frontend build passing after each migration step.
