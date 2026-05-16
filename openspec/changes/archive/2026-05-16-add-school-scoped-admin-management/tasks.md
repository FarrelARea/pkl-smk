## 1. Data model and access rules

- [x] 1.1 Audit admin-managed entities and identify which tables need a direct `school_id` column versus school scoping through existing relations.
- [x] 1.2 Add the required migrations/backfill logic so school-scoped records and school admin accounts carry consistent school context.
- [x] 1.3 Update seeders or fixtures so a superadmin can create or test school admin accounts with valid school assignments.
- [x] 1.4 Add or update authorization helpers/middleware rules that distinguish global superadmin access from school-scoped admin access.

## 2. School admin management

- [x] 2.1 Add superadmin-only routes, controller logic, and API endpoints for listing, creating, updating, and deleting school admin accounts.
- [x] 2.2 Build the `/admin` management page or section that lets superadmin manage school admin accounts and assign one school per account.
- [x] 2.3 Prevent non-superadmin users from opening or calling school admin management UI and endpoints.

## 3. Scoped admin behavior across existing modules

- [x] 3.1 Update admin-facing queries and endpoints for schools, teachers, students, classes, and related operational data to enforce school scope for school admins.
- [x] 3.2 Ensure create and update flows automatically use the logged-in school admin’s assigned school and reject cross-school payloads.
- [x] 3.3 Remove or hide school-management actions that school admins must not use, while preserving full functionality for superadmin.

## 4. Login and verification

- [x] 4.1 Keep `/login/admin` working for both superadmin and school admin accounts without changing non-admin login flows.
- [x] 4.2 Verify dashboard/navigation behavior so superadmin sees global admin capabilities and school admins only see scoped capabilities.
- [x] 4.3 Add focused tests or manual verification coverage for school-admin creation, login, scoped reads/writes, and blocked school-management actions.
