---
phase: quick-11
plan: 01
type: execute
wave: 1
depends_on: []
files_modified:
  - index.php
  - viaggi.php
  - data/tags.json
  - admin/tags.php
autonomous: true
requirements: [QUICK-11]

must_haves:
  truths:
    - "index.php and viaggi.php render trip cards without PHP warnings when date_start/date_end is null or empty"
    - "Trips with no date show 'Date da definire' in trip cards"
    - "data/tags.json contains all 12 months in the mese category"
    - "admin/tags.php new-tag form correctly sends group/category so new tags appear in the right section"
  artifacts:
    - path: "index.php"
      provides: "Null-safe date rendering in homepage trip cards"
    - path: "viaggi.php"
      provides: "Null-safe date rendering in catalog trip cards"
    - path: "data/tags.json"
      provides: "Complete 12-month mese tag set"
    - path: "admin/tags.php"
      provides: "Verified working category select for new tag creation"
  key_links:
    - from: "viaggi.php"
      to: "strtotime($trip['date_start'])"
      via: "null coalescing + empty check"
      pattern: "\\$date_start.*\\?\\?"
---

<objective>
Fix three separate bugs: null-safety for date fields in trip card rendering, missing month tags in tags.json, and verified correct group assignment when creating new tags in admin.

Purpose: Eliminate PHP undefined index warnings on date_start/date_end, ensure all 12 months appear in the mese filter, and confirm the new-tag form routes tags to the correct category group.
Output: index.php, viaggi.php (safe date rendering), data/tags.json (complete months), admin/tags.php (verified/fixed group select).
</objective>

<execution_context>
@./.claude/get-shit-done/workflows/execute-plan.md
</execution_context>

<context>
@.planning/STATE.md
</context>

<tasks>

<task type="auto">
  <name>Task 1: Fix date null-safety in index.php and viaggi.php</name>
  <files>index.php, viaggi.php</files>
  <action>
Fix all occurrences where date_start or date_end are passed directly to strtotime() without null checks.

In index.php around line 67-70, replace:
```php
<p class="trip-card__dates">
  <?= date('j M', strtotime($trip['date_start'])) ?> &ndash;
  <?= date('j M Y', strtotime($trip['date_end'])) ?>
</p>
```
with:
```php
<p class="trip-card__dates">
  <?php
    $ds = $trip['date_start'] ?? '';
    $de = $trip['date_end'] ?? '';
    if (!empty($ds) && !empty($de)):
      echo date('j M', strtotime($ds)) . ' &ndash; ' . date('j M Y', strtotime($de));
    else:
      echo 'Date da definire';
    endif;
  ?>
</p>
```

In viaggi.php around line 217, replace:
```php
$trip_month = (int) date('n', strtotime($trip['date_start']));
```
with:
```php
$ds_raw = $trip['date_start'] ?? '';
$trip_month = !empty($ds_raw) ? (int) date('n', strtotime($ds_raw)) : 0;
```

In viaggi.php around lines 251-254, replace:
```php
<p class="trip-card__dates">
  <?= date('j M', strtotime($trip['date_start'])) ?> &ndash;
  <?= date('j M Y', strtotime($trip['date_end'])) ?>
</p>
```
with:
```php
<p class="trip-card__dates">
  <?php
    $ds = $trip['date_start'] ?? '';
    $de = $trip['date_end'] ?? '';
    if (!empty($ds) && !empty($de)):
      echo date('j M', strtotime($ds)) . ' &ndash; ' . date('j M Y', strtotime($de));
    else:
      echo 'Date da definire';
    endif;
  ?>
</p>
```

Also update the data-date attribute on the trip card wrapper in viaggi.php (around line 230) to use the null-coalesced variable already set:
```php
data-date="<?= htmlspecialchars($ds_raw) ?>"
```
(this variable is now set earlier in the loop from the $trip_month fix above).

Note: viaggio.php already uses fmt_date() which handles empty strings — no changes needed there.
  </action>
  <verify>
    <automated>grep -n "strtotime(\$trip\[" /c/Users/Zanni/viaggiacolbaffo/index.php /c/Users/Zanni/viaggiacolbaffo/viaggi.php</automated>
  </verify>
  <done>Zero occurrences of strtotime($trip['date_start']) or strtotime($trip['date_end']) without a prior null coalesce/empty check. Both files render "Date da definire" when date fields are empty.</done>
</task>

<task type="auto">
  <name>Task 2: Add missing months to data/tags.json and verify admin/tags.php group select</name>
  <files>data/tags.json, admin/tags.php</files>
  <action>
BUG 2 — Add missing months to data/tags.json.

Current mese entries: aprile, maggio, giugno, settembre, ottobre.
Missing: gennaio, febbraio, marzo, luglio, agosto, novembre, dicembre.

Insert all 7 missing month tags into data/tags.json in chronological order within the mese category. Add them after the existing mese entries. The complete mese set in the file should be (in month order):
- { "slug": "gennaio", "label": "Gennaio", "category": "mese" }
- { "slug": "febbraio", "label": "Febbraio", "category": "mese" }
- { "slug": "marzo", "label": "Marzo", "category": "mese" }
- { "slug": "aprile", "label": "Aprile", "category": "mese" }  (already present)
- { "slug": "maggio", "label": "Maggio", "category": "mese" }  (already present)
- { "slug": "giugno", "label": "Giugno", "category": "mese" }  (already present)
- { "slug": "luglio", "label": "Luglio", "category": "mese" }
- { "slug": "agosto", "label": "Agosto", "category": "mese" }
- { "slug": "settembre", "label": "Settembre", "category": "mese" }  (already present)
- { "slug": "ottobre", "label": "Ottobre", "category": "mese" }  (already present)
- { "slug": "novembre", "label": "Novembre", "category": "mese" }
- { "slug": "dicembre", "label": "Dicembre", "category": "mese" }

Rewrite data/tags.json with all existing entries plus the 7 new month entries. Preserve all non-mese tags unchanged. Maintain JSON_UNESCAPED_UNICODE (literal UTF-8, no \uXXXX escapes).

BUG 3 — Verify admin/tags.php group select.

Reading the existing code: the form at line 165-171 already has a `<select id="new-tag-category">` with all required options (continente, tipo viaggio, per chi, mese, altro). The JS at line 271 already appends `category` to the FormData. The PHP handler at line 28-29 already reads `$category = trim($_POST['category'] ?? '')` and saves it at line 57.

However, the JS callback at line 283-285 does:
```js
const cat = data.tag.category || 'altro';
const container = document.getElementById('chips-' + cat)
               || document.getElementById('chips-altro');
```

For categories with a space like "tipo viaggio" or "per chi", `getElementById('chips-tipo viaggio')` will fail because HTML ids with spaces are invalid. The PHP renders these IDs as `id="chips-tipo viaggio"` (line 196 in the card loop) — this is the bug.

Fix in admin/tags.php: replace spaces with hyphens in chip container IDs consistently in both the PHP HTML output and the JS lookup.

In PHP (line 196), change:
```php
<div class="tag-chips" id="chips-<?= htmlspecialchars($cat_key) ?>">
```
to use a slug-safe id helper. Since PHP renders this, define a simple inline function or use str_replace:
```php
<div class="tag-chips" id="chips-<?= htmlspecialchars(str_replace(' ', '-', $cat_key)) ?>">
```
Apply the same str_replace to the second loop (around line 230) for non-standard categories.

In JS (line 283-285), change:
```js
const cat = data.tag.category || 'altro';
const container = document.getElementById('chips-' + cat)
               || document.getElementById('chips-altro');
```
to:
```js
const cat = (data.tag.category || 'altro').replace(/ /g, '-');
const container = document.getElementById('chips-' + cat)
               || document.getElementById('chips-altro');
```

Also update the `data-category` attribute on the card wrapper (line 190) to use the hyphenated slug for consistency — but note this attribute is used for display grouping only, not for getElementById, so it can remain as-is. Only the `id=` attributes and the JS lookup need the fix.
  </action>
  <verify>
    <automated>grep -c '"category": "mese"' /c/Users/Zanni/viaggiacolbaffo/data/tags.json</automated>
  </verify>
  <done>data/tags.json contains exactly 12 mese entries (gennaio through dicembre). admin/tags.php chip container IDs use hyphens for multi-word categories; JS lookup applies same replacement so new tags land in the correct group.</done>
</task>

</tasks>

<verification>
1. grep confirms zero bare strtotime($trip['date_start']) in index.php and viaggi.php
2. data/tags.json mese count equals 12
3. admin/tags.php HTML contains id="chips-tipo-viaggio" and id="chips-per-chi" (hyphenated)
4. admin/tags.php JS contains .replace(/ /g, '-') in the add-tag callback
</verification>

<success_criteria>
- index.php and viaggi.php: no PHP undefined index warnings for date_start/date_end; trips without dates show "Date da definire"
- data/tags.json: all 12 months present in mese category
- admin/tags.php: adding a new "tipo viaggio" or "per chi" tag appends the chip to the correct group section
</success_criteria>

<output>
After completion, create `.planning/quick/11-fix-3-issues-date-null-checks-missing-mo/11-SUMMARY.md`
</output>
