---
phase: quick-16
plan: 16
type: execute
wave: 1
depends_on: []
files_modified: [assets/css/style.css]
autonomous: true
requirements: [QUICK-16]

must_haves:
  truths:
    - "Trip-tabs nav items are horizontally centered within the nav bar"
    - "Hotel cards display in horizontal row layout (image left, text right) on desktop"
    - "Hotel cards stack vertically on mobile (max-width 768px)"
  artifacts:
    - path: "assets/css/style.css"
      provides: "Updated .trip-tabs__nav and hotel section CSS"
      contains: "justify-content: center"
  key_links:
    - from: "assets/css/style.css"
      to: ".trip-tabs__nav"
      via: "justify-content: center"
      pattern: "justify-content:\\s*center"
    - from: "assets/css/style.css"
      to: ".hotel-row"
      via: "flex horizontal layout"
      pattern: "\\.hotel-row\\s*\\{"
---

<objective>
Two targeted CSS edits to assets/css/style.css.

Purpose: Center the trip-tabs navigation and replace the hotel cards with the Villa Mercede horizontal-row style (image left, content right, red badge, hover lift).
Output: Updated style.css with centered nav and new hotel section styles.
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
  <name>Task 1: Apply both CSS edits to style.css</name>
  <files>assets/css/style.css</files>
  <action>
Read assets/css/style.css in full first.

EDIT 1 — Find the exact block:
```css
.trip-tabs__nav {
  display: flex;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
  overflow-x: auto;
  scrollbar-width: none;
}
```
Replace with (adding `justify-content: center;` as the second line after `display: flex;`):
```css
.trip-tabs__nav {
  display: flex;
  justify-content: center;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
  overflow-x: auto;
  scrollbar-width: none;
}
```

EDIT 2 — Find the entire hotel section CSS block. It begins with:
```css
.hotel-section { padding: 60px 0; background: #060b20; }
```
and ends at the closing `}` of the `@media (max-width: 700px)` block that belongs to the hotel section.

Replace that entire block (from `.hotel-section { padding: 60px 0; ...` through the closing `}` of the hotel media query) with:

```css
/* ========================================================
   HOTEL / ALLOGGI SECTION — Villa Mercede style
   ======================================================== */
.hotel-section { padding: 80px 0; background: #060b20; }

.hotel-list {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 32px;
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.hotel-row {
  display: flex;
  gap: 0;
  background: #0d1330;
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid rgba(255,255,255,0.07);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  min-height: 300px;
}

.hotel-row:hover {
  transform: translateY(-3px);
  box-shadow: 0 16px 48px rgba(0,0,0,0.4);
  border-color: rgba(255,255,255,0.14);
}

.hotel-row__img-wrap {
  width: 45%;
  flex-shrink: 0;
  position: relative;
  overflow: hidden;
}

.hotel-row__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  min-height: 300px;
  transition: transform 0.4s ease;
}

.hotel-row:hover .hotel-row__img {
  transform: scale(1.04);
}

.hotel-row__img-placeholder {
  width: 100%;
  height: 100%;
  min-height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #1a1f3e;
  color: rgba(255,255,255,0.15);
  font-size: 4rem;
}

.hotel-badge-notti {
  position: absolute;
  bottom: 16px;
  left: 16px;
  background: #CC0031;
  color: #fff;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  backdrop-filter: blur(4px);
}

.hotel-row__body {
  flex: 1;
  padding: 40px 44px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 0;
}

.hotel-row__top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 20px;
}

.hotel-row__city {
  font-size: 0.72rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: #CC0031;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.hotel-row__name {
  font-family: 'Playfair Display', serif;
  font-size: 1.6rem;
  color: #fff;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 10px;
}

.hotel-stars {
  color: #e6b800;
  font-size: 1rem;
  letter-spacing: 2px;
  margin-bottom: 0;
}

.hotel-row__badges {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 6px;
  flex-shrink: 0;
}

.hotel-colazione-yes {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(39,174,96,0.12);
  border: 1px solid rgba(39,174,96,0.3);
  color: #2ecc71;
  padding: 6px 14px; border-radius: 20px;
  font-size: 0.75rem; font-weight: 600; white-space: nowrap;
}

.hotel-colazione-no {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.1);
  color: rgba(255,255,255,0.4);
  padding: 6px 14px; border-radius: 20px;
  font-size: 0.75rem; white-space: nowrap;
}

.hotel-row__desc {
  color: rgba(255,255,255,0.6);
  font-size: 0.92rem;
  line-height: 1.7;
  margin: 0 0 20px;
}

.hotel-row__body::before {
  content: '';
  display: block;
  width: 48px;
  height: 2px;
  background: #CC0031;
  margin-bottom: 24px;
  border-radius: 2px;
}

.hotel-row__address {
  font-size: 0.8rem;
  color: rgba(255,255,255,0.3);
  display: flex;
  align-items: center;
  gap: 7px;
  margin-top: auto;
  padding-top: 20px;
  border-top: 1px solid rgba(255,255,255,0.06);
}

@media (max-width: 768px) {
  .hotel-list { padding: 0 16px; gap: 20px; }
  .hotel-row { flex-direction: column; min-height: auto; border-radius: 16px; }
  .hotel-row__img-wrap { width: 100%; height: 220px; }
  .hotel-row__img { min-height: 220px; }
  .hotel-row__body { padding: 24px 20px; }
  .hotel-row__top { flex-direction: column; gap: 12px; }
  .hotel-row__badges { align-items: flex-start; }
  .hotel-row__name { font-size: 1.3rem; }
  .hotel-row__body::before { margin-bottom: 16px; }
}
```

Make both edits in a single Write operation on the full file.
  </action>
  <verify>
    <automated>grep -n "justify-content: center" /c/Users/Zanni/viaggiacolbaffo/assets/css/style.css && grep -n "hotel-row__body::before" /c/Users/Zanni/viaggiacolbaffo/assets/css/style.css</automated>
  </verify>
  <done>style.css contains `justify-content: center` inside `.trip-tabs__nav` and the hotel section has been replaced with the Villa Mercede row layout including `.hotel-row__body::before` red accent line.</done>
</task>

</tasks>

<verification>
grep -n "justify-content: center" assets/css/style.css
grep -n "\.hotel-row " assets/css/style.css
grep -n "@media (max-width: 768px)" assets/css/style.css
</verification>

<success_criteria>
- `.trip-tabs__nav` contains `justify-content: center`
- `.hotel-row` flex layout exists (image 45% width, body flex:1)
- `.hotel-badge-notti` red badge positioned absolute bottom-left
- Mobile breakpoint at 768px stacks cards vertically
- Old `.hotel-section { padding: 60px 0; }` is gone, replaced by `padding: 80px 0`
</success_criteria>

<output>
After completion, create `.planning/quick/16-center-trip-tabs-nav-hotel-cards-villa-m/16-SUMMARY.md`
</output>
