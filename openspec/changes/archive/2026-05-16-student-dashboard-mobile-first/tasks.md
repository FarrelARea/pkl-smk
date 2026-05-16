## 1. Restructure the student dashboard layout

- [x] 1.1 Rework `resources/views/dashboard/student.blade.php` into a mobile-first single-column content flow that prioritizes status magang and primary actions.
- [x] 1.2 Simplify the quick action cards so the main student actions are easy to scan and tap on small screens.
- [x] 1.3 Adjust supporting sections such as attendance summary, today clock status, and recent daily logs to stay readable on mobile and expand cleanly on larger screens.

## 2. Keep existing dashboard behavior functional

- [x] 2.1 Update any student dashboard DOM hooks in `resources/views/dashboard.blade.php` so existing loaders and action handlers still target the new layout correctly.
- [ ] 2.2 Verify the clock modal still opens, captures location/photo, and submits attendance correctly after the layout changes.
- [ ] 2.3 Verify recent log rendering, attendance summary rendering, and student navigation links still work with the simplified UI.

## 3. Polish usability states

- [x] 3.1 Improve loading and empty states in the student dashboard so users can understand data availability at a glance.
- [x] 3.2 Review spacing, button sizes, and section copy for simple and easy mobile use without adding extra feature scope.
- [ ] 3.3 Test the student dashboard in a browser on mobile-sized and desktop-sized viewports and fix any obvious regressions in the golden path.
