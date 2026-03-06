---
status: resolved
trigger: "Trip Card Mobile Layout — Badge/Content Overlap: on mobile, trip card title text overlaps with the status pill badge in the top-right corner"
created: 2026-03-06T00:00:00Z
updated: 2026-03-06T14:30:00Z
---

## Current Focus

hypothesis: confirmed — .trip-card__content has no min-height or padding-top guard, and the card has no explicit min-height, so on short mobile viewports the absolutely-positioned bottom content block rises high enough to collide with the absolutely-positioned top badges
test: trace the stacking geometry of .trip-card, .trip-card__content, and .trip-card__status
expecting: confirmed — content block reaches the badge zone because nothing prevents it
next_action: DIAGNOSIS COMPLETE — return structured result to caller

## Symptoms

expected: bottom content (title, dates, price, CTA) stays below the badge zone; minimum card height 280px; gradient covers at least 60% from bottom
actual: card title "West America Aprile 2026" rises and visually collides with the "ULTIMI POSTI" orange pill in the top-right corner on mobile
errors: none (layout bug, no JS/PHP error)
reproduction: view index.php on a mobile viewport (< 768 px) while the trips-carousel shows a card with both a continent badge and a status pill
started: present since Phase 2 styles were applied

## Eliminated

- hypothesis: the badges themselves overflow or have wrong z-index
  evidence: .trip-card__continent and .trip-card__status both have z-index:2 and correct absolute positioning at top:1rem; they render in the right place — the problem is the content block from below, not the badges from above
  timestamp: 2026-03-06T00:00:00Z

- hypothesis: the overlay gradient is the cause
  evidence: .trip-card__overlay uses linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.85) 100%) — it covers the full card and does not constrain layout; it is cosmetic only
  timestamp: 2026-03-06T00:00:00Z

## Evidence

- timestamp: 2026-03-06T00:00:00Z
  checked: style.css lines 149-255 — .trip-card and all child selectors
  found: |
    .trip-card has position:relative, overflow:hidden, no min-height set.
    .trip-card__image has aspect-ratio:4/3 — this drives the card height.
    .trip-card__content has position:absolute; bottom:0; padding:1.25rem; width:100% — no padding-top, no min-height.
    .trip-card__status has position:absolute; top:1rem; right:1rem; z-index:2.
    No mobile @media rule adjusts any of these properties.
  implication: |
    The card height is entirely determined by aspect-ratio:4/3 on the image.
    On a mobile carousel item that is flex: 0 0 85% of the viewport, at 375 px viewport
    the item is ~319 px wide, giving a card height of ~239 px — BELOW the required 280 px minimum.
    The content block starts at the bottom and grows upward with no ceiling.
    The status pill sits at top:1rem (~16 px from top).
    A title + dates + price + button block stacks to roughly 120-140 px of content height.
    In a 239 px card, bottom:0 + 140 px of content = content top edge at ~99 px from the card top.
    The badge zone ends at approximately top:1rem + pill height (~28 px) = ~44 px from the top.
    So there is only ~55 px of clear space between badge bottom and content top — which collapses
    further when font sizes scale or when the title wraps to two lines, producing the observed overlap.
  implication: two independent missing constraints cause the overlap

- timestamp: 2026-03-06T00:00:00Z
  checked: style.css lines 459-481 — .trips-carousel and .trips-carousel__item
  found: |
    Mobile: .trips-carousel__item { flex: 0 0 85%; } — no height constraint on the item or its child .trip-card.
    Desktop (>=768px): switches to grid, no card height constraint added there either.
  implication: the carousel wrapper does not enforce minimum card height; height is purely driven by image aspect-ratio

- timestamp: 2026-03-06T00:00:00Z
  checked: index.php lines 44-70 — trip card HTML structure
  found: |
    Structure is: .trip-card > img.trip-card__image + .trip-card__overlay + span.trip-card__continent + span.trip-card__status + div.trip-card__content
    .trip-card__content contains: h3.trip-card__title + p.trip-card__dates + p.trip-card__price + a.trip-card__cta
    No inline styles or utility classes add any spacing constraint.
  implication: HTML structure is correct; the bug is purely CSS — no structural fix needed in PHP

## Resolution

root_cause: |
  Two missing CSS constraints work together to cause the overlap:

  1. .trip-card has no min-height. On mobile at 85% of a 375 px viewport the card
     renders at ~239 px via aspect-ratio:4/3 on the image — below the required 280 px.
     This compresses the space available between the badge zone and the content block.

  2. .trip-card__content has no padding-top guard. It sits at bottom:0 with only
     padding:1.25rem on all sides. When content (title + dates + price + CTA button)
     is tall enough, the block's top edge intrudes into the top badge zone (~44 px
     from the card top). Nothing prevents this.

  The gradient is also too weak at the top — transparent 0% means the badge area
  has no background darkening, making any text collision visually worse, but this
  is secondary to the layout constraint problem.

fix: |
  Three targeted CSS changes are needed, all in assets/css/style.css,
  within or directly after the existing /* === TRIP CARDS === */ block (lines 147-255):

  CHANGE 1 — enforce minimum card height (line ~149, add to .trip-card rule):
    min-height: 280px;

  CHANGE 2 — add padding-top guard to .trip-card__content to push content
  below the badge zone. The badge zone bottom is at top:1rem + ~28px pill height
  = ~60px. A padding-top of 3.5rem (~56px) plus the existing 1.25rem bottom padding
  is safe. Replace the current padding shorthand:
    current:  padding: 1.25rem;
    new:      padding: 3.5rem 1.25rem 1.25rem;
  (padding-top: 3.5rem ensures content never overlaps when block is pushed up)

  CHANGE 3 — tighten the gradient so the content area always has a dark backdrop
  even when the block rises higher than expected. Adjust .trip-card__overlay:
    current:  background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.85) 100%);
    new:      background: linear-gradient(to bottom, transparent 0%, transparent 30%, rgba(0,0,0,0.75) 60%, rgba(0,0,0,0.92) 100%);
  This keeps the top badge zone clear while ensuring >=60% gradient coverage
  from the bottom per the spec.

  OPTIONAL — add a mobile-specific @media rule to ensure the carousel item
  and card cannot collapse below 280px even on very small screens:
    @media (max-width: 767px) {
      .trip-card {
        min-height: 280px;
      }
    }

files_changed: []
