## ADDED Requirements

### Requirement: Teacher can decide student permission requests
The system SHALL allow a teacher to review and accept or reject `izin` and `sakit` requests for students within the teacher's authorized scope.

#### Scenario: Teacher reviews pending permission requests
- **WHEN** a teacher opens the permission request workflow
- **THEN** the system shows pending `izin` and `sakit` requests for students assigned to or otherwise authorized for that teacher

#### Scenario: Teacher accepts a permission request
- **WHEN** a teacher accepts a pending `izin` or `sakit` request
- **THEN** the system stores the approval result and updates the request status in the teacher view

#### Scenario: Teacher rejects a permission request
- **WHEN** a teacher rejects a pending `izin` or `sakit` request
- **THEN** the system stores the rejection result and updates the request status in the teacher view

### Requirement: Teacher can enter or update student scores
The system SHALL allow a teacher to add or update scores for students within the teacher's authorized academic or internship scope.

#### Scenario: Teacher opens scoring workflow
- **WHEN** a teacher selects score entry for a student
- **THEN** the system shows the relevant scoring interface for that student and the applicable assessment context

#### Scenario: Teacher submits a score
- **WHEN** a teacher saves a valid score for an authorized student
- **THEN** the system persists the score and reflects the updated result in the teacher workflow

### Requirement: Teacher can view student uploaded files
The system SHALL allow a teacher to view uploaded student files that fall within the teacher's authorized review scope.

#### Scenario: Teacher opens uploaded files list
- **WHEN** a teacher opens the uploaded file workflow
- **THEN** the system shows student files that are visible to that teacher based on assignment and role rules

#### Scenario: Teacher opens a student file
- **WHEN** a teacher selects a visible uploaded file
- **THEN** the system provides access to inspect or download that file

### Requirement: Teacher can review daily attendance in a calendar-oriented view
The system SHALL provide a date-based attendance view that allows a teacher to see which authorized students are recorded as attending today.

#### Scenario: Teacher checks attendance today
- **WHEN** a teacher opens the attendance calendar or day view for the current date
- **THEN** the system shows which students in the teacher's scope are marked as attending today

#### Scenario: Teacher changes the viewed date
- **WHEN** a teacher navigates to another date in the attendance view
- **THEN** the system shows attendance information for that selected date using the same teacher scope rules

### Requirement: Teacher oversight data is scope-limited
The system SHALL restrict teacher permission decisions, scoring data, uploaded files, and attendance visibility to records within the teacher's authorized scope.

#### Scenario: Teacher requests out-of-scope oversight data
- **WHEN** a teacher attempts to access a permission request, student score target, file, or attendance record outside the teacher's authorized scope
- **THEN** the system denies access to that record
