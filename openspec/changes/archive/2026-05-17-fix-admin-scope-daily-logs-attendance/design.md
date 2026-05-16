## Context

The repository already defines school-scoped admin behavior in the `school-scoped-admin-management` capability, but admin daily log and attendance pages are not consistently enforcing that behavior. These pages are operational screens that often derive school ownership indirectly through related student, class, internship, or attendance records rather than through a direct `school_id` on the top-level record. The fix needs to preserve the current product rule: school admins are restricted to their assigned school, while superadmins retain cross-school visibility.

## Goals / Non-Goals

**Goals:**
- Make `/admin/daily-logs` follow assigned-school scoping for school admins.
- Make `/admin/attendance` follow assigned-school scoping for school admins.
- Preserve unrestricted cross-school access for superadmins on both pages.
- Reuse existing authoritative relationships to derive school scope when direct ownership is unavailable.
- Keep frontend behavior aligned with backend authorization and filtering.

**Non-Goals:**
- Adding new `school_id` columns to operational child tables covered by indirect ownership.
- Changing unrelated admin pages or introducing a new authorization model.
- Redesigning attendance or daily log UI beyond what is required to reflect correct scoped data.

## Decisions

- Enforce scope primarily in backend query construction for the admin daily log and attendance endpoints. This keeps the access rule authoritative even if a user issues direct requests outside the Blade UI.
- Use the logged-in admin's assigned school as the effective scope for school admin users, and bypass that scope for superadmins. This matches the existing capability contract already used in other admin flows.
- Prefer direct `school_id` filtering where the queried record already stores school ownership; otherwise derive scope through authoritative relations such as student, class, internship, or other existing parent records. This aligns with the earlier scope decision to avoid adding redundant ownership fields to operational tables.
- Keep the frontend unchanged unless it currently assumes cross-school results in a way that breaks rendering. The main correction should happen in the API data source rather than in client-side filtering.

## Risks / Trade-offs

- [Indirect ownership queries are inconsistent between endpoints] → Normalize the scope rule in each affected controller query and verify the same school relation is used for list and detail-style access.
- [Operational records may have incomplete relationships] → Use existing authoritative relations only; if some records cannot resolve school ownership, they should remain excluded from scoped admin results rather than weakening access control.
- [Frontend may expose empty-state or filter assumptions after scoping is tightened] → Verify both pages still render correctly for scoped datasets and for superadmin cross-school datasets.
