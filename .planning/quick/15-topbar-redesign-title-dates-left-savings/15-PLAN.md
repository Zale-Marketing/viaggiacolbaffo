---
phase: quick-15
plan: 15
type: execute
wave: 1
depends_on: []
files_modified:
  - viaggio.php
  - assets/css/style.css
autonomous: true
requirements: [QUICK-15]

must_haves:
  truths:
    - "Topbar shows trip title + dates stacked on the left"
    - "Topbar shows savings badge + status pill centered"
    - "Topbar shows CTA button on the right"
    - "Status pill colour matches trip status (green/yellow/red/grey)"
    - "Mobile hides center column and dates, collapses layout to 2 columns"
  artifacts:
    - path: "viaggio.php"
      provides: "3-column topbar HTML with left/center/right sections"
    - path: "assets/css/style.css"
      provides: "Grid-based topbar CSS with status pill variants"
  key_links:
    - from: "viaggio.php"
      to: "$trip_status / $status_label"
      via: "PHP variables already defined at line 39-40"
    - from: "viaggio.php JS"
      to: "#topbar-savings"
      via: "getElementById + innerHTML with piggy-bank icon"
---

<objective>
Redesign the sticky trip topbar into a 3-column grid layout: title + dates on the left, savings badge + status pill centered, CTA button on the right. Replace the flat flex row with a CSS grid, add status pill variants, update the JS savings badge copy, and ship mobile-responsive collapse rules.

Purpose: Denser information display in the topbar — users see trip status and dates without scrolling down.
Output: Updated viaggio.php (HTML + JS) and assets/css/style.css (topbar block).
</objective>

<execution_context>
@./.claude/get-shit-done/workflows/execute-plan.md
@./.claude/get-shit-done/templates/summary.md
</execution_context>

<context>
@.planning/STATE.md
</context>

<tasks>

<task type="auto">
  <name>Task 1: Replace topbar HTML in viaggio.php</name>
  <files>viaggio.php</files>
  <action>
Replace the .trip-topbar div (lines 86-98) with the new 3-column structure below. The variables $date_display, $trip_status, $status_label are already defined earlier in the file — use them directly.

Replace this block:
```
<div class="trip-topbar" id="trip-topbar">
  <div class="trip-topbar__left">
    <span class="trip-topbar__name"><?php echo htmlspecialchars($trip['title'] ?? ''); ?></span>
    <?php if (!empty($fc['competitor_enabled'])): ?>
    <span class="trip-topbar__savings" id="topbar-savings"></span>
    <?php endif; ?>
  </div>
  <div class="trip-topbar__right">
    <?php if ($has_form): ?>
    <a href="#richiedi-preventivo" class="trip-topbar__cta">Richiedi Preventivo</a>
    <?php endif; ?>
  </div>
</div>
```

With:
```
  <div class="trip-topbar" id="trip-topbar">

    <!-- LEFT: title + dates -->
    <div class="trip-topbar__left">
      <div class="trip-topbar__name"><?php echo htmlspecialchars($trip['title'] ?? ''); ?></div>
      <div class="trip-topbar__dates">
        <i class="fa-regular fa-calendar"></i>
        <?php echo htmlspecialchars($date_display); ?>
      </div>
    </div>

    <!-- CENTER: savings + status -->
    <div class="trip-topbar__center">
      <?php if (!empty($fc['competitor_enabled'])): ?>
      <span class="trip-topbar__savings" id="topbar-savings"></span>
      <?php endif; ?>
      <span class="trip-topbar__status trip-topbar__status--<?php echo htmlspecialchars($trip_status); ?>">
        <?php
        $topbar_icons = [
          'confermata'   => '✓',
          'ultimi-posti' => '⚡',
          'sold-out'     => '✕',
          'programmata'  => '◷',
        ];
        echo ($topbar_icons[$trip_status] ?? '●') . ' ' . htmlspecialchars($status_label);
        ?>
      </span>
    </div>

    <!-- RIGHT: CTA -->
    <div class="trip-topbar__right">
      <?php if ($has_form): ?>
      <a href="#richiedi-preventivo" class="trip-topbar__cta">Richiedi Preventivo</a>
      <?php endif; ?>
    </div>

  </div>
```

Also find and replace the JS savings badge text (around line 815):

Find:
```
          topbarSavings.textContent = '✓ Risparmi fino a €' + ref_save.toLocaleString('it-IT') + ' vs altre agenzie';
```

Replace with:
```
          topbarSavings.innerHTML = '<i class="fa-solid fa-piggy-bank"></i> Con il Baffo risparmi <strong>€' + ref_save.toLocaleString('it-IT') + '</strong>';
```
  </action>
  <verify>
    <automated>grep -n "trip-topbar__center\|trip-topbar__dates\|trip-topbar__status\|topbar_icons\|piggy-bank" /c/Users/Zanni/viaggiacolbaffo/viaggio.php</automated>
  </verify>
  <done>viaggio.php contains __center, __dates, __status, topbar_icons array, and piggy-bank icon in the JS savings badge</done>
</task>

<task type="auto">
  <name>Task 2: Replace topbar CSS block in style.css</name>
  <files>assets/css/style.css</files>
  <action>
Replace the entire topbar CSS block from `/* --- 2. Sticky top bar --- */` (line 1338) through the closing `@media (max-width: 600px)` block ending at line 1416 (just before `/* --- 3. Highlights bar --- */`).

NOTE: .trip-tabs is defined separately at line 1462 — do NOT touch it. Only replace lines 1338-1416.

Replace with:

```css
/* --- 2. Sticky top bar --- */

.trip-topbar {
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 200;
  background: #000744;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  padding: 0 28px;
  height: 64px;
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: center;
  gap: 24px;
  transform: translateY(-100%);
  transition: transform 0.3s ease;
}

.trip-topbar.visible {
  transform: translateY(0);
}

/* LEFT */
.trip-topbar__left {
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-width: 0;
}

.trip-topbar__name {
  font-family: var(--font-heading);
  font-weight: 700;
  color: #ffffff;
  font-size: 0.95rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.2;
}

.trip-topbar__dates {
  font-size: 0.75rem;
  color: rgba(255,255,255,0.5);
  margin-top: 2px;
  display: flex;
  align-items: center;
  gap: 5px;
  white-space: nowrap;
}

/* CENTER */
.trip-topbar__center {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  flex-wrap: nowrap;
}

.trip-topbar__savings {
  display: none;
  align-items: center;
  gap: 7px;
  background: rgba(46,204,113,0.15);
  border: 1px solid rgba(46,204,113,0.35);
  color: #2ecc71;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.78rem;
  font-weight: 600;
  white-space: nowrap;
}

.trip-topbar__savings strong {
  font-weight: 800;
}

/* Status pills */
.trip-topbar__status {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  white-space: nowrap;
  letter-spacing: 0.02em;
}

.trip-topbar__status--confermata {
  background: rgba(46,204,113,0.15);
  border: 1px solid rgba(46,204,113,0.3);
  color: #2ecc71;
}

.trip-topbar__status--ultimi-posti {
  background: rgba(230,184,0,0.15);
  border: 1px solid rgba(230,184,0,0.35);
  color: #e6b800;
}

.trip-topbar__status--sold-out {
  background: rgba(204,0,49,0.15);
  border: 1px solid rgba(204,0,49,0.3);
  color: #CC0031;
}

.trip-topbar__status--programmata {
  background: rgba(255,255,255,0.07);
  border: 1px solid rgba(255,255,255,0.15);
  color: rgba(255,255,255,0.6);
}

/* RIGHT */
.trip-topbar__right {
  display: flex;
  justify-content: flex-end;
  flex-shrink: 0;
}

.trip-topbar__cta {
  display: inline-block;
  background: #CC0031;
  color: #FFFFFF;
  padding: 10px 24px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 0.875rem;
  transition: background 0.2s ease;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
  white-space: nowrap;
  font-family: var(--font-body);
}

.trip-topbar__cta:hover {
  background: #a80028;
}

/* Mobile */
@media (max-width: 768px) {
  .trip-topbar {
    grid-template-columns: 1fr auto;
    padding: 0 16px;
    height: 56px;
  }

  .trip-topbar__center {
    display: none;
  }

  .trip-topbar__name { font-size: 0.85rem; }
  .trip-topbar__dates { display: none; }
  .trip-topbar__cta { padding: 8px 16px; font-size: 0.8rem; }
}

@media (max-width: 480px) {
  .trip-topbar__cta { display: none; }
}
```
  </action>
  <verify>
    <automated>grep -n "grid-template-columns: 1fr auto 1fr\|trip-topbar__center\|trip-topbar__status--confermata\|trip-topbar__dates\|trip-topbar__status--sold-out" /c/Users/Zanni/viaggiacolbaffo/assets/css/style.css</automated>
  </verify>
  <done>style.css contains grid-template-columns 3-col, __center, __dates, and all 4 status pill variants (confermata, ultimi-posti, sold-out, programmata)</done>
</task>

</tasks>

<verification>
Both files updated:
- viaggio.php: 3-section topbar HTML, $topbar_icons array, piggy-bank innerHTML
- style.css: grid-based topbar, status pill variants, mobile breakpoints

Spot-check: `grep -n "trip-topbar__center\|trip-topbar__status" /c/Users/Zanni/viaggiacolbaffo/viaggio.php /c/Users/Zanni/viaggiacolbaffo/assets/css/style.css`
</verification>

<success_criteria>
- Topbar HTML has left/center/right divs (not left/right only)
- Status pill renders with icon + label, colour varies by $trip_status
- Savings badge uses innerHTML with piggy-bank icon and bold euro amount
- CSS uses grid-template-columns: 1fr auto 1fr (not flexbox justify-content: space-between)
- Mobile (max-width 768px): center hidden, 2-col grid; 480px: CTA hidden
</success_criteria>

<output>
After completion, create `.planning/quick/15-topbar-redesign-title-dates-left-savings/15-SUMMARY.md`
</output>
