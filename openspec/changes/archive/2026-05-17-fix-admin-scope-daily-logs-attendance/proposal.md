## Why

Admin and superadmin users cannot reliably view daily logs and attendance from the admin pages using the same school-scope rules already applied elsewhere in the admin surface. This creates inconsistent access behavior in two high-visibility operational screens and breaks the expected distinction between school-scoped admin access and cross-school superadmin access.

## What Changes

- Update admin daily log behavior so school admins only see records within their assigned school scope.
- Update admin attendance behavior so school admins only see records within their assigned school scope.
- Preserve superadmin behavior so superadmins can continue to see records across all schools on both pages.
- Align these pages with the existing rule that direct school ownership should be preferred when available, and otherwise school scope should be derived from authoritative related records.

## Capabilities

### New Capabilities

### Modified Capabilities
- `school-scoped-admin-management`: refine admin operational data access requirements so daily log and attendance admin pages enforce assigned-school scope for school admins and full cross-school visibility for superadmins.

## Impact

- Affected code will likely include admin attendance and daily log API endpoints, query scoping in related controllers, and any Blade/frontend data loading for `/admin/daily-logs` and `/admin/attendance`.
- No new external dependencies are expected.
- Existing school-scope behavior in other admin flows remains the reference model for this change.
