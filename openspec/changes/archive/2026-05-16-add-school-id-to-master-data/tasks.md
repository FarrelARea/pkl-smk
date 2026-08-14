## 1. Audit school-owned entities

- [x] 1.1 Inventory all master-data and internship-support tables touched by school-admin workflows, including internships, daily logs, supervisors, and related reference records.
- [x] 1.2 Classify each table as already having `school_id`, derivable from existing relations, or intentionally global/shared.
- [x] 1.3 Document the authoritative backfill source for every table that needs a new `school_id`.

## 2. Add direct school ownership to schema

- [x] 2.1 Create migrations that add nullable `school_id` columns, indexes, and foreign keys to each school-owned table that lacks direct ownership.
- [x] 2.2 Implement backfill logic for those tables using their documented authoritative relations.
- [x] 2.3 Tighten constraints or follow-up enforcement so newly scoped tables treat `school_id` as required once data is complete.

## 3. Update backend ownership and authorization logic

- [x] 3.1 Update affected Eloquent models and relationships to expose direct `school_id` ownership.
- [x] 3.2 Update admin create and update flows so school admins automatically write their assigned `school_id` instead of trusting client-provided values.
- [x] 3.3 Update scoped queries, validation, and authorization checks to use direct `school_id` on records where available.

## 4. Update admin UI and input flows

- [x] 4.1 Remove or disable manual school selection from school-admin CRUD forms that now derive school ownership automatically.
- [x] 4.2 Ensure superadmin flows keep explicit school selection only where cross-school creation remains required.
- [x] 4.3 Update shared admin JavaScript and Blade pages so list/filter/create flows continue to work with the new ownership rules.

## 5. Verify data integrity and regression safety

- [ ] 5.1 Seed or migrate representative data and verify backfilled `school_id` values on each newly scoped table.
- [ ] 5.2 Test school-admin and superadmin CRUD flows for scoped entities to confirm automatic ownership assignment and cross-school access restrictions.
- [ ] 5.3 Regenerate or review API documentation and note any request/response changes caused by the new school ownership model.
