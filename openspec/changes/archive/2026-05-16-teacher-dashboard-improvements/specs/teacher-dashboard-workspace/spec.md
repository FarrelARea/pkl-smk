## ADDED Requirements

### Requirement: Teacher dashboard groups work by domain
The system SHALL present the teacher dashboard as a simple workspace grouped into `internship-related`, `school-related`, and `other` sections so teachers can find common tasks without navigating multiple unrelated pages first.

#### Scenario: Teacher opens dashboard
- **WHEN** an authenticated teacher opens `/dashboard`
- **THEN** the system shows a teacher dashboard with distinct sections for internship-related, school-related, and other tasks

#### Scenario: Teacher reviews available actions
- **WHEN** the dashboard loads teacher workspace content
- **THEN** each section shows only the actions and summaries relevant to that category

### Requirement: Teacher dashboard surfaces actionable summaries
The system SHALL show concise summary information and direct entry points for the highest-value teacher tasks, including permissions, scoring, uploaded files, and daily attendance.

#### Scenario: Teacher views dashboard summaries
- **WHEN** a teacher opens the dashboard
- **THEN** the system shows summary cards or panels that indicate available actions or counts for permissions, scoring, uploaded files, and attendance

#### Scenario: Teacher starts a workflow from dashboard
- **WHEN** a teacher selects a dashboard action for a supported workflow
- **THEN** the system opens the corresponding teacher page, panel, or modal for completing that workflow

### Requirement: Teacher dashboard remains simple
The system SHALL keep the teacher dashboard lightweight and readable by limiting the dashboard to core actions, summaries, and navigation into deeper teacher workflows.

#### Scenario: Teacher has many assigned records
- **WHEN** the teacher has many students, attendance records, or documents
- **THEN** the dashboard shows a manageable summary view instead of rendering every record directly on the landing page
