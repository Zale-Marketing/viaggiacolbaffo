---
task: 19
slug: fix-gate-bar-trigger-logic-and-mobile-to
description: Fix gate bar trigger logic and mobile topbar CSS
date: 2026-03-09
---

# Quick Task 19: Fix gate bar trigger logic and mobile topbar CSS

## Tasks

### Task 1 — Fix IntersectionObserver sentinel in viaggio.php
- **File:** `viaggio.php`
- **Action:** Replace visibleDays querySelector sentinel with `document.querySelector('.gated-content')`. Replace IntersectionObserver block with rootMargin `-50%` bottom trigger + `barShown` flag + rect-based hide-when-below logic.

### Task 2 — Mobile topbar 2-row grid in assets/css/style.css
- **File:** `assets/css/style.css`
- **Action:** Replace both `@media (max-width: 768px)` and `@media (max-width: 480px)` topbar blocks with 2-row grid layout (left/right row 1, center row 2), visible dates at 0.7rem, hidden status pill, proper savings + CTA sizing.
