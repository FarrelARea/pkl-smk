## Context

The application has moved toward school-scoped administration, but many school-managed tables still rely on indirect joins through users, classes, internships, or other parent records to determine school ownership. This makes authorization, filtering, and record creation inconsistent, especially for admin-managed create flows where the effective school should come from the logged-in admin rather than client input.

This change spans database schema, model relationships, API controllers, and admin UI flows. It also introduces migration complexity because existing rows must be backfilled before newly added `school_id` columns can be treated as required.

## Goals / Non-Goals

**Goals:**
- Establish direct `school_id` ownership on all school-owned master data and related internship-support tables that currently lack it.
- Ensure school admin create/update flows derive the effective `school_id` from the authenticated admin account.
- Make read/write authorization and query filtering rely on direct school ownership where available.
- Define a repeatable audit pattern so each candidate table is evaluated explicitly instead of patched ad hoc.

**Non-Goals:**
- Rework unrelated role/permission behavior outside school scoping.
- Redesign all admin UI pages beyond the changes needed to remove manual school selection and preserve correct CRUD behavior.
- Add multi-school ownership for records that are currently expected to belong to a single school.

## Decisions

### Use direct `school_id` columns for school-owned records
Direct `school_id` columns will be added to each table that represents data owned by one school but currently lacks explicit ownership. This is preferred over continuing to infer school scope from joins because direct ownership simplifies authorization, filters, validation, and future indexing.

Alternative considered: keep deriving school scope through existing relationships. Rejected because it keeps controller logic inconsistent and makes back-office CRUD paths harder to secure.

### Backfill from existing authoritative relationships before enforcing required ownership
Each newly scoped table will use a migration or staged migration pair that first adds a nullable `school_id`, fills existing rows using the most reliable existing relationship, and only then tightens constraints when data is complete. This minimizes migration failures on live data.

Alternative considered: adding non-null `school_id` immediately with defaults. Rejected because there is no safe universal default and the correct school must come from existing domain relationships.

### Admin-managed writes will ignore client-provided school choice when school scope is implicit
For school admin flows, controllers and validation logic will derive the effective `school_id` from the authenticated admin's assigned school, even if the client sends another `school_id`. Superadmin flows may continue to select school explicitly where the product requires cross-school administration.

Alternative considered: allowing clients to submit `school_id` and validating it matches the admin's school. Rejected because silently trusting client school selection increases coupling and keeps unnecessary inputs in the UI.

### Table audit will classify candidates by ownership source
The implementation should review each master-data and internship-support table and place it into one of three groups: already has `school_id`, can derive `school_id` from an existing parent relation, or should remain unscoped because it is global/shared. This avoids adding `school_id` blindly to tables that are intentionally cross-school.

Alternative considered: adding `school_id` to every table touched by admin users. Rejected because some entities are global reference data or derive scope indirectly by design.

## Risks / Trade-offs

- [Backfill ambiguity on some records] → Mitigate by explicitly documenting the ownership source per table and flagging tables with missing or conflicting parent relations before enforcing non-null constraints.
- [Controller behavior divergence between school admin and superadmin flows] → Mitigate by centralizing effective-school resolution in shared controller helpers or consistent request handling patterns.
- [Schema sprawl across many tables] → Mitigate by grouping migrations logically and keeping the audit list explicit in specs/tasks so no table is missed.
- [Existing API clients may still send `school_id`] → Mitigate by preserving compatibility where safe but treating school admin payload values as ignored or overridden by server-side scope.

## Migration Plan

1. Inventory all candidate master-data and internship-support tables.
2. For each school-owned table without direct ownership, add nullable `school_id` plus foreign key/index.
3. Backfill `school_id` from existing authoritative relations.
4. Update models, queries, and admin controllers to read/write direct school ownership.
5. Remove manual school selection from school-admin flows and derive school server-side.
6. Tighten constraints to required where backfill completeness is guaranteed.

Rollback would remove enforcement changes first, then drop newly added constraints/columns only if necessary and safe for the environment.

## Open Questions

- Which candidate tables are intentionally global and should not receive `school_id`?
- Are there any legacy rows that cannot be mapped to a school from existing relations and need manual remediation?
- Should superadmin create flows keep explicit school selection for every newly scoped entity, or only for a subset of entities?
