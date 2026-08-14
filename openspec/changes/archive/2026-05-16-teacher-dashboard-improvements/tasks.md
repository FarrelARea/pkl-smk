## 1. Teacher dashboard workspace

- [x] 1.1 Audit the current teacher dashboard Blade view, route handling, and supporting JavaScript to identify reusable teacher page entry points.
- [x] 1.2 Redesign the teacher dashboard view so it groups content into internship-related, school-related, and other sections with simple summary cards and action links.
- [x] 1.3 Update teacher login-to-dashboard behavior if needed so successful teacher login consistently lands on the new teacher workspace.

## 2. Teacher oversight workflows

- [x] 2.1 Verify existing teacher-facing APIs and controllers for permission requests, score entry, student documents, and attendance data.
- [x] 2.2 Add or adjust teacher-scoped endpoints and authorization rules for accepting or rejecting `izin` and `sakit` requests.
- [x] 2.3 Add or adjust teacher-scoped score entry flow for assigned students using the existing assessment model.
- [x] 2.4 Add teacher-scoped uploaded file visibility so teachers can inspect or download relevant student files.
- [x] 2.5 Add a lightweight teacher attendance calendar or date-based panel that shows who is attending today.

## 3. Integration and validation

- [x] 3.1 Wire dashboard summaries and actions to the updated teacher workflows using existing Blade and frontend helper patterns.
- [x] 3.2 Validate that all teacher dashboard data is restricted to assigned and authorized records.
- [ ] 3.3 Test teacher login, dashboard usability, permission decisions, score entry, file viewing, and attendance visibility end to end.
