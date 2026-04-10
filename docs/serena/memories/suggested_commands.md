# Suggested Commands (Darwin/macOS)

## Setup
- `composer install`
- `cp .env.example .env`
- `php artisan key:generate`
- `php artisan migrate`

## Run app/dev environment
- `php artisan serve`
- `npm run dev`
- `composer run dev`  (runs server, queue listener, logs, and Vite concurrently)

## Tests
- `php artisan test`
- `php artisan test --testsuite=Unit`
- `php artisan test --testsuite=Integration`
- `php artisan test --testsuite=Feature`
- `composer test`
- `composer test:unit`
- `composer test:integration`
- `composer test:feature`

## Lint/format
- `./vendor/bin/pint`
- `./vendor/bin/pint --test`  (CI-style format check)
- `./vendor/bin/phpcs --standard=.phpcs.xml`

## AI suggestion batch
- `php artisan tasks:suggest`
- `php artisan tasks:suggest --user-id=1`

## Useful macOS terminal utilities
- `ls`, `cd`, `pwd`, `cat`, `sed`, `find`, `grep`, `rg`, `git status`, `git diff`
