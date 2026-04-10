# Coding Style and Conventions

## Formatting and standards
- Formatter: Laravel Pint (`pint.json`), preset `psr12`.
- Additional enforced rules include:
  - ordered imports
  - remove unused imports
  - short array syntax
  - single quotes
  - trailing comma in multiline structures
  - single spaces around binary operators
- PHPCS config (`.phpcs.xml`) also references PSR-12.

## PHP/Laravel style patterns seen in codebase
- Strong use of typed properties and typed method signatures.
- Naming style tends to use snake_case in some internal variable/property names (e.g., `$task_service`, `$user_id`) while class names and methods remain PSR-style.
- Domain and UseCase layers are intentionally framework-agnostic where possible.
- UseCases validate `Input` type and return `Result::success` / `Result::failure` consistently.
- Request validation classes customize authorization/validation failure responses in JSON.
- Repository interfaces live in `Domain`, concrete Eloquent repositories live in `Infrastructure`.

## Documentation/comments
- PHPDoc comments are used broadly for method intent and params.
- Missing class/file doc comments are tolerated by PHPCS config.
