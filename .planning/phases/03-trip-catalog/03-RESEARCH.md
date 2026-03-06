# Phase 3: Trip Catalog - Research

**Researched:** 2026-03-06
**Domain:** PHP catalog page with vanilla JS filtering, sticky UI, and Tally embed
**Confidence:** HIGH

---

<user_constraints>
## User Constraints (from CONTEXT.md)

### Locked Decisions

**Filter bar design**
- Active filter: navy #000744 background + white text
- Inactive filter: transparent background, white outline border
- Mobile: both rows scroll horizontally (no wrapping)
- Filter bar is sticky — sticks to top of viewport after user scrolls past the hero

**Multi-filter behavior**
- Row 1 (continents): single-select — clicking a continent replaces the previous selection
- Row 2 (tags): multi-select — AND logic, multiple tags can be active simultaneously
- Clicking an already-active filter deselects it (toggle behavior)
- "Tutti" on each row deselects all filters in that row
- "Mostrando X viaggi" count updates instantly as JS filters apply, with a brief CSS fade transition on the number

**Catalog hero**
- Short banner: ~35–40vh height (not full-viewport)
- Content: "I Nostri Viaggi" in large Playfair Display + short subline only (no CTA button)
- Static background photo with dark overlay — no parallax effect
- Purpose: just enough visual premium feel before quickly getting to filters

**Empty state**
- Shown instantly when no trips match active filters (no skeleton, immediate swap)
- Warm, human Italian copy — e.g. "Nessun viaggio trovato per i tuoi filtri. Non trovate quello che cercate? Proponeteci un viaggio su misura!"
- Inline Tally iframe embed directly below the message (no modal, no new tab)
- Visual treatment: centered, dark background, white text — headline uses navy #000744 underline accent (NOT gold, NOT red — navy is the brand accent for decorative elements)
- CSS fade/opacity transition when empty state appears or disappears (grid <-> empty state swap)

**Brand palette clarification (CRITICAL)**
- Navy #000744 is the PRIMARY brand color and decorative accent (section header underlines, active filter, CTAs)
- Red #CC0031 is URGENCY/ERRORS only (status badges, error states, urgency banners)
- There is NO gold accent color in use — despite --gold being defined in CSS variables, it maps to red (#CC0031) which is urgency-only
- All section header underlines: navy #000744
- All decorative accents (filter active state, underlines): navy #000744

### Claude's Discretion
- Exact Unsplash photo URL for the catalog hero banner
- Filter bar background color (dark, slightly elevated from page background)
- Exact spacing between filter rows
- Tally form URL placeholder in config.php (already defined as TALLY_CATALOG_URL or similar)

### Deferred Ideas (OUT OF SCOPE)
None — discussion stayed within phase scope.
</user_constraints>

---

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|-----------------|
| CATALOG-01 | Hero banner "I Nostri Viaggi" over dark travel photo | Catalog hero pattern documented in Architecture Patterns; hero CSS already established in Phase 2 (.hero class) — new `.catalog-hero` variant needed at 35–40vh |
| CATALOG-02 | Dual-row filter bar: Row 1 = continent filters (Tutti + 6 continents from tags.json); Row 2 = tag/theme filters (Tutti + all non-continent tags from tags.json); active filter shows navy background | Filter bar architecture documented; tags.json structure confirmed (21 entries including continents as slugs); continent slugs are: america, asia, europa, africa, oceania, medio-oriente |
| CATALOG-03 | Trip count display "Mostrando X viaggi" updates dynamically with filtering | JS filter engine section documents the live-count + fade transition pattern |
| CATALOG-04 | Trip grid: 3-col desktop / 2-col tablet / 1-col mobile, cards identical to homepage | .trip-grid and all .trip-card sub-classes are PERMANENT and already defined in style.css — zero CSS changes needed for the grid itself |
| CATALOG-05 | JavaScript filtering: continent single-select, tags AND multi-select, smooth CSS transition, URL deep-linking (?continent=america&tag=famiglia) | JS filtering engine fully documented in Code Examples; URL param pre-application from PHP documented |
| CATALOG-06 | Empty state: friendly Italian message + inline Tally iframe embed; TALLY_CATALOG_URL from config.php | Empty state architecture documented; TALLY_CATALOG_URL already defined in includes/config.php as empty string |
</phase_requirements>

---

## Summary

Phase 3 creates `viaggi.php` — the trip catalog page. The page has three visual layers: a short hero banner (35–40vh), a sticky dual-row filter bar, and the trip grid with count display and empty state. All underlying components (trip cards, grid, PHP data functions, header/footer pattern) exist from Phases 1–2 and are used verbatim without modification.

The primary implementation work is two things: (1) new CSS for the catalog hero, filter bar, filter pills, count display, and empty state; and (2) an inline JavaScript filter engine that reads `data-continent` and `data-tags` attributes on each `.trip-card` wrapper, hides/shows cards based on active filters, updates the count, manages URL params, and swaps the grid/empty state panels. The PHP layer reads all published trips and all tags from JSON, renders the page with pre-applied filters from `$_GET`, and outputs trip wrappers with data attributes for the JS engine.

The stack is identical to Phase 2: pure PHP, vanilla JS inline `<script>` in `viaggi.php`, CSS appended to `assets/css/style.css` under a `/* === PHASE 3: TRIP CATALOG === */` comment block. No new dependencies are introduced.

**Primary recommendation:** Build `viaggi.php` as a single PHP file with an inline `<script>` block. Add all Phase 3 CSS to style.css in a clearly-labeled section. Reuse `.trip-grid` and `.trip-card` classes exactly as they exist — do not duplicate or override them.

---

## Standard Stack

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| PHP | 8.x (SiteGround) | Server-side rendering, $_GET params, JSON data load | Established in Phases 1–2; only server-side option |
| Vanilla JS | ES2020 | In-page filter engine (no framework, no build step) | SiteGround shared hosting; established pattern from Phase 2 |
| CSS (custom) | n/a | Filter bar, hero, empty state, count display | No external frameworks; pure custom CSS per project brief |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| Tally.so | external embed | Custom request form in empty state | When TALLY_CATALOG_URL is non-empty |
| Font Awesome 6.5 | CDN (already loaded) | Icons in filter UI if needed | Already in header.php — free to use |
| Google Fonts | CDN (already loaded) | Playfair Display in catalog hero title | Already in header.php |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| Inline `<script>` in viaggi.php | Separate `/assets/js/catalog.js` | CONTEXT.md locks inline scripts per page — established in Phase 2; separate file would require new deployment concern |
| CSS appended to style.css | New `/assets/css/catalog.css` | Single stylesheet is the established pattern; adding a second sheet adds a render-blocking request |
| Tally iframe | Custom HTML form | CONTEXT.md locks Tally embed; TALLY_CATALOG_URL already in config.php |

**Installation:**
```bash
# No new packages — pure PHP/HTML/CSS/JS
```

---

## Architecture Patterns

### Recommended Project Structure
```
viaggiacolbaffo/
├── viaggi.php              # NEW — catalog page (this phase)
├── assets/css/style.css    # EXTEND — append Phase 3 CSS section
├── includes/
│   ├── config.php          # ALREADY has TALLY_CATALOG_URL defined
│   ├── functions.php       # ALREADY has load_trips(), load_tags()
│   ├── header.php          # REUSE — set $hero_page = true before include
│   └── footer.php          # REUSE — standard include
└── data/
    ├── trips.json          # READ — all trips
    └── tags.json           # READ — all tags (21 entries)
```

### Pattern 1: PHP page bootstrap (established in Phase 2)
**What:** Every page starts with config + functions require, sets page variables, then includes header.
**When to use:** Every PHP page. Locked pattern.
**Example:**
```php
<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';
$page_title = 'I Nostri Viaggi — Viaggia col Baffo';
$hero_page = true;
require_once ROOT . '/includes/header.php';
?>
```

### Pattern 2: PHP pre-apply URL filters for deep-linking
**What:** Read `$_GET['continent']` and `$_GET['tag']` before rendering to pass initial state to JS.
**When to use:** Catalog page — enables deep-linking from destination cards, homepage "Vedi tutti i viaggi" with pre-filter, and Trip Detail "tags as pill links" (Phase 4).
**Example:**
```php
<?php
$all_trips   = array_values(array_filter(load_trips(), fn($t) => $t['published'] === true));
$all_tags    = load_tags();

// Pre-applied filters from URL (for deep-link support)
$init_continent = htmlspecialchars($_GET['continent'] ?? '');
$init_tag       = htmlspecialchars($_GET['tag'] ?? '');
?>
```

### Pattern 3: Trip card wrappers with data attributes for JS
**What:** Each published trip is wrapped in a `<div class="trip-card-wrapper">` that carries `data-continent` and `data-tags` — the JS engine reads these, never the card internals.
**When to use:** Required for the JS filter engine to work without re-rendering DOM.
**Example:**
```php
<?php foreach ($all_trips as $trip): ?>
<div class="trip-card-wrapper"
     data-continent="<?= htmlspecialchars($trip['continent']) ?>"
     data-tags="<?= htmlspecialchars(implode(' ', $trip['tags'] ?? [])) ?>">
  <div class="trip-card">
    <img class="trip-card__image" src="<?= htmlspecialchars($trip['hero_image']) ?>" alt="<?= htmlspecialchars($trip['title']) ?>" loading="lazy">
    <div class="trip-card__overlay"></div>
    <span class="trip-card__continent"><?= htmlspecialchars(ucfirst($trip['continent'])) ?></span>
    <span class="trip-card__status status--<?= htmlspecialchars($trip['status']) ?>">
      <?= htmlspecialchars(match($trip['status']) {
        'confermata'   => 'Confermata',
        'ultimi-posti' => 'Ultimi Posti',
        'sold-out'     => 'Sold Out',
        'programmata'  => 'Programmata',
        default        => ucfirst($trip['status'])
      }) ?>
    </span>
    <div class="trip-card__content">
      <h3 class="trip-card__title"><?= htmlspecialchars($trip['title']) ?></h3>
      <p class="trip-card__dates">
        <?= date('j M', strtotime($trip['date_start'])) ?> &ndash;
        <?= date('j M Y', strtotime($trip['date_end'])) ?>
      </p>
      <p class="trip-card__price">Da <?= number_format($trip['price_from'], 0, ',', '.') ?> &euro;</p>
      <a class="trip-card__cta btn btn--gold" href="/viaggio/<?= htmlspecialchars($trip['slug']) ?>">Scopri il viaggio</a>
    </div>
  </div>
</div>
<?php endforeach; ?>
```

### Pattern 4: JS filter engine (inline `<script>` at bottom of viaggi.php)
**What:** Reads filter pill clicks, updates active state, hides/shows `.trip-card-wrapper` elements, updates count, syncs URL params, swaps grid/empty state panels.
**When to use:** All filter interactivity — no server round-trip, pure client-side.
**Example:**
```javascript
(function() {
  // State
  var activeContinent = '<?= $init_continent ?>';
  var activeTags = <?= $init_tag ? json_encode([$init_tag]) : '[]' ?>;

  var wrappers = Array.from(document.querySelectorAll('.trip-card-wrapper'));
  var countEl  = document.getElementById('trip-count');
  var gridEl   = document.getElementById('trips-grid');
  var emptyEl  = document.getElementById('empty-state');

  function applyFilters() {
    var visible = 0;
    wrappers.forEach(function(w) {
      var continent = w.dataset.continent;
      var tags      = w.dataset.tags.split(' ');

      var continentMatch = !activeContinent || continent === activeContinent;
      var tagsMatch = activeTags.every(function(t) { return tags.indexOf(t) !== -1; });

      if (continentMatch && tagsMatch) {
        w.style.display = '';
        visible++;
      } else {
        w.style.display = 'none';
      }
    });

    // Update count with fade
    countEl.classList.add('count-fade');
    setTimeout(function() {
      countEl.textContent = visible;
      countEl.classList.remove('count-fade');
    }, 150);

    // Swap grid / empty state
    var hasResults = visible > 0;
    gridEl.style.display   = hasResults ? '' : 'none';
    emptyEl.style.display  = hasResults ? 'none' : '';

    // Sync URL params
    var params = new URLSearchParams();
    if (activeContinent) params.set('continent', activeContinent);
    activeTags.forEach(function(t) { params.append('tag', t); });
    var newUrl = params.toString() ? '?' + params.toString() : window.location.pathname;
    history.replaceState(null, '', newUrl);
  }

  // Continent pills (single-select)
  document.querySelectorAll('[data-filter-continent]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var val = this.dataset.filterContinent;
      activeContinent = (activeContinent === val) ? '' : val;
      syncPillsContinent();
      applyFilters();
    });
  });

  // Tag pills (multi-select AND logic)
  document.querySelectorAll('[data-filter-tag]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var val = this.dataset.filterTag;
      if (val === '') {
        activeTags = [];
      } else {
        var idx = activeTags.indexOf(val);
        if (idx === -1) activeTags.push(val);
        else activeTags.splice(idx, 1);
      }
      syncPillsTags();
      applyFilters();
    });
  });

  function syncPillsContinent() {
    document.querySelectorAll('[data-filter-continent]').forEach(function(btn) {
      var isActive = btn.dataset.filterContinent === activeContinent && activeContinent !== '';
      var isTutti  = btn.dataset.filterContinent === '' && activeContinent === '';
      btn.classList.toggle('filter-pill--active', isActive || isTutti);
    });
  }

  function syncPillsTags() {
    document.querySelectorAll('[data-filter-tag]').forEach(function(btn) {
      var val = btn.dataset.filterTag;
      var isActive = val === '' ? activeTags.length === 0 : activeTags.indexOf(val) !== -1;
      btn.classList.toggle('filter-pill--active', isActive);
    });
  }

  // Initialize
  syncPillsContinent();
  syncPillsTags();
  applyFilters();
})();
```

### Pattern 5: Sticky filter bar (CSS position: sticky)
**What:** Filter bar sticks to top of viewport after hero scrolls away. CSS-only — no JS scroll listener needed. The `.has-hero` header is `position: sticky; top: 0; z-index: 100`. The filter bar must be `position: sticky; top: [header height]; z-index: 90`.
**When to use:** Catalog filter bar only.
**Key insight:** The site header (`#site-header`) is already `position: sticky; top: 0`. The filter bar sits below in document flow. Use `position: sticky; top: 64px` (estimated header nav height from Phase 2 — `padding: 1rem 1.5rem` on a logo with `max-height: 50px` ≈ 50 + 32px padding = ~82px). Use `top: 80px` to be safe and avoid overlap.

### Pattern 6: Horizontal scroll filter rows (mobile)
**What:** Both filter rows scroll horizontally on mobile without wrapping. Standard pattern: `display: flex; overflow-x: auto; scrollbar-width: none; -webkit-overflow-scrolling: touch; white-space: nowrap`. Pills use `flex-shrink: 0`.
**When to use:** Both filter rows on all viewport sizes — desktop pills may naturally fit without scrolling, but the scroll container causes no harm.

### Pattern 7: Grid / empty state panel swap
**What:** Two sibling elements: `#trips-grid` (the `.trip-grid`) and `#empty-state`. JS toggles their `display` property. CSS opacity transition on the empty state for a smooth appearance.
**Example:**
```css
#empty-state {
  display: none;
  opacity: 0;
  transition: opacity 0.3s ease;
}
#empty-state.is-visible {
  display: block;
  opacity: 1;
}
```
**Note:** Since JS sets `display: none` directly (not via class toggle), the `display: none` path does not transition. To get a smooth fade-in, use a two-step approach in JS: remove `display:none` first, then on next frame add `.is-visible` class. Or simplify to `visibility + opacity` instead of `display` — but CONTEXT.md says "immediate swap" so a simple display toggle is acceptable; the CSS fade is on the count number, not the panel.

### Anti-Patterns to Avoid
- **Modifying `.trip-card` or `.trip-card__` classes:** These are PERMANENT from Phase 1 Plan 03. Never override or extend them for catalog-specific layout — add outer wrappers instead.
- **Using `overflow: hidden` on `.catalog-hero`:** Will break the sticky header (same bug fixed in Phase 2).
- **Separate JS file for catalog:** CONTEXT.md locks inline `<script>` pattern. A separate file adds deployment complexity and breaks the established pattern.
- **PHP-side filtering:** Filtering must be client-side JS so the count updates instantly without page reload (CATALOG-03, CATALOG-05).
- **Using `--gold` CSS variable for decorative accents:** `--gold` maps to `#CC0031` (red/urgency) in this project — always use `#000744` or `var(--primary)` for decorative navy accents.
- **Using `display: flex` with `flex-wrap: wrap` on filter rows:** CONTEXT.md requires `no wrapping` — must be `flex-wrap: nowrap`.

---

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Custom request form in empty state | HTML form with PHP handler | Tally iframe embed (`TALLY_CATALOG_URL` from config.php) | CONTEXT.md locked decision; Tally handles submission, spam, notifications |
| URL state management | Custom history API wrapper | `history.replaceState` + `URLSearchParams` directly in the filter IIFE | These are native browser APIs, no abstraction needed |
| CSS sticky detection | IntersectionObserver + JS | `position: sticky` CSS-only | Modern CSS handles this natively across all current browsers |
| Tag separation logic | Custom tag string parsing | `data-tags` attribute with space-separated slugs + `.split(' ')` | Simple, correct, no edge cases (slugs are lowercase ASCII with hyphens — no spaces in slug values) |

**Key insight:** The JS filter engine is ~80 lines of vanilla JS. Do not reach for a library. The data model (published trips with continent + tags arrays) is already perfectly shaped for client-side filtering.

---

## Common Pitfalls

### Pitfall 1: Sticky filter bar z-index conflict with site header
**What goes wrong:** Filter bar appears on top of or behind the header when scrolling.
**Why it happens:** Site header has `z-index: 100`. Filter bar needs a lower z-index but must still appear above the trip grid content.
**How to avoid:** Set filter bar to `position: sticky; top: 80px; z-index: 90`. The `top: 80px` clears the header height (~82px from Phase 2 nav padding). If the filter bar appears under the header, increase `top` value slightly.
**Warning signs:** Filter bar disappears or merges with header on scroll.

### Pitfall 2: CSS variable `--gold` is red, not gold
**What goes wrong:** Using `var(--gold)` for active filter or underline accents produces red (#CC0031), which looks like an error state.
**Why it happens:** The project remapped `--gold: #CC0031` after the design system was finalized. The CSS variable name is misleading.
**How to avoid:** Always use `#000744` or `var(--primary)` for all decorative navy accents. Use `var(--gold)` / `var(--accent)` ONLY for urgency/error elements.
**Warning signs:** Active filter pills appear red instead of navy.

### Pitfall 3: Tag AND logic produces zero results unexpectedly
**What goes wrong:** User selects multiple tags and gets empty state even when trips exist that match one tag.
**Why it happens:** AND logic (every selected tag must be in the trip's tags array) is stricter than OR. With only one trip in the data (West America), selecting both `famiglia` and `road-trip` works, but selecting `famiglia` and `cultura` returns zero.
**How to avoid:** This is correct behavior per CONTEXT.md. Ensure the empty state appears cleanly. Make sure the Tally embed is ready as fallback. The JS implementation using `Array.every()` is correct.
**Warning signs:** Empty state appears when user combines multiple tags — this is EXPECTED, not a bug.

### Pitfall 4: `data-tags` attribute with multi-word tag slugs
**What goes wrong:** If a tag slug contained a space, `split(' ')` would break it.
**Why it happens:** `data-tags` uses space as the delimiter between tags.
**How to avoid:** All tag slugs in `tags.json` are hyphenated (e.g. `road-trip`, `medio-oriente`, `ultimi-posti`) with no spaces. Verified in tags.json — all 21 slugs are safe. No special handling needed.
**Warning signs:** Tags with hyphens not matching — would indicate a slug normalization issue upstream.

### Pitfall 5: URL deep-linking with multiple tags
**What goes wrong:** `?tag=famiglia&tag=road-trip` — `$_GET['tag']` in PHP only returns the last value.
**Why it happens:** PHP's `$_GET` for repeated keys only keeps the last one unless keys use array notation (`tag[]`).
**How to avoid:** CONTEXT.md shows `?continent=america&tag=famiglia` (single tag). For Phase 3, pre-apply only the first `$_GET['tag']` value from PHP; JS handles the rest client-side after page load. This is the simplest correct approach for Phase 3 scope. (Multi-tag deep-linking via URL could use `tag[]` notation in a future phase.)
**Warning signs:** Only one tag pre-applied when URL has multiple `&tag=` params.

### Pitfall 6: Count display flicker on initial load
**What goes wrong:** Count shows "0" briefly before JS runs, then jumps to the correct number.
**Why it happens:** PHP renders the count number statically, JS overrides it on DOMContentLoaded.
**How to avoid:** PHP renders the initial count: `<span id="trip-count"><?= count($all_trips) ?></span>`. JS then applies URL-based filters immediately in the IIFE (no `DOMContentLoaded` wrapper needed since the `<script>` is at the bottom of the page, after all elements). The IIFE calls `applyFilters()` synchronously at end of initialization, updating the count before the browser paints if the script is in `<body>` before `</main>` close.
**Warning signs:** Count shows maximum then drops on first filter application.

### Pitfall 7: Tally embed when TALLY_CATALOG_URL is empty
**What goes wrong:** Empty `src=""` on an iframe produces a load error or renders a blank/broken frame.
**Why it happens:** `TALLY_CATALOG_URL` is currently `''` in config.php.
**How to avoid:** Guard the iframe with a PHP conditional:
```php
<?php if (TALLY_CATALOG_URL): ?>
  <iframe src="<?= htmlspecialchars(TALLY_CATALOG_URL) ?>" ...></iframe>
<?php else: ?>
  <p class="catalog-empty__cta-fallback">Scrivici su WhatsApp per un viaggio su misura.</p>
<?php endif; ?>
```
**Warning signs:** Broken iframe in empty state when Tally URL is not yet configured.

---

## Code Examples

Verified patterns from existing codebase (inspected directly):

### Catalog Hero CSS (new — appended to style.css)
```css
/* === PHASE 3: TRIP CATALOG === */

/* --- 1. Catalog hero (short banner, 35-40vh) --- */

.catalog-hero {
  position: relative;
  height: 38vh;
  min-height: 260px;
  background-image: url('https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=1920&q=80');
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.catalog-hero__overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
}

.catalog-hero__content {
  position: relative;
  z-index: 2;
  padding: 0 1.5rem;
}

.catalog-hero__title {
  font-family: var(--font-heading);
  font-size: clamp(2rem, 5vw, 3.5rem);
  color: var(--white);
  margin: 0 0 0.5rem;
  text-shadow: 0 2px 10px rgba(0,0,0,0.5);
}

.catalog-hero__sub {
  font-size: 1rem;
  color: rgba(255,255,255,0.8);
  margin: 0;
}
```

### Filter Bar CSS
```css
/* --- 2. Filter bar --- */

.filter-bar {
  position: sticky;
  top: 80px;
  z-index: 90;
  background: var(--dark-card);
  border-bottom: 1px solid rgba(255,255,255,0.08);
  padding: 0.75rem 0;
}

.filter-bar__row {
  display: flex;
  overflow-x: auto;
  gap: 0.5rem;
  padding: 0.375rem 1.5rem;
  scrollbar-width: none;
  -webkit-overflow-scrolling: touch;
  flex-wrap: nowrap;
}

.filter-bar__row::-webkit-scrollbar {
  display: none;
}

/* --- 3. Filter pills --- */

.filter-pill {
  flex-shrink: 0;
  display: inline-block;
  padding: 6px 16px;
  border-radius: 20px;
  border: 1.5px solid rgba(255,255,255,0.5);
  background: transparent;
  color: var(--white);
  font-size: 0.85rem;
  font-weight: 500;
  font-family: var(--font-body);
  cursor: pointer;
  transition: var(--transition);
  white-space: nowrap;
}

.filter-pill:hover {
  border-color: var(--white);
  background: rgba(255,255,255,0.1);
}

.filter-pill--active {
  background: #000744;
  border-color: #000744;
  color: var(--white);
}
```

### Count Display CSS
```css
/* --- 4. Count display --- */

.catalog-count {
  text-align: center;
  padding: 1.25rem 0 0.5rem;
  font-size: 0.9rem;
  color: var(--grey);
}

.catalog-count__number {
  color: var(--white);
  font-weight: 600;
  transition: opacity 0.15s ease;
}

.count-fade {
  opacity: 0;
}
```

### Empty State CSS
```css
/* --- 5. Empty state --- */

.catalog-empty {
  display: none;
  text-align: center;
  padding: 4rem 1.5rem;
  background: var(--dark);
  border-radius: var(--radius);
  margin: 2rem 0;
}

.catalog-empty__title {
  font-family: var(--font-heading);
  font-size: clamp(1.5rem, 3vw, 2rem);
  margin: 0 0 0.5rem;
  position: relative;
  display: inline-block;
  padding-bottom: 0.75rem;
}

.catalog-empty__title::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 3rem;
  height: 3px;
  background: #000744;
}

.catalog-empty__text {
  color: rgba(255,255,255,0.75);
  max-width: 500px;
  margin: 1rem auto 2rem;
}

.catalog-empty iframe {
  width: 100%;
  max-width: 600px;
  height: 400px;
  border: none;
  border-radius: var(--radius);
}
```

### PHP continent separation from tags.json
```php
<?php
$all_tags_raw = load_tags();
// Continent slugs match the trip['continent'] field values
$continent_slugs = ['america', 'asia', 'europa', 'africa', 'oceania', 'medio-oriente'];
$continents = array_values(array_filter($all_tags_raw, fn($t) => in_array($t['slug'], $continent_slugs)));
$theme_tags = array_values(array_filter($all_tags_raw, fn($t) => !in_array($t['slug'], $continent_slugs)));
?>
```

### Filter bar HTML structure
```php
<div class="filter-bar" id="filter-bar">
  <!-- Row 1: Continents (single-select) -->
  <div class="filter-bar__row">
    <button class="filter-pill filter-pill--active" data-filter-continent="">Tutti</button>
    <?php foreach ($continents as $c): ?>
      <button class="filter-pill <?= $init_continent === $c['slug'] ? 'filter-pill--active' : '' ?>"
              data-filter-continent="<?= htmlspecialchars($c['slug']) ?>">
        <?= htmlspecialchars($c['label']) ?>
      </button>
    <?php endforeach; ?>
  </div>
  <!-- Row 2: Theme tags (multi-select) -->
  <div class="filter-bar__row">
    <button class="filter-pill filter-pill--active" data-filter-tag="">Tutti</button>
    <?php foreach ($theme_tags as $t): ?>
      <button class="filter-pill <?= $init_tag === $t['slug'] ? 'filter-pill--active' : '' ?>"
              data-filter-tag="<?= htmlspecialchars($t['slug']) ?>">
        <?= htmlspecialchars($t['label']) ?>
      </button>
    <?php endforeach; ?>
  </div>
</div>
```

---

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Separate JS file per page | Inline `<script>` in page PHP file | Established Phase 2 | No build step, easier to read per-page logic |
| `var(--gold)` for brand accents | `#000744` / `var(--primary)` for decorative navy | Phase 2 design system finalization | --gold maps to red urgency; all accents are navy |
| `overflow:hidden` on hero | No overflow:hidden on hero sections | Phase 2 bug fix | Enables sticky header to work correctly |

**Deprecated/outdated in this project:**
- `var(--gold)` for non-urgency decorative use: remapped to red; always use `var(--primary)` (#000744) for decorative navy
- `--black`: aliased to `#000744` (navy), not true black — use `var(--dark)` (#111827) for dark backgrounds

---

## Open Questions

1. **Header height for filter bar `top` value**
   - What we know: Header nav has `padding: 1rem 1.5rem` and logo `max-height: 50px` → total ~82px
   - What's unclear: Exact rendered height may vary slightly by browser
   - Recommendation: Use `top: 80px` as the filter bar sticky offset; add a CSS comment noting the dependency. If filter bar overlaps header in UAT, adjust to `top: 84px`.

2. **Tally embed height**
   - What we know: TALLY_CATALOG_URL is empty; no Tally form exists yet
   - What's unclear: Final form field count determines iframe height needed
   - Recommendation: Set `height: 400px` as default; add a CSS comment that Lorenzo can adjust once the Tally form is built. The iframe renders content independently.

3. **Filter bar row label/divider**
   - What we know: CONTEXT.md does not specify row labels (e.g. "Continente:" prefix before pills)
   - What's unclear: Whether the two rows need visual separation beyond spacing
   - Recommendation: Use `padding-top: 0.375rem` gap between rows within `.filter-bar`. No text labels needed — the "Tutti" pill on each row is self-explanatory. Claude's discretion.

---

## Validation Architecture

### Test Framework
| Property | Value |
|----------|-------|
| Framework | None detected — manual browser verification |
| Config file | none |
| Quick run command | `grep -n "display: none" /c/Users/Zanni/viaggiacolbaffo/viaggi.php` (structural check) |
| Full suite command | Manual UAT: browser open viaggi.php, apply filters, verify count, verify empty state |

### Phase Requirements → Test Map
| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|-------------|
| CATALOG-01 | Hero renders at 35–40vh with overlay | grep/content | `grep -n "catalog-hero" /c/Users/Zanni/viaggiacolbaffo/viaggi.php` | ❌ Wave 0 |
| CATALOG-02 | Filter pills render for all continents + tags from tags.json | grep/content | `grep -c "filter-pill" /c/Users/Zanni/viaggiacolbaffo/viaggi.php` | ❌ Wave 0 |
| CATALOG-03 | Count element present with ID "trip-count" | grep/content | `grep -n "trip-count" /c/Users/Zanni/viaggiacolbaffo/viaggi.php` | ❌ Wave 0 |
| CATALOG-04 | trip-grid and trip-card-wrapper elements present | grep/content | `grep -n "trip-card-wrapper" /c/Users/Zanni/viaggiacolbaffo/viaggi.php` | ❌ Wave 0 |
| CATALOG-05 | JS filter engine: applyFilters function + history.replaceState + URLSearchParams | grep/content | `grep -n "applyFilters\|replaceState\|URLSearchParams" /c/Users/Zanni/viaggiacolbaffo/viaggi.php` | ❌ Wave 0 |
| CATALOG-06 | Empty state div + Tally conditional guard | grep/content | `grep -n "catalog-empty\|TALLY_CATALOG_URL" /c/Users/Zanni/viaggiacolbaffo/viaggi.php` | ❌ Wave 0 |

### Sampling Rate
- **Per task commit:** `grep -n "filter-pill\|trip-count\|applyFilters\|catalog-hero\|catalog-empty" /c/Users/Zanni/viaggiacolbaffo/viaggi.php`
- **Per wave merge:** Full grep suite above — all 6 checks must return matches
- **Phase gate:** All grep checks green + manual browser smoke test before `/gsd:verify-work`

### Wave 0 Gaps
- [ ] `viaggi.php` — does not exist yet; created in Wave 1, Task 1
- [ ] Phase 3 CSS section in `assets/css/style.css` — does not exist yet; appended in Wave 1, Task 2

*(No test framework to install — project uses manual verification + grep-based structural checks)*

---

## Sources

### Primary (HIGH confidence)
- Direct code inspection: `includes/functions.php` — `load_trips()`, `load_tags()` signatures confirmed
- Direct code inspection: `includes/config.php` — `TALLY_CATALOG_URL` constant confirmed defined (empty string)
- Direct code inspection: `assets/css/style.css` — All existing class names, CSS variable values, and line numbers confirmed
- Direct code inspection: `data/tags.json` — All 21 tag slugs confirmed; continent slugs confirmed as: america, asia, europa, africa, oceania, medio-oriente
- Direct code inspection: `includes/header.php` — `$hero_page = true` pattern, `#site-header` element, scroll threshold 80px confirmed
- Direct code inspection: `index.php` — Trip card rendering pattern (PHP match for status, htmlspecialchars, date format) confirmed
- `.planning/phases/03-trip-catalog/03-CONTEXT.md` — All locked decisions sourced from here

### Secondary (MEDIUM confidence)
- CSS `position: sticky` browser support: universally supported in all modern browsers (Chrome 56+, Firefox 59+, Safari 13+) — established web standard
- `history.replaceState` + `URLSearchParams`: universally supported in all modern browsers — established web standard
- `Array.prototype.every()` for AND filter logic: ES5+, universally supported

### Tertiary (LOW confidence)
- None — all findings derive from direct code inspection or established web standards

---

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH — all from direct codebase inspection; no new dependencies
- Architecture: HIGH — PHP/JS patterns directly derived from Phase 2 established code
- Pitfalls: HIGH — most from direct CSS variable inspection revealing --gold = red issue; sticky z-index from Phase 2 bug history
- CSS class names: HIGH — inspected style.css in full; all class names verified

**Research date:** 2026-03-06
**Valid until:** 2026-06-06 (stable stack — PHP/vanilla JS, no framework churn)
