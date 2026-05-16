## Why

The current teacher dashboard is not usable enough for daily internship supervision, so teachers have to jump between pages or cannot complete key actions efficiently. The dashboard should stay simple while surfacing the internship and school workflows teachers use most often.

## What Changes

- Redesign the teacher dashboard information architecture so actions and data are grouped into simple sections such as internship-related, school-related, and other.
- Add dashboard support for reviewing student permission requests (`izin` / `sakit`) with accept and reject actions.
- Add teacher-facing scoring access so teachers can open assigned students and submit or update student scores.
- Add visibility into student-uploaded files so teachers can review documents from the dashboard flow.
- Add an attendance calendar or day view that shows which students are present today.
- Keep the experience lightweight and Blade-based, avoiding unnecessary complexity or a full SPA redesign.

## Capabilities

### New Capabilities
- `teacher-dashboard-workspace`: A teacher dashboard workspace that groups teacher tasks into internship-related, school-related, and other sections with direct access to common actions.
- `teacher-oversight-tools`: Teacher tools for handling permission decisions, scoring students, reviewing uploaded files, and checking daily attendance from a calendar view.

### Modified Capabilities
- `role-based-login`: Teacher post-login navigation should land users on a dashboard that exposes the updated teacher workspace and teacher-specific task entry points.

## Impact

- Affected Blade views for teacher dashboard and related teacher pages.
- Likely affected teacher-facing API endpoints and controllers for permission requests, assessments/scores, student documents, and attendance data.
- May require updates to frontend helper scripts used by teacher pages.
- No new external dependencies are expected if the calendar view is implemented with existing Blade and JavaScript patterns.
