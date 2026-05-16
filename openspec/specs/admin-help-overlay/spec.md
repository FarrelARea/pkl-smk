## ADDED Requirements

### Requirement: Help callouts must visually identify their targets
The system SHALL render each admin help explanation as a callout that visibly points to the highlighted UI element it describes when the user chooses to show help on screen.

#### Scenario: Single highlighted target
- **WHEN** the user activates "Tunjukkan di layar" and one admin help target is highlighted
- **THEN** the system shows a help callout for that target
- **AND** the callout includes a visible directional cue that points to the highlighted element

#### Scenario: Multiple highlighted targets
- **WHEN** the user activates "Tunjukkan di layar" and multiple admin help targets are highlighted on the same page
- **THEN** the system shows a separate help callout for each highlighted target
- **AND** each callout visibly indicates which element it belongs to without relying on text order alone

### Requirement: Help callouts must remain understandable within the viewport
The system SHALL place admin help callouts so their content remains readable while preserving a clear association with the related target.

#### Scenario: Target near viewport edge
- **WHEN** a highlighted admin help target is close to a viewport edge
- **THEN** the system repositions the help callout to keep it visible inside the viewport
- **AND** the callout still includes a visible cue linking it to the target

#### Scenario: Highlight cleanup
- **WHEN** the user dismisses the on-screen admin help overlay
- **THEN** the system removes the callouts and target highlights together
- **AND** no directional cues remain visible on the page
