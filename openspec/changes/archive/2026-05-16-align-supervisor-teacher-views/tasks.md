## 1. Audit parity scope

- [x] 1.1 Inventory teacher workspace segments, routes, Blade views, and supporting API endpoints that define the baseline supervisor parity target
- [x] 1.2 Map each teacher segment to an existing supervisor page or identify the missing supervisor route/view/API gap

## 2. Align supervisor navigation and entry points

- [x] 2.1 Update supervisor post-login redirect and role gating so supervisor users land in the expanded supervisor workspace
- [x] 2.2 Add or align supervisor sidebar/menu entries so the visible segment structure mirrors the teacher workflow

## 3. Implement supervisor segment parity

- [x] 3.1 Build or adapt supervisor Blade pages and client-side scripts for each missing core segment using teacher workflow behavior as the baseline
- [x] 3.2 Adjust role-specific copy, headings, icons, and contextual labels so supervisor pages stay distinct from teacher presentation

## 4. Close backend data gaps

- [x] 4.1 Audit supervisor controllers and endpoints against the data required by the parity segments
- [x] 4.2 Extend existing supervisor APIs or add supervisor-specific responses where needed without reusing teacher-only authorization paths

## 5. Validate the supervisor experience

- [ ] 5.1 Manually test supervisor login, landing page, sidebar visibility, and each parity segment end-to-end in the browser
- [ ] 5.2 Run targeted automated checks for the changed Laravel and frontend code paths and fix any regressions found
