## Context

Admin pages use a shared Blade help-button component that can highlight one or more annotated UI targets and render floating explanation bubbles. The current implementation places text bubbles near highlighted elements, but it does not include a pointer, connector, or other explicit visual anchor between a bubble and its related element. Because multiple targets can be highlighted at once on dense admin screens, users can struggle to tell which explanation belongs to which control.

## Goals / Non-Goals

**Goals:**
- Make each help explanation on admin pages visually point to the target it describes.
- Preserve the existing help-button trigger flow and target registration model based on `data-help-target` selectors.
- Keep the first implementation centralized in the shared help component so existing admin pages benefit without page-specific rewrites.

**Non-Goals:**
- Redesign the written help copy for every admin page.
- Extend the first pass beyond admin pages.
- Introduce a new walkthrough system, step-by-step tour, or persistent onboarding state.

## Decisions

### Use connector-style callouts in the shared help overlay
The shared component will render each explanation as a callout that includes a visible directional cue to its highlighted element, such as an arrow or connector stem. This directly addresses the current ambiguity while keeping the existing highlight model.

Alternative considered: keeping the current detached badges and only changing spacing or colors. This was rejected because better spacing still leaves the core association problem unsolved when multiple elements are close together.

### Continue deriving help relationships from existing selectors and labels
The help overlay will continue using `data-help-target` selectors plus label text from the component inputs. This keeps the change backward-compatible for current admin pages and avoids duplicating mapping logic per screen.

Alternative considered: defining page-specific coordinates or explicit layout metadata for every help target. This was rejected because it would add maintenance burden across many admin views for a problem that can be solved centrally.

### Reposition callouts within the viewport while preserving target association
Callouts should still avoid clipping and overlap where possible, but preserving a clear link to the target takes precedence over perfectly compact layout. The positioning logic can adapt placement above, below, left, or right of the element as long as the visual cue remains attached.

Alternative considered: locking all bubbles to a single side of the screen with a legend-style list. This was rejected because it increases eye travel and weakens the spatial connection to the UI.

## Risks / Trade-offs

- Connector and placement logic may become more complex on crowded layouts or small screens. → Mitigation: keep the implementation inside the shared component and choose simple fallback placements that prioritize clarity over perfect symmetry.
- Callouts may still overlap in edge cases when many targets are visible at once. → Mitigation: retain existing collision avoidance behavior where practical and allow vertical stacking or alternative placement when space is limited.
- Some admin pages may have targets inside scrollable or narrow containers. → Mitigation: base positioning on live element bounds and validate against representative admin pages that already use the help component.

## Migration Plan

- Update the shared help-button component implementation.
- Verify the new behavior on the existing admin pages that already use the component.
- Roll back by restoring the previous component markup and positioning logic if the new callouts create regressions.

## Open Questions

- None at proposal time; the initial implementation can proceed within the shared admin help component.
