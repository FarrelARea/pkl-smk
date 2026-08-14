## 1. Navigation and UI scoping

- [x] 1.1 Audit admin sidebar/layout and identify where the Sekolah menu is rendered for authenticated admin users.
- [x] 1.2 Update the admin navigation so `school_admin` users do not see the Sekolah menu or other school-management entry points, while superadmin behavior stays unchanged.
- [x] 1.3 Audit admin Blade pages and JavaScript flows that currently render a school selector for scoped CRUD operations.

## 2. School-bound form behavior

- [x] 2.1 Update admin forms used by school admins so school context comes from the logged-in admin assignment instead of a manual selector.
- [x] 2.2 Preserve global school selection behavior for superadmin on the same pages where cross-school management is still allowed.
- [x] 2.3 Ensure any displayed school context for school admins is read-only or hidden so the UI does not imply the school can be changed.

## 3. Backend enforcement

- [x] 3.1 Identify controller endpoints that accept `school_id` from admin-facing flows touched by this change.
- [x] 3.2 Update those endpoints so requests from `school_admin` derive the effective `school_id` from the logged-in user assignment instead of trusting client payload.
- [x] 3.3 Reject or ignore cross-school payload attempts from `school_admin` according to each endpoint contract, while preserving superadmin global behavior.

## 4. Verification

- [x] 4.1 Verify a school admin no longer sees the Sekolah menu after login.
- [x] 4.2 Verify a school admin can create or update school-scoped data without manually selecting a school and that saved data is tied to the assigned school.
- [x] 4.3 Verify direct API requests from a school admin cannot force another `school_id`, and superadmin flows still work across schools.
