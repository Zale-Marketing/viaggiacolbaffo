---
phase: quick-1-logo-visibility
plan: 01
type: execute
wave: 1
depends_on: []
files_modified:
  - assets/css/style.css
  - includes/header.php
autonomous: true
requirements: [LOGO-VISIBILITY]
must_haves:
  truths:
    - "Logo is legible on dark/transparent header (hero pages)"
    - "Logo is legible on the dark footer background"
    - "Header becomes white with navy links after scrolling 80px on hero pages"
    - "Header is white with navy links by default on non-hero pages"
  artifacts:
    - path: "assets/css/style.css"
      provides: "White pill background on .header-logo img and .footer-logo img, transparent/scrolled header states"
    - path: "includes/header.php"
      provides: "header-logo class on the logo anchor, scroll threshold updated to 80px"
  key_links:
    - from: "includes/header.php"
      to: "assets/css/style.css"
      via: ".header-logo class and body.has-hero + #site-header rules"
      pattern: "header-logo"
---

<objective>
Fix logo visibility across all surfaces by wrapping the logo image in a white pill background. Correct the header scroll behavior: transparent over hero, white after 80px scroll.

Purpose: The logo (navy + red on transparent PNG) is invisible against the dark hero and dark footer. A white pill makes it legible everywhere without altering the logo asset.
Output: Updated style.css and header.php. No other files change.
</objective>

<execution_context>
@./.claude/get-shit-done/workflows/execute-plan.md
@./.claude/get-shit-done/templates/summary.md
</execution_context>

<context>
@.planning/PROJECT.md
@.planning/STATE.md
</context>

<tasks>

<task type="auto">
  <name>Task 1: Add logo pill CSS and correct header scroll rules</name>
  <files>assets/css/style.css</files>
  <action>
Make the following targeted edits to assets/css/style.css:

1. REPLACE the existing `header` block (lines ~295-301):

FROM:
```css
header {
  background: #FFFFFF;
  border-bottom: 1px solid rgba(0, 7, 68, 0.12);
  position: sticky;
  top: 0;
  z-index: 100;
}
```

TO:
```css
header {
  background: #FFFFFF;
  border-bottom: 1px solid rgba(0, 7, 68, 0.12);
  position: sticky;
  top: 0;
  z-index: 100;
  transition: background 0.3s ease, border-color 0.3s ease;
}
```

2. REPLACE the existing `body.has-hero #site-header` transparent rule (lines ~321-327):

FROM:
```css
body.has-hero #site-header {
  background: rgba(0, 0, 0, 0.3);
  border-bottom: none;
}
```

TO:
```css
body.has-hero #site-header {
  background: rgba(0, 0, 0, 0.35);
  border-bottom: none;
}
```

3. REPLACE the existing `body.has-hero #site-header.scrolled` block (lines ~329-335):

FROM:
```css
body.has-hero #site-header.scrolled {
  background: var(--white);
  border-bottom: 1px solid rgba(0, 7, 68, 0.12);
}
body.has-hero #site-header.scrolled nav a {
  color: #000744;
}
```

TO:
```css
body.has-hero #site-header.scrolled {
  background: #FFFFFF;
  border-bottom: 1px solid rgba(0, 7, 68, 0.12);
}
body.has-hero #site-header.scrolled nav a {
  color: #000744;
}
```

4. ADD the following two new rule blocks immediately after the `/* === HEADER ===  */` section (after the `header nav a:hover` block, before `/* === PHASE 2: HOMEPAGE ===  */`):

```css
/* Logo pill — white background so navy+red logo reads on any surface */
.header-logo img {
  background: #FFFFFF;
  padding: 6px 10px;
  border-radius: 8px;
  max-height: 50px;
  width: auto;
  display: block;
}

.footer-logo img {
  background: #FFFFFF;
  padding: 8px 12px;
  border-radius: 8px;
  max-height: 55px;
  width: auto;
  display: block;
}
```

5. REMOVE the existing `.site-footer__logo` rule (lines ~730-734) — the new `.footer-logo img` rule replaces it. If the footer PHP uses `.site-footer__logo` directly on the img, Task 2 will move the class; after that change the old rule is dead. To avoid specificity conflicts, delete it entirely:

```css
.site-footer__logo {
  max-height: 45px;
  width: auto;
  margin-bottom: 1rem;
}
```

NOTE: `margin-bottom: 1rem` will be re-applied via the wrapper `<a>` in footer.php (Task 2 adds `style="display:block;margin-bottom:1rem;"` to the anchor). Do not lose this spacing.
  </action>
  <verify>
    <automated>grep -n "header-logo\|footer-logo\|rgba(0,0,0,0.35)" /c/Users/Zanni/viaggiacolbaffo/assets/css/style.css</automated>
  </verify>
  <done>
    style.css contains `.header-logo img` with `background: #FFFFFF`, `.footer-logo img` with `background: #FFFFFF`, and `body.has-hero #site-header` with `rgba(0, 0, 0, 0.35)`.
  </done>
</task>

<task type="auto">
  <name>Task 2: Apply header-logo class and fix scroll threshold in header.php</name>
  <files>includes/header.php</files>
  <action>
Make two targeted edits to includes/header.php:

1. ADD class `header-logo` to the logo anchor tag. Current line 23:

FROM:
```html
<a href="/"><img src="https://viaggiacolbaffo.com/wp-content/uploads/2025/09/Progetto-senza-titolo-2025-09-30T075103.747-e1759212405873.png" alt="Viaggia col Baffo" style="max-height:50px;width:auto;display:block;"></a>
```

TO:
```html
<a href="/" class="header-logo"><img src="https://viaggiacolbaffo.com/wp-content/uploads/2025/09/Progetto-senza-titolo-2025-09-30T075103.747-e1759212405873.png" alt="Viaggia col Baffo"></a>
```

(Remove the inline style from the img — those properties now live in `.header-logo img` in style.css.)

2. CHANGE the scroll threshold from 10 to 80 in the inline script. Current line 34:

FROM:
```js
header.classList.toggle('scrolled', window.scrollY > 10);
```

TO:
```js
header.classList.toggle('scrolled', window.scrollY > 80);
```

3. UPDATE the footer logo anchor in includes/footer.php to use class `footer-logo` and remove the `.site-footer__logo` class from the img. Current footer.php line 7:

FROM:
```html
<a href="/"><img src="https://viaggiacolbaffo.com/wp-content/uploads/2025/09/Progetto-senza-titolo-2025-09-30T075103.747-e1759212405873.png" alt="Viaggia col Baffo" class="site-footer__logo"></a>
```

TO:
```html
<a href="/" class="footer-logo" style="display:block;margin-bottom:1rem;"><img src="https://viaggiacolbaffo.com/wp-content/uploads/2025/09/Progetto-senza-titolo-2025-09-30T075103.747-e1759212405873.png" alt="Viaggia col Baffo"></a>
```

(The `margin-bottom` that was on `.site-footer__logo` is preserved on the anchor since that rule was deleted from CSS in Task 1.)

After writing both files, run the commit:
```bash
cd /c/Users/Zanni/viaggiacolbaffo && git add assets/css/style.css includes/header.php includes/footer.php && git commit -m "fix: logo white background pill on all dark surfaces, header scroll behavior"
```
  </action>
  <verify>
    <automated>grep -n "header-logo\|footer-logo\|scrollY > 80" /c/Users/Zanni/viaggiacolbaffo/includes/header.php /c/Users/Zanni/viaggiacolbaffo/includes/footer.php</automated>
  </verify>
  <done>
    header.php anchor has `class="header-logo"`, scroll threshold is `> 80`, footer.php anchor has `class="footer-logo"`. Git commit created.
  </done>
</task>

</tasks>

<verification>
After both tasks:
- grep confirms `.header-logo img` and `.footer-logo img` exist in style.css with `background: #FFFFFF`
- grep confirms `scrollY > 80` in header.php
- grep confirms `class="header-logo"` in header.php and `class="footer-logo"` in footer.php
- Git log shows the fix commit
</verification>

<success_criteria>
- Logo PNG is visible on dark hero background (white pill surrounds it in transparent header state)
- Logo PNG is visible in the dark footer (white pill surrounds it)
- On hero pages: header is semi-transparent until user scrolls 80px, then transitions to white with navy nav links
- On non-hero pages: header is white with navy nav links from page load (unchanged behavior)
- No inline styles left on the logo img elements
</success_criteria>

<output>
After completion, create `.planning/quick/1-fix-logo-visibility-on-dark-backgrounds-/1-SUMMARY.md` summarising what was changed.
</output>
