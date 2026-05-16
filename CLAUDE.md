# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

This is a Laravel 13 internship-management application named Simaskansa. It serves role-specific Blade pages for school admins, teachers, students, and company supervisors, while most application behavior is driven by a JWT-authenticated `/api/v1` backend.

Key stack details:
- PHP 8.3+, Laravel 13
- Blade views with Vite-built frontend assets
- Tailwind CSS 4
- JWT auth via `tymon/jwt-auth`
- OpenAPI docs via `darkaonline/l5-swagger`
- Excel import/export via `maatwebsite/excel`
- Default local/test database setup uses SQLite

## Common commands

### Initial setup
```bash
composer setup
```
This installs PHP and Node dependencies, copies `.env` if needed, generates the app key, runs migrations, and builds frontend assets.

If API authentication is needed, also generate the JWT secret:
```bash
php artisan jwt:secret
```

### Local development
```bash
composer dev
```
Runs the Laravel dev server, queue listener, log tailing with Pail, and Vite dev server concurrently.

If you only need the app server:
```bash
php artisan serve
```

If you only need the frontend dev server:
```bash
npm run dev
```

### Frontend build
```bash
npm run build
```

### Tests
```bash
composer test
```

Run all tests directly:
```bash
php artisan test
```

Run a specific test file:
```bash
php artisan test tests/Feature/ExampleTest.php
```

Run a specific test method:
```bash
php artisan test --filter=test_method_name
```

### Database and app maintenance
```bash
php artisan migrate
php artisan db:seed
php artisan optimize:clear
php artisan storage:link
```

### API documentation
Swagger UI is exposed at:
```bash
/api/documentation
```

Additional helper routes exist at:
```bash
/docs
/docs.json
```

If docs need regeneration, use the package artisan command and check the installed L5 Swagger command set:
```bash
php artisan list | grep swagger
```

## Architecture

### High-level request flow
- `routes/web.php` maps browser routes directly to Blade views. These routes are mostly thin shells for role-based pages.
- `routes/api.php` contains the real application surface area under `/api/v1`.
- `bootstrap/app.php` wires web/API routing and registers the custom `role` middleware alias.
- Frontend pages usually load, then call the API from browser-side JavaScript using the JWT stored in `localStorage`.

### Auth and roles
- API auth is JWT-based through `App\Models\User` implementing `JWTSubject`.
- Frontend auth helpers live in `resources/js/auth.js`.
  - JWT is stored in `localStorage` as `jwt_token`.
  - `Auth.apiFetch()` automatically adds `Authorization: Bearer ...` and redirects to `/login` on 401.
- Authorization is mainly enforced in `App\Http\Middleware\RoleMiddleware`.
  - `superadmin` bypasses normal role checks.
- Main roles present in the codebase are:
  - `school_admin`
  - `teacher`
  - `student`
  - `company_supervisor`
  - `superadmin`

### UI structure
- Shared authenticated layout is `resources/views/layouts/app.blade.php`.
  - Sidebar sections are shown/hidden client-side after calling `/api/v1/auth/me`.
  - Many pages depend on `window.Auth` and inline scripts rather than a large SPA framework.
- Guest/auth pages use `resources/views/layouts/guest.blade.php` and `resources/views/auth/*`.
- `resources/js/admin-utils.js` contains shared browser utilities for CRUD-style admin pages: modals, toast messages, table rendering, pagination, delete confirmation, and select population.
- Vite entry points are defined in `vite.config.js`:
  - `resources/css/app.css`
  - `resources/js/app.js`
  - `resources/js/auth.js`
  - `resources/js/admin-utils.js`

### Backend domain shape
The app models internship operations across schools, classes, companies, supervisors, and students. Important entity groups:
- Core org data: `School`, `SchoolClass`, `Company`, `User`
- Internship lifecycle: `Internship`, `DailyLog`, `DailyLogComment`, `Attendance`, `ClockInOut`, `PermissionRequest`
- Evaluation/assessment: `Evaluation`, `FinalAssessment`, `AssessmentTemplate`, `TemplateSection`, `TemplateIndicator`, `StudentAssessment`, `AssessmentScore`
- Assignment/coordination: `TeacherStudentAssignment`, `TeacherCompanyAssignment`, `AttendancePoint`, `DocumentRequirement`, `StudentDocument`

A useful mental model is that `User` is the central actor model, and many workflows branch by role instead of by separate auth tables.

### API organization
`routes/api.php` is large and role-segmented. When making backend changes, check route middleware and surrounding role-specific endpoints before editing a controller.

Major API areas include:
- Auth and identity: `/auth/*`
- Master data CRUD: schools, classes, teachers, students, companies, supervisors
- Internship operations: internships, attendance, daily logs, evaluations, final assessments
- Student self-service: attendance, evaluations, documents, daily-log comments
- Teacher workflows: student review panel, approvals, assessments, assigned companies
- Admin workflows: teacher/student/company assignments, imports/exports, recap/reporting
- Location-aware attendance: attendance points plus company geolocation fields
- Address lookup/geocoding via `AddressController`

### Data/storage notes
- Local defaults in `.env.example` use SQLite with database-backed sessions, cache, and queue.
- PHPUnit uses in-memory SQLite.
- Production guidance recommends MySQL or PostgreSQL, database-backed queues, and a persistent queue worker.
- Uploaded files are expected to be exposed through `php artisan storage:link`.

### Operational notes from project docs
- Production deploys require both `php artisan key:generate` and `php artisan jwt:secret`.
- Queue workers matter in production; the provided deployment guide uses Supervisor with `queue:work database`.
- After deployment, caches are expected to be rebuilt with `config:cache`, `route:cache`, `view:cache`, and `event:cache`.

## Working effectively in this repo

- Prefer tracing a feature from `routes/web.php` or `routes/api.php` into the controller, then the related model relationships in `app/Models`.
- For browser behavior bugs, inspect the Blade view and its inline scripts together with `resources/js/auth.js` and `resources/js/admin-utils.js`.
- For role-specific issues, verify both the frontend menu gating and the API middleware rules.
- For API/doc changes, check whether Swagger annotations in `app/` also need updating because L5 Swagger scans the `app` directory.
- There are currently only placeholder example tests in `tests/`; do not assume meaningful coverage exists.
