## ADDED Requirements

### Requirement: School-owned master data stores direct school ownership
The system SHALL store a direct `school_id` on every school-owned master-data or internship-support record that requires single-school ownership and does not already persist that ownership explicitly.

#### Scenario: Candidate table is identified as school-owned
- **WHEN** a master-data or internship-support table is determined to belong to exactly one school per record
- **THEN** the schema MUST include a `school_id` field that references the owning school

#### Scenario: Candidate table already has direct school ownership
- **WHEN** a reviewed table already stores an authoritative `school_id`
- **THEN** the system MUST preserve that ownership model and MUST NOT add a duplicate ownership field

### Requirement: Existing records are backfilled before school ownership is enforced
The system MUST populate newly added `school_id` fields for existing records from authoritative existing relations before treating those fields as required for normal operations.

#### Scenario: Existing record can derive school ownership
- **WHEN** a record in a newly scoped table is linked to a user, class, internship, supervisor assignment, or other parent record that already identifies its school
- **THEN** the migration MUST copy that school into the record's new `school_id`

#### Scenario: Existing record cannot derive school ownership automatically
- **WHEN** a record in a newly scoped table has no reliable relationship that determines its school
- **THEN** the system MUST flag that record for remediation before strict ownership constraints are enforced

### Requirement: School-scoped queries use direct school ownership
The system SHALL use direct `school_id` ownership for filtering, authorization, and validation on school-owned records once that ownership exists.

#### Scenario: School-scoped list is requested
- **WHEN** a school admin or scoped endpoint requests records from a table that has direct school ownership
- **THEN** the query MUST filter by the record's `school_id`

#### Scenario: Cross-school access is attempted on a direct-owned record
- **WHEN** a user attempts to read or mutate a record whose `school_id` belongs to another school
- **THEN** the system MUST reject the operation or omit the record according to the endpoint contract
