---
task: 19
status: complete
commit: fe3c41a
date: 2026-03-09
---

# Quick Task 19 — Summary

## What changed

### viaggio.php
- **Sentinel:** `visibleDays` querySelector replaced with `document.querySelector('.gated-content')` — watches the first gated element directly.
- **IntersectionObserver:** New logic with `rootMargin: '0px 0px -50% 0px'` fires when gated content hits mid-screen. Added `barShown` flag to prevent duplicate toggles. When sentinel is below mid-screen (user hasn't reached it), bar hides and sheet closes. When sentinel is above viewport (scrolled past), bar stays visible.

### assets/css/style.css
- **Topbar @768px:** Switched to explicit `grid-template-rows: auto auto` 2-row layout. Left+Right in row 1, Center (savings badge) in row 2 spanning full width. Dates now visible at `0.7rem` (previously hidden). Status pill hidden. Savings and CTA properly sized.
- **Topbar @480px:** Replaced `display: none` on CTA with proper padding/font-size shrink — CTA stays visible on all mobile sizes.
