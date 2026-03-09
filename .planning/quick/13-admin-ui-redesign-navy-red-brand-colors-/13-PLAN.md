---
phase: quick-13
plan: 01
type: execute
wave: 1
depends_on: []
files_modified:
  - admin/admin.css
  - admin/login.php
  - admin/index.php
  - admin/edit-trip.php
  - admin/settings.php
  - admin/tags.php
  - admin/destinations.php
autonomous: true
requirements: [QUICK-13]
must_haves:
  truths:
    - "Admin pages use navy #000744 as primary color with no gold (#C9A84C) anywhere"
    - "CTA buttons (Pubblica, Crea Nuovo Viaggio) use red #CC0031"
    - "Nav logo uses .logo-icon span in all five admin PHP files"
    - "Stat cards on index.php carry --red and --green modifier classes"
    - "showToast in index.php uses className-based icons; inline <style> block is removed"
  artifacts:
    - path: "admin/admin.css"
      provides: "Complete design system — CSS vars (navy/red/no-gold), nav, cards, tables, buttons, forms, modals, toast"
    - path: "admin/index.php"
      provides: "Updated nav logo, stat card modifiers, btn-cta buttons, className-based showToast, no inline <style>"
    - path: "admin/edit-trip.php"
      provides: "Updated nav logo, btn-cta on Pubblica, no inline gold/primary CSS vars"
  key_links:
    - from: "admin/admin.css"
      to: "all admin PHP files"
      via: "single <link rel=stylesheet href=admin.css>"
      pattern: "admin\\.css"
---

<objective>
Replace the gold-accented admin design system with a navy/red brand-aligned system across all admin pages.

Purpose: The current admin uses gold (#C9A84C) which clashes with the front-end brand palette. Replacing with navy (#000744) primary and red (#CC0031) CTA creates visual consistency between admin and public site.
Output: Updated admin.css + HTML patches in 6 PHP files. No functional changes — styling only except showToast refactor in index.php.
</objective>

<execution_context>
@./.claude/get-shit-done/workflows/execute-plan.md
</execution_context>

<context>
@.planning/STATE.md
</context>

<tasks>

<task type="auto">
  <name>Task 1: Replace admin.css with navy/red design system</name>
  <files>admin/admin.css</files>
  <action>
Replace the ENTIRE content of admin/admin.css with the new design system. Key changes from current file:

CSS variables — remove --gold and --gold-dark; add:
  --primary:      #000744
  --primary-dark: #000530
  --cta:          #CC0031
  --cta-dark:     #a8002a
  --bg:           #f4f5f7   (keep)
  --white:        #ffffff   (keep)
  --text:         #1a1a1a   (keep)
  --text-muted:   #666666   (keep)
  --border:       #e0e0e0   (keep)
  --danger:       #e53e3e   (keep)
  --success:      #38a169   (keep)
  --radius:       8px       (keep)
  --shadow:       0 1px 4px rgba(0,0,0,0.08) (keep)
  --nav-height:   56px      (keep)

Nav (.admin-nav): background: var(--primary). All gold/yellow references replaced with var(--primary) or var(--cta).

.logo-icon span: display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; background:var(--cta); color:#fff; border-radius:6px; font-weight:700; font-size:14px; — replaces the old gold compass span.

.admin-nav__visit link: color:rgba(255,255,255,0.7), hover color:#fff.

Stat cards: Base .stat-card unchanged. Add modifiers:
  .stat-card--red  { border-top: 3px solid var(--cta); }
  .stat-card--green{ border-top: 3px solid var(--success); }

Buttons:
  .btn-primary: background:var(--primary); hover:var(--primary-dark) — used for secondary actions.
  .btn-cta:     background:var(--cta); hover:var(--cta-dark) — used for publish/create actions.
  Both keep same padding/radius/font-weight as current .btn-primary.

Keep all other existing rule structure (tables, forms, toggles, tabs, pills, modals, save footer, alerts, login card, toast, drag handles, spinners, itinerary rows, hotel rows) — update only color references:
  - Any occurrence of var(--gold) or #C9A84C → var(--primary) or var(--cta) depending on context:
      * Nav background, active states, focus rings → var(--primary)
      * Submit/CTA buttons, badge accents → var(--cta)
  - Any occurrence of var(--gold-dark) or #a8832a → var(--primary-dark) or var(--cta-dark)

Toast: add rules for .toast--success (left border var(--success)) and .toast--error (left border var(--danger)); .toast__icon::before content depends on modifier class (add via JS in Task 2, styled here with appropriate colors).

Remove any inline override block if admin.css previously contained one (it does not currently — nothing to remove here).
  </action>
  <verify>
    <automated>grep -c "gold" C:/Users/Zanni/viaggiacolbaffo/admin/admin.css || true</automated>
  </verify>
  <done>admin/admin.css contains zero occurrences of "gold" or "#C9A84C"; contains --primary: #000744 and --cta: #CC0031 in :root; .btn-cta rule exists; .logo-icon span rule exists; .stat-card--red and .stat-card--green rules exist.</done>
</task>

<task type="auto">
  <name>Task 2: Patch PHP files — nav logo, stat cards, btn-cta, showToast, remove inline styles</name>
  <files>admin/login.php, admin/index.php, admin/edit-trip.php, admin/settings.php, admin/tags.php, admin/destinations.php</files>
  <action>
Apply the following targeted find-and-replace operations in each file. Make NO other changes.

--- admin/login.php ---
Find the login card header section that contains the gold compass icon HTML (a span with gold color style or class referencing the compass/gold icon). Replace it so the logo uses the new .logo-icon structure:
  OLD pattern (approximate): a span or div containing an emoji/icon with gold styling as the login logo
  NEW: &lt;div class="logo-icon"&gt;&lt;span&gt;V&lt;/span&gt;&lt;/div&gt; (or match the exact existing structure and swap classes)
  If the login card already has a header with a title "Viaggia Col Baffo Admin" or similar, keep the title text — only change the icon element.

--- admin/index.php ---
1. Nav logo: Find the existing nav logo HTML — likely a &lt;span&gt; or &lt;div&gt; with gold/compass styling.
   Replace with: &lt;span class="logo-icon"&gt;&lt;span&gt;V&lt;/span&gt;&lt;/span&gt;

2. "Vai al sito" link: Add class="admin-nav__visit" to the anchor tag that links to the public site (href="/").

3. Stat cards: Find the two stat card divs for published and draft counts.
   - Published card: add class "stat-card--red" (so it reads class="stat-card stat-card--red")
   - Draft card: add class "stat-card--green" (so it reads class="stat-card stat-card--green")

4. "Crea Nuovo Viaggio" button(s): Change class from btn-primary to btn-cta on any button/link that creates a new trip.

5. showToast function: Replace the existing showToast JS function body with a className-based version:
   function showToast(message, type = 'success') {
     const t = document.createElement('div');
     t.className = 'toast toast--' + type;
     t.innerHTML = '&lt;span class="toast__icon"&gt;&lt;/span&gt;&lt;span class="toast__msg"&gt;' + message + '&lt;/span&gt;';
     document.body.appendChild(t);
     requestAnimationFrame(() => t.classList.add('toast--show'));
     setTimeout(() => {
       t.classList.remove('toast--show');
       t.addEventListener('transitionend', () => t.remove(), {once: true});
     }, 3000);
   }
   (Adjust slightly if needed to match surrounding code style, but class-based approach is required.)

6. Remove inline &lt;style&gt; block: Delete the entire &lt;style&gt;...&lt;/style&gt; block embedded in index.php that contains drag/drop or toast CSS rules. These are now covered by admin.css.

--- admin/edit-trip.php ---
1. Nav logo: Same replacement as index.php — swap gold compass span for &lt;span class="logo-icon"&gt;&lt;span&gt;V&lt;/span&gt;&lt;/span&gt;.

2. Save footer Pubblica button: Change class btn-primary to btn-cta on the Pubblica/Salva e Pubblica button in the save footer.

3. Remove any inline &lt;style&gt; rules referencing var(--gold), var(--primary) as gold, or #C9A84C. Delete only those specific rules/blocks, not unrelated inline styles.

--- admin/settings.php, admin/tags.php, admin/destinations.php ---
In each file: Find the nav logo element (same gold compass span as above) and replace with &lt;span class="logo-icon"&gt;&lt;span&gt;V&lt;/span&gt;&lt;/span&gt;. No other changes needed in these three files.
  </action>
  <verify>
    <automated>grep -rn "gold\|#C9A84C\|btn-primary" C:/Users/Zanni/viaggiacolbaffo/admin/index.php C:/Users/Zanni/viaggiacolbaffo/admin/edit-trip.php C:/Users/Zanni/viaggiacolbaffo/admin/settings.php C:/Users/Zanni/viaggiacolbaffo/admin/tags.php C:/Users/Zanni/viaggiacolbaffo/admin/destinations.php || true</automated>
  </verify>
  <done>All six PHP files contain no gold/btn-primary references; admin/index.php has logo-icon, admin-nav__visit, stat-card--red, stat-card--green, btn-cta, className-based showToast, and no inline style block for drag/toast; admin/edit-trip.php has logo-icon and btn-cta on Pubblica; settings/tags/destinations each have logo-icon in nav.</done>
</task>

</tasks>

<verification>
After both tasks complete:

1. CSS audit: `grep -c "gold\|C9A84C" admin/admin.css` must return 0.
2. PHP audit: `grep -rn "btn-primary\|gold\|C9A84C" admin/*.php` should return only non-admin-UI occurrences (e.g., PHP variable names unrelated to CSS classes are acceptable but unlikely).
3. Visual spot-check (optional): Open admin/index.php in browser — nav should be navy, "Crea Nuovo Viaggio" button should be red, stat cards for published/draft should have colored top borders.
</verification>

<success_criteria>
- admin/admin.css: zero "gold" occurrences; --primary: #000744 and --cta: #CC0031 defined; .btn-cta, .logo-icon span, .stat-card--red, .stat-card--green rules present
- admin/index.php: logo-icon in nav, admin-nav__visit on "Vai al sito", stat-card--red/green modifiers, btn-cta on create buttons, className-based showToast, no inline style block
- admin/edit-trip.php: logo-icon in nav, btn-cta on Pubblica, no inline gold/primary CSS
- admin/settings.php, admin/tags.php, admin/destinations.php: logo-icon in nav
- admin/login.php: updated card header with .logo-icon structure
</success_criteria>

<output>
After completion, create `.planning/quick/13-admin-ui-redesign-navy-red-brand-colors-/13-SUMMARY.md` with what was changed in each file.
</output>
