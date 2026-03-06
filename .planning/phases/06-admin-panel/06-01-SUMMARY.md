---
phase: 06-admin-panel
plan: 01
subsystem: admin-foundation
tags: [config, data-layer, admin-css, destinations, tags]
dependency_graph:
  requires: []
  provides: [admin-config.json-overlay, save_tags, load_destinations, save_destinations, destinations.json, tags-with-category, admin.css]
  affects: [includes/config.php, includes/functions.php, data/destinations.json, data/tags.json, admin/admin.css, data/admin-config.json]
tech_stack:
  added: []
  patterns: [flock-write-pattern, admin-config-overlay-pattern]
key_files:
  created: [data/admin-config.json, data/destinations.json, admin/admin.css]
  modified: [includes/config.php, includes/functions.php, data/tags.json, README.md]
decisions:
  - config.php loads admin-config.json BEFORE any define() call using $_acfg overlay pattern; unset at bottom to avoid global scope pollution
  - ANTHROPIC_API_KEY replaces OPENAI_API_KEY throughout — rename reflects actual AI provider
  - save_destinations() does NOT wrap array_values() because destinations is a keyed object accessed by slug
  - tags.json category values: continente / tipo viaggio / per chi / mese (giappone and primavera map to tipo viaggio)
metrics:
  duration: "8 minutes"
  completed: 2026-03-06
  tasks: 2
  files: 7
---

# Phase 6 Plan 01: Admin Foundation Summary

**One-liner:** admin-config.json overlay in config.php, three new flock-pattern data functions, all 6 destinations migrated to JSON, 22 tags backfilled with category, and full-component admin.css created.

## Tasks Completed

| # | Task | Commit | Files |
|---|------|--------|-------|
| 1 | Refactor config.php — admin-config.json overlay + rename OPENAI→ANTHROPIC | d423051 | includes/config.php, data/admin-config.json, README.md |
| 2 | Extend functions.php + migrate destinations.json + backfill tags.json + create admin.css | 89ab0f0 | includes/functions.php, data/destinations.json, data/tags.json, admin/admin.css |

## What Was Built

### Task 1 — config.php admin-config overlay

Rewrote `includes/config.php` so that `data/admin-config.json` is loaded at the top of the file using `$_acfg = json_decode(file_get_contents($_acfg_file), true)`. Every `define()` now uses the JSON value first, falling back to the hardcoded default. `OPENAI_API_KEY` removed and replaced with `ANTHROPIC_API_KEY`. Bootstrap `data/admin-config.json` created with all 12 required keys including `urgency_bar_text`, `company_name`, and the API key fields. README updated to reference Anthropic.

### Task 2 — functions.php extensions + data files

Added `save_tags()`, `load_destinations()`, and `save_destinations()` to `includes/functions.php` using the identical flock-write pattern as the existing `save_trips()`. Key distinction: `save_destinations()` does not call `array_values()` because destinations is a keyed object (slug → object), while `save_tags()` does call `array_values()` since tags is a 0-indexed array.

`data/destinations.json` migrated from `includes/destinations-data.php` — all 6 slugs (america, asia, europa, africa, oceania, medio-oriente) with full editorial content: name, hero_image, 3 intro_paragraphs, 5 practical_info entries, 4 see_also cards, 3 curiosita facts.

`data/tags.json` updated: all 22 tags now have a `category` field. Mapping: continente (6 tags), tipo viaggio (8 tags including giappone/primavera), per chi (3 tags), mese (5 tags).

`admin/admin.css` created: 400+ lines of CSS with CSS variables (--gold: #C9A84C, etc.), admin nav (56px, sticky), page wrapper (1200px max-width), stats bar, card component, table with dark header, btn-primary/secondary/danger, form controls with gold focus ring, tab navigation, status pills, tag chips, modal overlay with animation, sticky save footer, alert messages, empty state, login page, spinner, and responsive breakpoints at 768px.

## Decisions Made

1. **admin-config overlay pattern** — PHP constants cannot be redefined, so JSON must be read before any `define()`. Using `$_acfg['key'] ?: 'default'` for string values that must be non-empty (password, WhatsApp), and `$_acfg['key'] ?? ''` for values that legitimately may be empty. Unset at end to avoid polluting global scope.

2. **ANTHROPIC_API_KEY rename** — The API key constant was named OPENAI_API_KEY but the project uses Anthropic. Renamed throughout (config.php, README.md) to reflect actual provider.

3. **save_destinations without array_values** — Destinations are keyed objects (accessed by slug like `$destinations['america']`). Wrapping in `array_values()` would lose the slug keys and break all slug-based lookups. Tags and trips are 0-indexed arrays so they do use `array_values()`.

4. **giappone and primavera as "tipo viaggio"** — Per the plan's category mapping. These are thematic trip types, not geographic continents.

## Deviations from Plan

None — plan executed exactly as written.

## Self-Check

- [x] `includes/config.php` contains "admin-config.json": YES (3 occurrences)
- [x] `includes/config.php` contains "ANTHROPIC_API_KEY": YES
- [x] `includes/config.php` does not contain "OPENAI_API_KEY": CONFIRMED
- [x] `data/admin-config.json` exists with all 12 keys including "urgency_bar_text": YES
- [x] `includes/functions.php` contains save_tags, load_destinations, save_destinations: YES (2 grep matches for combined pattern)
- [x] `data/destinations.json` has all 6 slugs: YES (america, asia, europa, africa, oceania, medio-oriente)
- [x] `data/tags.json` has "category" on all 22 entries: YES (22 matches)
- [x] `admin/admin.css` exists with "admin" keyword: YES (58 matches)
- [x] Commits d423051 and 89ab0f0 exist in git log: CONFIRMED

## Self-Check: PASSED
