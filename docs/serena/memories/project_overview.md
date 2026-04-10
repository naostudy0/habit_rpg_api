# Habit RPG API - Project Overview

- Purpose: Backend API for a habit/task management app with RPG flavor. AI suggests realistic habits/tasks from users' past task history.
- Nature: Personal portfolio project focused on practical AI-driven backend development.
- Core architecture: Layered architecture with clear flow `Controller -> UseCase -> Service -> Repository Interface -> Infrastructure(Eloquent) -> DB`.
- Key domains:
  - Auth (Sanctum token authentication)
  - Tasks CRUD + completion toggle
  - AI task suggestions (saved to `task_suggestions`)
  - User profile show/update
- AI integration: Uses Ollama endpoint from backend (`AISuggestionService`) and batch command `tasks:suggest`.
- Tech stack:
  - PHP 8.2+
  - Laravel 12
  - Laravel Sanctum
  - MySQL 8.0
  - PHPUnit 11
  - Laravel Pint (PSR-12 based)
  - Vite + Tailwind (minimal frontend asset setup)
- Runtime/development environment: Darwin (macOS), typically container-backed in this project ecosystem.
