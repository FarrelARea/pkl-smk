## 1. Scope the affected admin endpoints

- [x] 1.1 Identify the admin daily log endpoint and update its query so school admins only receive records from their assigned school while superadmins keep cross-school access.
- [x] 1.2 Identify the admin attendance endpoint and update its query so school admins only receive records from their assigned school while superadmins keep cross-school access.
- [x] 1.3 Ensure both endpoints derive school scope from direct `school_id` when present or from the existing authoritative relations when direct ownership is not stored on the top-level record.

## 2. Keep the admin pages aligned with the backend

- [ ] 2.1 Verify `/admin/daily-logs` still loads and renders correctly with scoped data for school admins and cross-school data for superadmins.
- [ ] 2.2 Verify `/admin/attendance` still loads and renders correctly with scoped data for school admins and cross-school data for superadmins.

## 3. Validate the behavior

- [ ] 3.1 Run targeted verification for the updated admin daily log and attendance flows.
- [ ] 3.2 Confirm the final behavior matches the school-scoped admin management requirement for both roles.
