## 1. Shared help overlay update

- [x] 1.1 Update `resources/views/components/help-button.blade.php` to render each help bubble as a callout with a visible pointer or connector to its highlighted target.
- [x] 1.2 Adjust callout positioning logic so bubbles stay readable in the viewport while preserving a clear visual link to the related target.
- [x] 1.3 Ensure dismissing the overlay removes highlights, callouts, and connector visuals together.

## 2. Admin page verification

- [x] 2.1 Verify existing admin pages that use `<x-help-button>` still highlight the intended targets without requiring page-specific rewrites.
- [ ] 2.2 Validate the updated on-screen help behavior on representative admin pages with multiple visible targets, such as classes and companies.
