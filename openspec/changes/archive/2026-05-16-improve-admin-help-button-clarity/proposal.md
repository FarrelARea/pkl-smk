## Why

The admin help overlay explains page elements, but the current floating text bubbles do not visually point back to the components they describe. This makes the guidance hard to follow, especially when several highlighted elements are visible at once.

## What Changes

- Improve the admin help overlay so each explanation bubble clearly points to its related UI element.
- Refine the highlight presentation on admin pages to make relationships between help content and highlighted targets easier to scan.
- Keep the current help-button workflow and scope the first pass to admin pages only.

## Capabilities

### New Capabilities
- `admin-help-overlay`: Guided on-screen help for admin pages that visually associates each explanation bubble with the UI element it describes.

### Modified Capabilities

## Impact

- Affected code: `resources/views/components/help-button.blade.php`, admin Blade views that register help targets, and any related admin frontend utilities.
- Systems: Admin web UI only.
- Dependencies: No new backend or external service dependencies expected.
