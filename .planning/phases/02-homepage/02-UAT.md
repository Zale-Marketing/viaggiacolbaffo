---
status: resolved
phase: 02-homepage
source: [02-01-SUMMARY.md, 02-02-SUMMARY.md, 02-03-SUMMARY.md]
started: 2026-03-06T13:30:00Z
updated: 2026-03-06T14:30:00Z
---

## Current Test

[testing complete]

## Tests

### 1. Hero Section
expected: Open index.php in the browser. A full-viewport hero section is visible with a mountain road background image, a tagline, and 2 CTA buttons (e.g. "Scopri i viaggi" and a WhatsApp/contact button).
result: pass

### 2. Transparent Header Scroll
expected: On the homepage, the header is fully transparent over the hero (no white background). Scroll down — the header smoothly transitions to solid white and stays there. Scroll back to top — it returns to transparent.
result: pass

### 3. Urgency Bar
expected: Immediately below the hero, a slim bar reads something like "West America Aprile 2026 — Ultimi 5 posti disponibili" (or similar urgency text). It is clearly visible and spans the full width.
result: pass

### 4. Active Trips Carousel
expected: A trips section shows the published trip cards loaded from trips.json. On mobile, cards snap-scroll horizontally. On desktop (768px+), they display as a grid. Each card has trip info (title, image, etc.).
result: issue
reported: "fail — on mobile the trip title text overlaps with the status pill badge in the top-right corner. The card title 'West America Aprile 2026' rises too high and collides with the 'ULTIMI POSTI' orange pill. The card layout needs fixing: badges must stay in the TOP area, title/dates/price must stay in the BOTTOM area separated by the gradient overlay. Specifically: TOP LEFT: continent badge (AMERICA), TOP RIGHT: status pill (ULTIMI POSTI), BOTTOM: title, dates, price, CTA button — these must NOT overlap with top badges. Add enough padding-top to the bottom content area so it never reaches the badge zone. Minimum card height on mobile: 280px. The gradient overlay must cover at least 60% from bottom to ensure readability."
severity: major

### 5. Destination Grid
expected: A 6-destination grid is visible (e.g. America, Africa, Asia, etc.). Each destination card is clickable and its href points to destinazione.php?slug=[name] (you can hover to see the link in the status bar).
result: pass

### 6. Why-Baffo Section
expected: A section with 4 feature/benefit blocks is visible (e.g. icons or headings explaining why to choose Viaggia col Baffo).
result: pass

### 7. Founder Section
expected: A founder section shows a portrait image, the founder's story text, and 3 gold/highlighted stats (e.g. years of experience, trips led, etc.).
result: pass

### 8. Testimonials Section
expected: 3 testimonial cards are displayed, each with a quote and attribution.
result: pass

### 9. B2B Banner
expected: A B2B/corporate travel banner is visible below the testimonials section, with a call-to-action for business clients.
result: pass

### 10. Footer
expected: A 3-column production footer is visible at the bottom: brand/logo column, navigation links column, and contacts column (WhatsApp link, phone, email). Social icons (Instagram, Facebook) are present. An IATA Accredited Agency badge is shown. The copyright year shows the current year (2026).
result: pass

## Summary

total: 10
passed: 9
issues: 1
pending: 0
skipped: 0

## Gaps

- truth: "Trip cards on mobile: TOP badges (continent + status pill) stay top, BOTTOM content (title/dates/price/CTA) stays bottom separated by gradient overlay — no overlap between zones. Min card height 280px. Gradient covers ≥60% from bottom."
  status: resolved
  reason: "User reported: on mobile the trip title text overlaps with the status pill badge in the top-right corner. The card title 'West America Aprile 2026' rises too high and collides with the 'ULTIMI POSTI' orange pill."
  severity: major
  test: 4
  root_cause: "Two missing CSS constraints: (1) .trip-card has no min-height — at 85% viewport width on mobile the card collapses to ~239px, below the required 280px; (2) .trip-card__content uses padding: 1.25rem on all sides — no top guard to keep content below the badge zone (~44px). Together, when the title wraps to 2 lines the content block's top edge reaches the badge zone. Secondary: gradient starts at transparent 0%, providing insufficient opacity in the top-badge zone, making overlap visually worse."
  artifacts:
    - path: "assets/css/style.css"
      issue: "Line ~149: .trip-card missing min-height: 280px"
    - path: "assets/css/style.css"
      issue: "Line ~229: .trip-card__content padding: 1.25rem (all sides) — needs padding-top: 3.5rem to clear badge zone"
    - path: "assets/css/style.css"
      issue: "Line ~173: .trip-card__overlay gradient starts transparent 0% — needs 4-stop gradient with dark coverage from 60% for readability"
  missing:
    - "Add min-height: 280px to .trip-card"
    - "Change .trip-card__content padding from 1.25rem to 3.5rem 1.25rem 1.25rem"
    - "Update .trip-card__overlay gradient: transparent 0%, transparent 30%, rgba(0,0,0,0.75) 60%, rgba(0,0,0,0.92) 100%"
  debug_session: ".planning/debug/trip-card-mobile-overlap.md"
