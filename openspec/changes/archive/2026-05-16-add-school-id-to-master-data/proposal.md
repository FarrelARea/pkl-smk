## Why

School-scoped administration is now a core access pattern in the application, but much of the master data still depends on indirect school relationships or has no explicit school ownership at all. Adding `school_id` consistently across school-owned master data is needed so admins can create, query, and secure records using a direct school boundary instead of ad hoc joins and assumptions.

## What Changes

- Audit all master-data and internship-support tables that should belong to a school, including examples like internships (`magang`), daily logs, and company supervisors.
- Add a `school_id` column and foreign-key relationship to every table that represents school-owned data but does not already store direct school ownership.
- Update creation flows so admin-managed records automatically persist the current admin's `school_id` instead of asking the admin to choose it manually.
- Update read, update, and listing flows so school-scoped data can be filtered and validated against the record's direct `school_id`.
- Backfill existing rows where needed so newly added `school_id` columns are populated from existing relationships before constraints are enforced.

## Capabilities

### New Capabilities
- `school-owned-master-data`: Define direct `school_id` ownership rules for school-managed master data and related internship support records.

### Modified Capabilities
- `school-scoped-admin-management`: School admin CRUD flows must automatically assign and enforce `school_id` across managed records and related lookups.

## Impact

- Affected areas include database migrations, Eloquent models, admin-facing API controllers, validation rules, seed/backfill logic, and Blade/admin JavaScript flows that currently infer school scope indirectly.
- API behavior for create/update/list endpoints will change to enforce direct school ownership.
- Existing data will require migration/backfill logic before `school_id` can be treated as required on newly scoped tables.
