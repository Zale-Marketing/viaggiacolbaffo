# Phase 5: Destinations + B2B - Research

**Researched:** 2026-03-06
**Domain:** PHP editorial pages, data-driven conditional rendering, Tally embed, cURL webhook
**Confidence:** HIGH

## Summary

Phase 5 is a content-heavy, pattern-reuse phase. All technical patterns required already exist in the codebase — destination pages follow the exact same structure as `viaggio.php` (slug routing, hero, sections, footer includes), and the B2B page follows the same section/layout pattern as the homepage. The only new technical element is the waitlist webhook endpoint, which is a simplified clone of `api/submit-form.php`.

The primary risk is volume and correctness of editorial content — 6 destinations, each needing real practical info, compelling Italian copy, 4 sub-destinations with photos, and 3 curiosity facts. This is the bulk of the work. The PHP scaffolding is straightforward and can be templated from existing pages.

The B2B page requires scraping the written guarantee language from the live site (`viaggiacolbaffo.com/diventa-agenzia-partner/`) — that copy has already been fetched and is documented in Code Examples below.

**Primary recommendation:** Build `includes/destinations-data.php` first (all 6 destinations' data in one pass), then `destinazione.php` as a single reusable template, then `agenzie.php`, then `api/submit-waitlist.php`.

---

<user_constraints>
## User Constraints (from CONTEXT.md)

### Locked Decisions

**Editorial content:**
- Claude writes solid, emotionally compelling Italian editorial copy for all 6 destinations — good-to-ship quality, Lorenzo refines later if desired
- 3 paragraphs per destination in `includes/destinations-data.php`
- All 6 destinations get full, accurate practical info boxes (currency, language, best season, timezone, visa requirements) — not placeholders
- Curiosita section: 3 facts per destination with Font Awesome 6 icons (fa-*), title, text, gold left accent border

**Data structure:**
- Single `includes/destinations-data.php` file — one PHP associative array keyed by slug
- Each slug entry contains: hero_image, intro_paragraphs (array of 3), practical_info (array of boxes), see_also (4 sub-destinations), curiosita (3 facts)
- Sub-destination data (name, photo URL, 2-line description) lives inside this same file

**Sub-destination cards ("Cosa Vedere"):**
- Visual style: vertical photo cards — full-bleed Unsplash photo top, name in Playfair + 2-line description below on dark card
- Exactly 4 per destination (4-col desktop / 2-col tablet / 1-col mobile grid)
- Cards are clickable: link to `viaggi.php?continent=[slug]` (filters catalog to that continent)
- Reuses visual DNA of `.trip-card` — consistent with the site's established card pattern

**B2B page (agenzie.php):**
- Tone: warm partnership / inviting — "Cresciamo insieme" — premium but human, matches Lorenzo's personal brand
- Commission language: "commissioni competitive" — no hardcoded percentage
- Written guarantee ("I tuoi clienti restano TUOI"): replicate copy from live site at viaggiacolbaffo.com/diventa-agenzia-partner/
- Agency testimonial (B2B-06): fictional Italian travel agency name + agent first name — realistic, Lorenzo replaces with a real one post-launch
- Agency registration form: Tally embed (TALLY_B2B_URL from config.php); if URL is empty, show fallback "Contattaci su WhatsApp" button using WHATSAPP_B2B_FALLBACK

**Waitlist form (DEST-07):**
- When a destination has 0 published trips: show sold-out dark box with name + email + phone form
- Custom PHP form — POSTs server-side via cURL to WAITLIST_WEBHOOK_URL (same pattern as api/submit-form.php)
- Fields: nome, email, telefono
- Success/error message inline, no page reload (AJAX fetch or PHP redirect)
- Submission includes destination name/slug so Lorenzo knows which destination the lead is for

**New config.php constants:**
- `WAITLIST_WEBHOOK_URL` — POST target for destination sold-out waitlist form (empty default)
- `TALLY_B2B_URL` — Tally embed URL for agency registration on agenzie.php (empty default)
- `WHATSAPP_B2B_FALLBACK` — WhatsApp link shown on agenzie.php if TALLY_B2B_URL is empty

### Claude's Discretion

- Exact Unsplash photo URLs for destination heroes and sub-destination cards
- Specific sub-destination names for each of the 6 destinations (e.g. America: New York, Grand Canyon, California, Las Vegas — or similar)
- Italian editorial copy content and phrasing
- Practical info values (real-world accurate data)
- Curiosita fact content per destination
- B2B page section copywriting (headings, sublines, how-it-works steps)
- Waitlist form AJAX vs PHP redirect approach

### Deferred Ideas (OUT OF SCOPE)

- Per-trip commission rate field in admin (visible on trip page to agencies) — Phase 6 admin panel
- Sub-destination detail pages — future phase or v2
</user_constraints>

---

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|-----------------|
| DEST-01 | destinazione.php reads ?slug= (america/asia/europa/africa/oceania/medio-oriente); 404 if invalid slug | Slug routing already established in viaggio.php — identical pattern. .htaccess already has `RewriteRule ^destinazione/([^/]+)/?$ destinazione.php?slug=$1` |
| DEST-02 | Full-viewport hero with stunning Unsplash photo and destination name overlay; breadcrumb: Home > Destinazioni > [Name] | Hero pattern established. hero_image comes from destinations-data.php. Breadcrumb is new HTML — simple nav element above the hero content |
| DEST-03 | Intro section: 3 paragraphs of inspiring editorial text (hardcoded in destinations-data.php per destination) | Loop over intro_paragraphs array from data file. Unsplash photos already used for all other pages — same approach |
| DEST-04 | Practical info boxes: currency, language, best season, timezone, visa requirements | New CSS component (info boxes). Data hardcoded in destinations-data.php per destination. Font Awesome icons for each box type |
| DEST-05 | "Cosa Vedere" section: 4 sub-destination cards with Unsplash photo, name, 2-line description; Boscolo-style layout | New CSS component reusing .trip-card visual DNA. 4-col desktop grid. Data from see_also array in destinations-data.php |
| DEST-06 | "Curiosita" section: 3 interesting facts with icon, title, text, gold left accent border | New CSS component. Data from curiosita array in destinations-data.php. Font Awesome icons, gold border-left accent |
| DEST-07 | Trips section: IF trips exist (published=true) show trip grid; IF no trips show waitlist dark box | Uses existing get_trips_by_continent(). Trip grid reuses .trip-card + .trip-grid. Waitlist form is new: AJAX POST to new api/submit-waitlist.php endpoint |
| B2B-01 | Dark hero: "Diventa Agenzia Partner" headline + subline | Standard hero pattern with $hero_page = true. Unique dark/dramatic photo |
| B2B-02 | Trust bar: "Garanzia scritta • Commissioni competitive • Supporto dedicato • Materiali marketing inclusi" | Simple horizontal bar — similar to urgency-bar but styled differently. Dots as separators |
| B2B-03 | 3 value prop cards: client guarantee, competitive commissions, ready catalog | Card grid — 3-col desktop. New CSS component or reuse .why-grid pattern from homepage |
| B2B-04 | How it works: 3 steps — Registrati → Ricevi il catalogo → Inizia a guadagnare | Numbered steps component. Simple horizontal flow desktop, vertical mobile |
| B2B-05 | Embedded Tally form for agency registration (URL from config.php) | Tally embed iframe pattern already in codebase (TALLY_CATALOG_URL for viaggi.php). Fallback WhatsApp button if TALLY_B2B_URL is empty |
| B2B-06 | Agency partner testimonial (placeholder) | Single testimonial card — can reuse .testimonial-card pattern from homepage |
</phase_requirements>

---

## Standard Stack

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| PHP | 8.x (SiteGround) | Server-side routing, data rendering | Project constraint — no Node.js available |
| Font Awesome 6 | 6.5.0 (CDN) | Icons for practical info boxes, curiosita, trust bar | Already loaded in header.php |
| Playfair Display + Inter | Google Fonts (CDN) | Typography | Already loaded in header.php |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| cURL (PHP) | Native | Webhook POST for waitlist form | Same pattern as api/submit-form.php |
| Tally | Embed (iframe) | Agency registration form | When TALLY_B2B_URL is configured in config.php |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| Inline AJAX for waitlist | PHP redirect with ?success=1 | AJAX is better UX (no page reload) — CONTEXT.md says either is acceptable |
| Tally iframe | Custom PHP form for agency reg | Tally is locked decision; custom form is the fallback when URL is empty |

**No installation required** — all dependencies already in place via CDN and PHP native.

---

## Architecture Patterns

### Recommended File Structure
```
viaggiacolbaffo/
├── destinazione.php          # Single template for all 6 destination slugs
├── agenzie.php               # B2B agency partnership page
├── includes/
│   ├── config.php            # ADD: WAITLIST_WEBHOOK_URL, TALLY_B2B_URL, WHATSAPP_B2B_FALLBACK
│   └── destinations-data.php # NEW: PHP array keyed by slug with all editorial content
├── api/
│   └── submit-waitlist.php   # NEW: cURL webhook endpoint for waitlist form
└── assets/css/
    └── style.css             # APPEND: Phase 5 CSS block at end of file
```

### Pattern 1: Slug Routing (established — replicate from viaggio.php)
**What:** Read `?slug=` from GET, validate against known slugs, 404 on invalid
**When to use:** destinazione.php

```php
// Source: viaggio.php (established pattern)
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';
require_once ROOT . '/includes/destinations-data.php';

$slug = $_GET['slug'] ?? '';
$valid_slugs = ['america', 'asia', 'europa', 'africa', 'oceania', 'medio-oriente'];
if (!$slug || !in_array($slug, $valid_slugs)) {
    header("Location: /404");
    exit;
}
$dest = $destinations[$slug];
```

### Pattern 2: destinations-data.php Array Structure
**What:** Single PHP file returning an associative array — same pattern as trips.json but as PHP for performance and type safety
**When to use:** All destination content lives here

```php
// Source: CONTEXT.md locked decision
<?php
$destinations = [
    'america' => [
        'name'             => 'America',
        'hero_image'       => 'https://images.unsplash.com/...',
        'intro_paragraphs' => [
            'Paragraph 1 text...',
            'Paragraph 2 text...',
            'Paragraph 3 text...',
        ],
        'practical_info'   => [
            ['icon' => 'fa-solid fa-coins',      'label' => 'Valuta',        'value' => 'Dollaro USA (USD)'],
            ['icon' => 'fa-solid fa-language',   'label' => 'Lingua',        'value' => 'Inglese'],
            ['icon' => 'fa-solid fa-sun',        'label' => 'Stagione Migliore', 'value' => 'Aprile – Ottobre'],
            ['icon' => 'fa-solid fa-clock',      'label' => 'Fuso Orario',   'value' => 'UTC-5 a UTC-8'],
            ['icon' => 'fa-solid fa-passport',   'label' => 'Visto',         'value' => 'ESTA (online, €14)'],
        ],
        'see_also'         => [
            ['name' => 'New York',     'image' => 'https://images.unsplash.com/...', 'description' => '2-line description'],
            ['name' => 'Grand Canyon', 'image' => 'https://images.unsplash.com/...', 'description' => '2-line description'],
            ['name' => 'California',   'image' => 'https://images.unsplash.com/...', 'description' => '2-line description'],
            ['name' => 'Las Vegas',    'image' => 'https://images.unsplash.com/...', 'description' => '2-line description'],
        ],
        'curiosita'        => [
            ['icon' => 'fa-solid fa-flag',   'title' => 'Title', 'text' => 'Fact text...'],
            ['icon' => 'fa-solid fa-route',  'title' => 'Title', 'text' => 'Fact text...'],
            ['icon' => 'fa-solid fa-star',   'title' => 'Title', 'text' => 'Fact text...'],
        ],
    ],
    // ... 5 more destinations
];
```

### Pattern 3: Conditional Trips/Waitlist (DEST-07)
**What:** Check get_trips_by_continent() — show trip grid if results, show waitlist form if empty. Note: function returns ALL trips including unpublished — must filter to published only.
**When to use:** Bottom of destinazione.php

```php
// Note: get_trips_by_continent() does NOT filter by published.
// Must apply published filter manually (same as homepage and catalog).
$continent_trips = array_values(array_filter(
    get_trips_by_continent($slug),
    fn($t) => $t['published'] === true
));
$has_trips = count($continent_trips) > 0;
```

### Pattern 4: Waitlist API (api/submit-waitlist.php)
**What:** Simplified clone of api/submit-form.php — accepts nome/email/telefono/destination_slug, POSTs JSON to WAITLIST_WEBHOOK_URL via cURL
**When to use:** AJAX POST target from waitlist form on destinazione.php

```php
// Source: api/submit-form.php (clone pattern)
// Key difference: uses WAITLIST_WEBHOOK_URL constant, not per-trip webhook_url
// Graceful degradation: if WAITLIST_WEBHOOK_URL is empty, return success (no crash)
$webhook_url = defined('WAITLIST_WEBHOOK_URL') ? WAITLIST_WEBHOOK_URL : '';
if (!$webhook_url) {
    echo json_encode(['success' => true, 'note' => 'no_webhook']);
    exit;
}
```

### Pattern 5: Tally Embed (B2B-05)
**What:** Conditional iframe — shown when TALLY_B2B_URL defined and non-empty, replaced by WhatsApp button otherwise
**When to use:** Agency registration section in agenzie.php

```php
// Source: viaggi.php Tally pattern (TALLY_CATALOG_URL)
<?php if (defined('TALLY_B2B_URL') && TALLY_B2B_URL): ?>
  <iframe src="<?= htmlspecialchars(TALLY_B2B_URL) ?>"
          width="100%" height="600" frameborder="0" style="border:none;">
  </iframe>
<?php elseif (defined('WHATSAPP_B2B_FALLBACK') && WHATSAPP_B2B_FALLBACK): ?>
  <a href="<?= htmlspecialchars(WHATSAPP_B2B_FALLBACK) ?>"
     class="btn btn--gold" target="_blank" rel="noopener">
    <i class="fa-brands fa-whatsapp"></i> Contattaci su WhatsApp
  </a>
<?php endif; ?>
```

### Pattern 6: Hero Page (established)
**What:** Set `$hero_page = true` before `require_once header.php` — triggers `body.has-hero` class which makes header transparent until scrolled
**When to use:** Both destinazione.php and agenzie.php

```php
$hero_page = true;
require_once ROOT . '/includes/header.php';
```

### Anti-Patterns to Avoid
- **Using get_trips_by_continent() without published filter:** The function returns all trips including unpublished drafts — always wrap with array_filter published===true
- **Hardcoding commission percentage on agenzie.php:** Never write "12%" or any specific number — "commissioni competitive" only (locked decision)
- **Creating separate JS files:** All page-specific JS must be inline in the PHP file (established pattern)
- **Modifying header.php or footer.php:** Only append to style.css and add new files
- **Using relative paths:** Always use ROOT constant for file includes
- **Missing defined() check for config constants:** Always check `defined('CONSTANT_NAME') && CONSTANT_NAME` before use — config.php may not define new constants on old deployments

---

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Trip cards on destination page | Custom card HTML | Exact same `.trip-card` markup as homepage/catalog | Consistency; CSS already written |
| Image hosting | Upload infrastructure | Unsplash direct URLs | Established pattern; no upload infra in v1 |
| Agency registration form | Custom multi-field PHP form | Tally embed (TALLY_B2B_URL) | Lorenzo already manages Tally; fallback WhatsApp handles no-config state |
| Webhook submission | New HTTP client | cURL pattern from api/submit-form.php | Already tested, handles errors, supports graceful degradation |

**Key insight:** This phase is almost entirely content + CSS + template wiring. The one truly new technical component is `api/submit-waitlist.php`, which is ~40 lines cloned from `api/submit-form.php`.

---

## Common Pitfalls

### Pitfall 1: get_trips_by_continent() Returns Unpublished Trips
**What goes wrong:** Destination page shows draft/unpublished trips to visitors, or incorrectly shows "no trips" waitlist when unpublished trips exist
**Why it happens:** `get_trips_by_continent()` in functions.php has no published filter — it returns all matching trips regardless of `published` field
**How to avoid:** Always filter: `array_filter(get_trips_by_continent($slug), fn($t) => $t['published'] === true)`
**Warning signs:** Test with a destination that has only unpublished trips — should show waitlist, not trip cards

### Pitfall 2: Missing .htaccess Clean URL for /destinazione/
**What goes wrong:** `/destinazione/america` returns 404 before PHP even runs
**Why it happens:** Clean URL rewrite must exist in .htaccess — this one already exists: `RewriteRule ^destinazione/([^/]+)/?$ destinazione.php?slug=$1 [L,QSA]`
**How to avoid:** Already in .htaccess — no action needed. Verify it when testing.
**Warning signs:** Direct .htaccess check confirms rule exists at line 9

### Pitfall 3: config.php Missing New Constants
**What goes wrong:** PHP fatal error on `WAITLIST_WEBHOOK_URL` or `TALLY_B2B_URL` undefined constant
**Why it happens:** New constants must be added to config.php; existing deployments may not have them
**How to avoid:** Always use `defined('CONSTANT') ? CONSTANT : ''` guard, AND add the constants to config.php in the same plan
**Warning signs:** PHP warnings about undefined constants

### Pitfall 4: Tally iframe Height on Mobile
**What goes wrong:** Tally form iframe is too short on mobile, showing scroll bar inside the iframe
**Why it happens:** Fixed height 600px doesn't adapt to form content on small screens
**How to avoid:** Use `width: 100%; min-height: 500px;` and test on mobile. Tally also supports `?transparentBackground=1` URL parameter for seamless embedding.
**Warning signs:** iframe has internal scroll on 375px viewport

### Pitfall 5: CSS Block Ordering in style.css
**What goes wrong:** Phase 5 CSS overwrites Phase 4 styles or creates specificity conflicts
**Why it happens:** CSS appended without a clear section comment, rules accidentally collide with existing class names
**How to avoid:** Append Phase 5 CSS as a clearly commented block at the very end of style.css, using unique class prefixes: `.dest-page-`, `.dest-hero-`, `.dest-info-`, `.dest-cosa-`, `.dest-curiosita-`, `.b2b-`
**Warning signs:** Existing pages visually break after Phase 5 CSS is added

### Pitfall 6: Breadcrumb Link to /destinazioni (Non-Existent Page)
**What goes wrong:** Breadcrumb "Home > Destinazioni > America" — clicking "Destinazioni" 404s because there's no destinazioni.php list page in v1
**Why it happens:** Footer already links to /destinazioni but no such page exists yet
**How to avoid:** Breadcrumb "Destinazioni" text can be plain text (not a link) or link back to homepage. Do NOT create a /destinazioni list page — that's out of scope for Phase 5.
**Warning signs:** 404 error when clicking breadcrumb middle link

---

## Code Examples

Verified patterns from existing codebase:

### Waitlist Form AJAX Pattern (inline JS in destinazione.php)
```javascript
// Pattern: same as quote form AJAX in viaggio.php
document.getElementById('waitlist-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.textContent = 'Invio in corso...';

    const data = new FormData(this);
    try {
        const res = await fetch('/api/submit-waitlist.php', { method: 'POST', body: data });
        const json = await res.json();
        if (json.success) {
            document.getElementById('waitlist-msg').textContent = 'Grazie! Ti contatteremo appena disponibile.';
            document.getElementById('waitlist-msg').className = 'form-msg form-msg--success';
            this.reset();
        } else {
            throw new Error(json.error || 'Errore');
        }
    } catch (err) {
        document.getElementById('waitlist-msg').textContent = err.message;
        document.getElementById('waitlist-msg').className = 'form-msg form-msg--error';
    } finally {
        btn.disabled = false;
        btn.textContent = 'Iscriviti alla lista d\'attesa';
    }
});
```

### cURL Webhook (api/submit-waitlist.php skeleton)
```php
// Source: api/submit-form.php clone pattern
<?php
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}
require_once __DIR__ . '/../includes/config.php';

$allowed = ['nome', 'email', 'telefono', 'destination_slug', 'destination_name'];
$data = [];
foreach ($allowed as $key) {
    if (isset($_POST[$key])) {
        $data[$key] = htmlspecialchars(strip_tags(trim($_POST[$key])));
    }
}

$webhook_url = defined('WAITLIST_WEBHOOK_URL') ? WAITLIST_WEBHOOK_URL : '';
if (!$webhook_url) {
    echo json_encode(['success' => true, 'note' => 'no_webhook']);
    exit;
}

$payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$ch = curl_init($webhook_url);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Content-Length: ' . strlen($payload)],
]);
$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err  = curl_error($ch);
curl_close($ch);

if ($curl_err || $http_code < 200 || $http_code >= 300) {
    echo json_encode(['success' => false, 'error' => 'Errore di invio. Riprova o contattaci su WhatsApp.']);
    exit;
}
echo json_encode(['success' => true]);
```

### B2B Written Guarantee Copy (from live site, verified)
```
// Source: WebFetch of viaggiacolbaffo.com/diventa-agenzia-partner/ (2026-03-06)

Headline: "I tuoi clienti restano tuoi, sempre"

Guarantee text:
"Non contatteremo mai direttamente i tuoi clienti per proporre i nostri servizi."
"Se in futuro un tuo cliente dovesse prenotare direttamente con noi (senza passare
dalla tua agenzia), ti riconosceremo comunque la tua commissione."

Trust points from live site:
- "Commissioni interessanti su ogni prenotazione confermata"
- "I tuoi clienti restano tuoi, sempre"
- "Garanzia Clienti" (Garanzia Scritta)
- "Supporto dedicato"
- "Preventivi in 24h"
```

### Practical Info Box Component (new CSS)
```css
/* === PHASE 5: DESTINATIONS + B2B === */

.dest-info-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}
@media (min-width: 480px) { .dest-info-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 900px) { .dest-info-grid { grid-template-columns: repeat(5, 1fr); } }

.dest-info-box {
  background: var(--dark-card);
  border-radius: var(--radius);
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 0.5rem;
}
.dest-info-box__icon { font-size: 1.5rem; color: var(--gold); }
.dest-info-box__label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--grey); }
.dest-info-box__value { font-size: 0.95rem; font-weight: 600; color: var(--white); }
```

### Curiosita Card with Gold Accent Border
```css
.dest-curiosita-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}
@media (min-width: 768px) { .dest-curiosita-grid { grid-template-columns: repeat(3, 1fr); } }

.dest-curiosita-card {
  background: var(--dark-card);
  border-radius: var(--radius);
  padding: 1.5rem 1.5rem 1.5rem 1.75rem;
  border-left: 4px solid var(--gold);
}
.dest-curiosita-card__icon { font-size: 1.75rem; color: var(--gold); margin-bottom: 0.75rem; }
.dest-curiosita-card__title { font-family: var(--font-heading); font-size: 1.1rem; margin-bottom: 0.5rem; }
.dest-curiosita-card__text { color: rgba(255,255,255,0.75); font-size: 0.95rem; line-height: 1.65; }
```

### Sub-destination Card ("Cosa Vedere") — 4-col grid
```css
.dest-cosa-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.25rem;
}
@media (min-width: 600px)  { .dest-cosa-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .dest-cosa-grid { grid-template-columns: repeat(4, 1fr); } }

/* Reuses .trip-card visual DNA — same dark card, photo top, text below */
.dest-cosa-card {
  display: block;
  border-radius: var(--radius);
  overflow: hidden;
  background: var(--dark-card);
  text-decoration: none;
  transition: var(--transition);
}
.dest-cosa-card:hover { transform: translateY(-4px); box-shadow: 0 8px 32px rgba(0,0,0,0.6); }
.dest-cosa-card__img { width: 100%; aspect-ratio: 3/4; object-fit: cover; display: block; }
.dest-cosa-card__body { padding: 1rem; }
.dest-cosa-card__name { font-family: var(--font-heading); font-size: 1.05rem; color: var(--white); margin-bottom: 0.35rem; }
.dest-cosa-card__desc { font-size: 0.85rem; color: var(--grey); line-height: 1.5; }
```

---

## Sub-Destination Suggestions (Claude's Discretion)

Recommended sub-destinations per continent (4 each). These are travel-iconic choices that match Lorenzo's luxury small-group style:

| Continent | Sub-destination 1 | Sub-destination 2 | Sub-destination 3 | Sub-destination 4 |
|-----------|-------------------|-------------------|-------------------|-------------------|
| america | New York | Grand Canyon | California | Patagonia |
| asia | Giappone | Bali | Thailandia | Vietnam |
| europa | Islanda | Portogallo | Grecia | Scozia |
| africa | Marocco | Namibia | Tanzania | Madagascar |
| oceania | Australia | Nuova Zelanda | Fiji | Tasmania |
| medio-oriente | Giordania | Oman | Dubai | Israele |

---

## Practical Info Reference Data (real-world accurate)

| Destination | Currency | Language | Best Season | Timezone | Visa |
|-------------|----------|----------|-------------|----------|------|
| America | USD | Inglese | Aprile – Ottobre | UTC-5 a UTC-8 | ESTA (online, ~€14) |
| Asia | Varia per paese | Varia | Ottobre – Aprile | UTC+7 a UTC+9 | Varia per paese |
| Europa | EUR / locale | Varia | Maggio – Settembre | UTC+0 a UTC+3 | Nessun visto (UE) |
| Africa | Varia | Varia | Giugno – Ottobre (safari) | UTC+0 a UTC+3 | Varia per paese |
| Oceania | AUD / NZD | Inglese | Dicembre – Marzo | UTC+8 a UTC+13 | ETA Australia (online) |
| Medio Oriente | USD / locale | Arabo | Ottobre – Aprile | UTC+2 a UTC+4 | Varia per paese |

---

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Separate CSS file per page | Appended blocks in single style.css | Phase 1 decision | Phase 5 appends one new block at end of file |
| jQuery AJAX | Native fetch() API | Phase 4 established | Use fetch() for waitlist form — no jQuery on site |
| PHP redirect for forms | Inline AJAX with JSON response | Phase 4 established | Waitlist uses same AJAX pattern (or PHP redirect — discretion) |

---

## Open Questions

1. **Footer `/destinazioni` link (no list page)**
   - What we know: Footer already links to `/destinazioni` (a destination listing page that doesn't exist)
   - What's unclear: Should Phase 5 create a minimal destinazioni.php list page, or leave the link broken temporarily?
   - Recommendation: Create a minimal `destinazioni.php` that lists all 6 destination cards (linking to `/destinazione/[slug]`) — it's ~30 lines of PHP using destinations-data.php and takes negligible effort. This is not explicitly required by any DEST-XX requirement but prevents a broken footer link.

2. **Waitlist form: AJAX vs PHP redirect**
   - What we know: CONTEXT.md explicitly marks this as Claude's Discretion
   - What's unclear: No preference stated
   - Recommendation: Use AJAX fetch() — consistent with the quote form in Phase 4 (viaggio.php), better UX, no page reload. Implement inline JS in destinazione.php (no separate .js file per established pattern).

3. **B2B testimonial agency name**
   - What we know: Must be fictional Italian travel agency + agent first name, realistic enough that Lorenzo just replaces it later
   - Recommendation: "Agenzia Viaggi Rossi — Marco Rossi, titolare" or similar — common Italian surname, sounds real.

---

## Validation Architecture

nyquist_validation is enabled in .planning/config.json.

### Test Framework
| Property | Value |
|----------|-------|
| Framework | None — PHP project with no automated test runner |
| Config file | None |
| Quick run command | Manual: `curl -s http://localhost/destinazione/america` and inspect output |
| Full suite command | Manual browser verification per DEST-XX and B2B-XX checklist |

### Phase Requirements → Test Map
| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|-------------|
| DEST-01 | Invalid slug redirects to 404 | smoke | `curl -v http://localhost/destinazione/invalid-slug 2>&1 \| grep "Location:"` | ❌ Wave 0 |
| DEST-01 | Valid slug loads page | smoke | `curl -s http://localhost/destinazione/america \| grep -q "America"` | ❌ Wave 0 |
| DEST-02 | Hero renders with destination name | manual | Browser: verify hero image + destination name visible | N/A |
| DEST-03 | 3 intro paragraphs render | smoke | `curl -s http://localhost/destinazione/asia \| grep -c "<p class=\"dest-intro"` | ❌ Wave 0 |
| DEST-04 | 5 practical info boxes render | smoke | `curl -s http://localhost/destinazione/europa \| grep -c "dest-info-box"` | ❌ Wave 0 |
| DEST-05 | 4 sub-destination cards render | smoke | `curl -s http://localhost/destinazione/africa \| grep -c "dest-cosa-card"` | ❌ Wave 0 |
| DEST-06 | 3 curiosita cards render | smoke | `curl -s http://localhost/destinazione/oceania \| grep -c "dest-curiosita-card"` | ❌ Wave 0 |
| DEST-07 | Continent with trips shows trip grid | manual | Browser: load /destinazione/america — check trip cards visible |  N/A |
| DEST-07 | Continent with no trips shows waitlist | manual | Temporarily unpublish all trips for a continent, verify waitlist appears | N/A |
| B2B-01 | B2B hero loads | smoke | `curl -s http://localhost/agenzie \| grep -q "Diventa Agenzia Partner"` | ❌ Wave 0 |
| B2B-02 | Trust bar renders | smoke | `curl -s http://localhost/agenzie \| grep -q "Garanzia scritta"` | ❌ Wave 0 |
| B2B-03 | 3 value prop cards render | smoke | `curl -s http://localhost/agenzie \| grep -c "b2b-value-card"` | ❌ Wave 0 |
| B2B-04 | How-it-works 3 steps | smoke | `curl -s http://localhost/agenzie \| grep -c "b2b-step"` | ❌ Wave 0 |
| B2B-05 | Tally fallback shows WhatsApp btn | smoke | `curl -s http://localhost/agenzie \| grep -q "WhatsApp"` (when TALLY_B2B_URL empty) | ❌ Wave 0 |
| B2B-06 | Testimonial renders | smoke | `curl -s http://localhost/agenzie \| grep -q "testimonial"` | ❌ Wave 0 |

**Note:** PHP CLI is not available in bash on this environment (established in STATE.md). All curl-based smoke tests require a running local server or deployed site. Verification must use content inspection of PHP source files as fallback (grep patterns in PHP output).

### Sampling Rate
- **Per task commit:** Manual content inspection of rendered PHP output
- **Per wave merge:** Full browser walkthrough of all 6 destination slugs + agenzie
- **Phase gate:** All 6 slugs load correctly, invalid slug 404s, waitlist toggle verified, B2B Tally fallback verified before `/gsd:verify-work`

### Wave 0 Gaps
- [ ] No automated test infrastructure exists — smoke tests are curl-based manual checks
- [ ] No local PHP server configured — tests require deployed site or local Apache/PHP setup

---

## Sources

### Primary (HIGH confidence)
- Existing codebase (viaggio.php, api/submit-form.php, includes/functions.php) — established patterns verified by file read
- .htaccess — clean URL rule for `/destinazione/` confirmed at line 9
- assets/css/style.css — CSS variables, class names, and section structure verified

### Secondary (MEDIUM confidence)
- WebFetch of viaggiacolbaffo.com/diventa-agenzia-partner/ (2026-03-06) — B2B guarantee copy extracted; site content may change before Phase 5 is built

### Tertiary (LOW confidence)
- Practical info reference data (visa requirements, currencies, seasons) — based on training data; real-world accurate for stable facts but should be spot-checked before launch

---

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH — all dependencies already in project, no new installs
- Architecture patterns: HIGH — all patterns verified against existing PHP files
- Editorial content: MEDIUM — practical info is accurate but Lorenzo should review Italian copy quality
- Pitfalls: HIGH — all verified against actual codebase behavior and established decisions in STATE.md

**Research date:** 2026-03-06
**Valid until:** 2026-04-06 (stable domain — PHP patterns won't change; Tally embed API is stable)
