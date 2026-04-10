# Architecture and Structure

## Important directories
- `app/Http/Controllers`: API endpoints
- `app/Http/Requests`: request validation/authorization
- `app/Http/Resources`: response shaping
- `app/UseCases`: application orchestration (Input/Output/Result)
- `app/Services`: business logic
- `app/Domain/Entities`: framework-independent domain entities
- `app/Domain/Repositories`: repository interfaces
- `app/Infrastructure/Repositories`: Eloquent implementations of repository interfaces
- `app/Console/Commands`: artisan batch jobs (notably AI suggestion generation)
- `routes/api.php`: auth/user/tasks/task-suggestions API routes
- `tests/Unit`, `tests/Integration`, `tests/Feature`: 3-layer test organization

## Route summary
- `POST /api/auth/login`
- `GET/PUT /api/user` (Sanctum required)
- `GET/POST/PUT/DELETE/PATCH /api/tasks...` (Sanctum required)
- `GET/DELETE /api/task-suggestions...` (Sanctum required)

## Batch/entry command
- `php artisan tasks:suggest [--user-id=ID]`
  - Generates AI suggestions from recent tasks, optionally for one user.
