# Task Completion Checklist

When finishing code changes in this project:

1. Run formatter checks/fix:
- `./vendor/bin/pint` (or `./vendor/bin/pint --test` for check-only)

2. Run relevant test suites:
- Minimum: `php artisan test --testsuite=Unit` for logic-layer changes
- Add `Integration` and/or `Feature` suites when touching services/controllers/routes/db behavior
- Full regression when needed: `php artisan test`

3. Confirm route/auth impact if API-related changes were made:
- Verify Sanctum-protected routes still behave correctly
- Verify request validation JSON error format remains consistent

4. If AI suggestion logic changed:
- Validate `php artisan tasks:suggest` behavior (optionally with `--user-id`)
- Ensure invalid/empty model output is handled safely

5. Keep architecture boundaries intact:
- Domain stays framework-independent
- UseCase layer avoids HTTP/framework coupling
- Infra implementations remain behind repository interfaces

6. Before PR/merge, mirror CI expectations:
- `./vendor/bin/pint --test`
- `php artisan test` must pass before PR/merge
