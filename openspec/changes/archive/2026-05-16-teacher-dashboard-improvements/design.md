## Context

The application is a Laravel and Blade-based internship-management system where teachers rely on role-specific pages backed by `/api/v1` endpoints. The current teacher dashboard does not provide a simple operational view for daily supervision tasks, even though teachers need one place to review permission requests, score students, inspect uploaded files, and monitor daily attendance.

The requested change should preserve the existing lightweight Blade approach, avoid unnecessary frontend complexity, and organize the teacher experience into clear sections: internship-related, school-related, and other. Because these capabilities touch multiple teacher workflows, the design should favor reusing existing teacher APIs and page patterns rather than inventing a new frontend architecture.

## Goals / Non-Goals

**Goals:**
- Provide a teacher dashboard that is simple, usable, and immediately actionable.
- Group teacher actions and summaries into internship-related, school-related, and other sections.
- Surface permission approvals, scoring, uploaded files, and daily attendance as first-class teacher workflows.
- Reuse existing routing, Blade, and API patterns where possible.
- Keep authorization aligned with teacher assignments and internship scope.

**Non-Goals:**
- Rebuild teacher workflows as a SPA.
- Introduce a broad redesign of all teacher pages outside the dashboard-driven flow.
- Expand teacher permissions beyond assigned or already-supported academic and internship data.
- Add advanced calendar scheduling beyond the attendance visibility needed for daily monitoring.

## Decisions

### 1. Use the dashboard as a workspace, not just a landing page
The teacher dashboard should become a task-oriented workspace with sectioned cards, summary counts, and direct links into focused pages or modals. This keeps the homepage simple while reducing navigation friction.

**Why this approach:** It gives teachers one clear entry point without forcing every feature into a single crowded screen.

**Alternative considered:** Putting all tools directly on one long dashboard page would be simpler to wire initially, but it would become harder to scan and maintain.

### 2. Group content by teacher mental model
The dashboard should present three top-level groups: internship-related, school-related, and other. Internship-related should prioritize attendance, permissions, scores, and uploaded internship documents. School-related can emphasize assigned students or classes. Other can hold secondary utilities or navigation links.

**Why this approach:** The user explicitly wants scoping by these categories, and the grouping matches how teachers think about their work rather than raw database entities.

**Alternative considered:** Grouping by data type or API resource would mirror backend structure, but it would be less usable for teachers.

### 3. Reuse existing detail pages and APIs where available
The dashboard should mostly aggregate and link into existing teacher workflows, with small endpoint or UI additions only where current behavior does not support permission decisions, score entry, document visibility, or attendance summaries.

**Why this approach:** It minimizes implementation risk in a codebase with Blade views and many existing `/api/v1` controllers.

**Alternative considered:** Creating a new dashboard-specific API layer could make frontend code cleaner, but it would add extra maintenance overhead unless existing endpoints are clearly insufficient.

### 4. Scope all teacher data to assigned and relevant records
Teacher-visible permission requests, students, scores, documents, and attendance summaries should be filtered to the students, classes, internships, or companies already associated with the teacher through current assignment rules.

**Why this approach:** The dashboard needs to be useful without widening access beyond teacher responsibilities.

**Alternative considered:** Showing broader school-wide data would make the dashboard richer, but it would likely violate current role boundaries and create noise.

### 5. Keep attendance visibility lightweight
The attendance view should prioritize “today” and near-term visibility, such as a simple calendar or date-based panel showing present students and related attendance states, instead of a full scheduling system.

**Why this approach:** The requirement is to see who is attendance today, so a compact calendar/day view is enough.

**Alternative considered:** A full-featured calendar dependency would provide more interactions, but it would add complexity without a clear need.

## Risks / Trade-offs

- [Dashboard becomes too dense] → Keep cards focused on high-value actions and move deeper work into dedicated pages.
- [Existing APIs do not fully support teacher actions] → Add narrowly scoped endpoint changes only after checking current teacher controllers and authorization rules.
- [Attendance/calendar data is inconsistent across internships] → Base the view on already persisted attendance records and clearly define the supported status set.
- [Document visibility exposes unrelated student files] → Filter document queries through teacher assignments and only show files already meant for teacher review.
- [Score entry collides with existing assessment workflows] → Reuse the current assessment domain and keep teacher score entry aligned with existing templates and ownership rules.

## Migration Plan

- Update teacher dashboard Blade layout and supporting JavaScript.
- Add or adjust teacher-facing API endpoints needed for dashboard summaries and actions.
- Validate teacher authorization and assignment scoping for permissions, scores, documents, and attendance.
- Test teacher login and dashboard navigation flows.
- If deployment issues arise, rollback can revert the dashboard view and related controller changes without requiring schema changes unless implementation later introduces one.

## Open Questions

- Should permission approval happen inline on the dashboard or on a dedicated list page linked from the dashboard?
- Which uploaded file types should be teacher-visible by default: all student documents or only internship-related submissions?
- Should the attendance view show only presence today, or also late/absent/permission states in the same calendar panel?
- Is score entry expected for all assigned students immediately, or only for students with active internships and matching assessment templates?
