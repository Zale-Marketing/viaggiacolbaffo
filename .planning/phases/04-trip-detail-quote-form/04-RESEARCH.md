# Phase 4: Trip Detail + Quote Form - Research

**Researched:** 2026-03-06
**Domain:** PHP single-trip page, vanilla JS accordion/lightbox/form, OpenAI API integration
**Confidence:** HIGH

<user_constraints>
## User Constraints (from CONTEXT.md)

### Locked Decisions

**Page layout**
- Full-viewport dark hero with `$hero_page = true` (transparent header, same pattern as homepage/catalog)
- Sticky tab bar after scrolling past hero: tabs for Itinerario, Cosa Include, Galleria, Richiedi Preventivo
- Sections flow full-width sequentially: hero → sticky nav → itinerary → includes/excludes → gallery → quote form
- No sticky sidebar — the quote form is a bottom section only

**Itinerary accordion**
- Default state: Day 1 open, all others collapsed
- Single-open behavior: opening a new day closes the previous one
- Visual treatment per row: navy circle pill with zero-padded day number on the left, bold title text, chevron on the right that rotates 90° on open
- CSS transition on expand/collapse (var(--transition))

**Gallery**
- Thumbnail grid: masonry-style 3-column layout (CSS columns or grid with `grid-auto-rows: auto`), 2-col tablet, 1-col mobile
- Lightbox: custom vanilla JS, no external library
  - Opens full-screen on thumbnail click
  - Navigation: left/right arrow buttons + keyboard ← / → + swipe (touchstart/touchend delta)
  - Photo counter displayed top-right: "2 / 4"
  - Esc key or click outside to close
  - Dark overlay background

**Quote form**
- Positioned as a full-width bottom section, dark background (`var(--dark-card)`), centered at ~700px max-width
- B2B/B2C toggle at top (default: B2C "Privato"; when agency code validates, suggest switching to B2B)
- Participant inputs: Adult counter (+ / - buttons, min 1); Children counter (+ / - buttons, min 0); each child added reveals an age input field
- Room type: select driven by `form_config.room_types` (slug, label, price_delta)
- Add-ons: checkboxes driven by `form_config.addons` — insurance is always one of them
- Agency code field (visible when B2B / "Agenzia" selected): validated client-side with SHA-256 hash comparison against a stored hash in `form_config` or `config.php`; on valid code, agency-specific fields appear: Nome agenzia, Codice IATA (optional), Città / Provincia
- Live price estimate box (visually distinct — navy border or highlighted): shows total and per-person breakdown, updates in real time as room type / participant count / add-ons change
- Competitor savings line: "Risparmia X€ rispetto al prezzo medio" — driven by `form_config.competitor_price`
- `form_config` pricing constants: `price_per_person`, `single_supplement`, `third_bed_price`, `fourth_bed_price`, `competitor_benchmark`
- Form submission: fetch() POST to `trip.webhook_url` (or fallback to `DEFAULT_WEBHOOK_URL` in config.php); no page reload; success message replaces form on success; error message inline on failure

**AI form generator endpoint**
- File: `api/generate-form.php`
- Method: POST, JSON body: `{ "description": "..." }` (plain Italian text)
- If `OPENAI_API_KEY` is empty in config.php: return a default `form_config` derived from the trip's `price_from` value (sensible defaults for room_types, standard fields list, no addons)
- If key present: call GPT-4o-mini with a prompt instructing it to infer room_types, addons, pricing constants, and fields from the description; return structured JSON matching the `form_config` schema
- Response: `{ "success": true, "form_config": { ... } }` or `{ "success": false, "error": "..." }`
- No authentication on the endpoint in Phase 4 — Phase 6 admin panel will call it from behind auth

### Claude's Discretion
- Exact sticky nav transition behavior (background appears on scroll, same scroll-listener pattern as header)
- Includes/excludes list visual treatment (checkmark icons for included, X icons for excluded — Font Awesome 6)
- Hero content layout (title, dates, duration, price_from, status pill, CTA "Richiedi Preventivo" scrolls to form)
- Lightbox CSS overlay and photo sizing details
- GPT-4o-mini system prompt wording
- 404 redirect logic for invalid slugs (header("Location: /404") or inline error page)

### Deferred Ideas (OUT OF SCOPE)
- Admin UI for the AI form generator (textarea + JSON preview) — Phase 6 admin panel
- Saving generated form_config back to trips.json through the UI — Phase 6
</user_constraints>

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|-----------------|
| TRIP-01 | PHP reads trip from trips.json by ?slug= parameter; 404 redirect if slug not found | `get_trip_by_slug()` in functions.php already handles slug lookup; .htaccess RewriteRule `^viaggio/([^/]+)/?$` already routes to `viaggio.php?slug=$1` |
| TRIP-02 | Full-viewport hero: trip hero_image, dark gradient overlay, trip title in large Playfair, dates + duration + price, status pill | `.hero`, `.hero__overlay`, `.hero__content` CSS classes already defined; status pill classes (`.status--confermata` etc.) already in style.css |
| TRIP-03 | Sticky top bar (appears after scrolling past hero): trip name left, "Richiedi Preventivo" gold button right (scrolls to form section) | Scroll listener pattern already in header.php (80px threshold); needs new `.trip-sticky-bar` CSS element with IntersectionObserver or scroll offset check |
| TRIP-04 | Highlights bar: 4 info boxes — Date, Durata, Da €X.XXX, Posti status | New `.trip-highlights` component; data comes from `$trip` array fields |
| TRIP-05 | Sticky tab navigation: Itinerario, Cosa Include, Galleria, Richiedi Preventivo — clicking scrolls smoothly to section | Requires `position: sticky` tab bar below highlights; `scroll-behavior: smooth` + JS `scrollIntoView()` or `offsetTop` calculation |
| TRIP-06 | Itinerary accordion — each day clickable row with expand/collapse; timeline visual with gold dots on left | Vanilla JS toggle pattern; CSS max-height transition for smooth expand/collapse; `var(--transition)` for animation |
| TRIP-07 | Cosa Include section: two columns — green checkmarks for included items, red X for excluded items | Font Awesome `fa-check` (green) and `fa-xmark` (red/accent) icons; PHP loops over `$trip['included']` and `$trip['excluded']` arrays |
| TRIP-08 | Galleria section: CSS masonry grid; click opens pure CSS/JS lightbox (no external library) | CSS `columns` property for masonry; vanilla JS lightbox with touchstart/touchend delta for swipe, keydown for arrow/esc |
| TRIP-09 | Tags section: "Questo viaggio è perfetto per:" — all trip tags as gold pill links to viaggi.php?tag= | PHP loop over `$trip['tags']`; styled as `.tag-pill` links to `/viaggi?per={tag}` or `/viaggi?tipo={tag}` |
| TRIP-10 | Related trips section: 3 cards of trips sharing same continent or overlapping tags | PHP: load all trips, filter by same continent, exclude current trip, limit 3; use existing `.trip-card` markup |
| FORM-01 | Form renderer reads form_config from trip JSON and renders HTML form dynamically (PHP + JS) | `$trip['form_config']` already in data structure; PHP renders form fields; form_config schema needs new pricing constants added to West America entry |
| FORM-02 | JavaScript calculates live price total as user selects room type, addons, and participant count; displays "Preventivo stimato: €X.XXX" updating in real time | Pure JS event listeners on all form inputs; price state object updated on each change; formatted with `Number.toLocaleString('it-IT')` |
| FORM-03 | When tipo_cliente = "Agenzia" selected, additional fields appear: nome agenzia, P.IVA, commissione richiesta | JS toggle show/hide of `.agency-fields` div on select change; SHA-256 via `crypto.subtle.digest()` Web Crypto API for agency code validation |
| FORM-04 | Form submits via AJAX POST to /api/submit-form.php which forwards JSON to trip's webhook_url via cURL; shows success/error message without page reload | New `api/submit-form.php` endpoint (distinct from generate-form.php); PHP cURL POST to webhook_url; JSON response; fetch() on client |
| FORM-05 | Below form: WhatsApp button "Preferisci scrivere su WhatsApp?" linking to configured WhatsApp number | `WHATSAPP_NUMBER` constant in config.php; build `https://wa.me/{number}` URL (strip spaces and +); Font Awesome WhatsApp icon |
| FORM-06 | Admin AI form generator: textarea for Lorenzo to describe trip in plain Italian; "Genera Form con AI" button calls GPT-4o-mini API; returns form_config JSON; admin previews generated form; edits webhook_url; saves to trips.json | `api/generate-form.php` endpoint in Phase 4; admin UI in Phase 6 (deferred) |
</phase_requirements>

---

## Summary

Phase 4 is the largest single-file build in the project: `viaggio.php` hosts the full single-trip experience across six sequential sections, plus a companion `api/generate-form.php` endpoint and a new `api/submit-form.php` endpoint. The codebase is mature — three prior phases established all shared infrastructure (CSS variables, header/footer includes, `$hero_page` transparent-header pattern, `.section`/`.container` layout primitives, `.trip-card` markup, and functions.php data API). This phase builds on that foundation without modifying shared files, except for one data update: extending the West America `form_config` in trips.json with pricing constant fields.

All JavaScript must be vanilla — the project explicitly prohibits external JS libraries. PHP is the only server-side language; no Node, no npm build step, no framework. The three principal JS systems — accordion, lightbox, and live-pricing form — are entirely self-contained and inline within `viaggio.php`'s closing `<script>` block per the established per-page pattern. The one exception allowed by project patterns is that `api/` endpoints are separate PHP files (not inline).

The most complex subsystem is the quote form: it combines live price calculation (multi-variable), B2B/B2C conditional field sets, per-child age inputs rendered dynamically by a counter, SHA-256 agency code validation using the Web Crypto API (available in all modern browsers, no library needed), and a webhook submission with fetch(). The AI endpoint is simpler — a thin PHP wrapper around the OpenAI Chat Completions API using file_get_contents/stream_context or cURL, with a graceful fallback when no API key is configured.

**Primary recommendation:** Build viaggio.php as a single atomic PHP file with all CSS in a page-scoped `<style>` block (or appended to the shared stylesheet) and all JS in a `<script>` block at the bottom. Keep each interactive system (accordion, lightbox, form) as a named IIFE to avoid global scope collisions. Write api/submit-form.php and api/generate-form.php as separate PHP files.

---

## Standard Stack

### Core
| Component | Version/Source | Purpose | Why Standard |
|-----------|---------------|---------|--------------|
| PHP | 8.x (SiteGround default) | Page render, data load, API endpoints | Hosting constraint; already in use |
| Vanilla JS | ES2020+ (no transpile) | Accordion, lightbox, form logic | Project constraint: no external JS |
| CSS custom properties | Already defined in style.css | All design tokens | Established by Phase 1 |
| Web Crypto API (`crypto.subtle`) | Browser-native, all modern browsers | SHA-256 for agency code | No library needed; widely supported |
| fetch() API | Browser-native | AJAX form submission | No library needed |
| OpenAI Chat Completions API | REST, model: gpt-4o-mini | AI form_config generation | Decided in CONTEXT.md |
| PHP cURL / file_get_contents | PHP built-in | Server-side OpenAI call | SiteGround shared hosting compatible |

### Supporting
| Component | Version | Purpose | When to Use |
|-----------|---------|---------|-------------|
| Font Awesome 6.5.0 | Already loaded in header.php | `fa-check`, `fa-xmark`, `fa-chevron-right`, `fa-whatsapp`, `fa-arrow-left`, `fa-arrow-right` icons | All icon needs in this phase |
| Playfair Display | Already loaded via Google Fonts | Trip title in hero, section headers | Per design system |
| CSS `columns` property | CSS3 native | Masonry gallery layout | Simplest masonry without JS |
| `IntersectionObserver` | Browser-native | Detect when hero exits viewport for sticky bars | More reliable than scroll offset math |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| CSS `columns` masonry | CSS Grid with `grid-row: span N` + JS heights | Grid masonry is more controllable but requires JS measurement; `columns` works without JS and is sufficient for static image grids |
| `IntersectionObserver` for sticky detection | `scroll` event + `offsetTop` math | Both work; IntersectionObserver is more performant (no main-thread scroll polling), but scroll offset is simpler and consistent with existing header pattern in header.php |
| `crypto.subtle.digest()` SHA-256 | bcrypt / server-side check | Server-side is more secure but adds a round-trip; for agency code validation (low-stakes, code is essentially a shared password), client-side SHA-256 is acceptable per CONTEXT.md decision |
| OpenAI cURL in PHP | OpenAI PHP SDK | SDK is not available via Composer on shared hosting without effort; raw cURL/file_get_contents is simpler, zero dependencies |

**Installation:** None required. All tools are built-in to PHP and the browser.

---

## Architecture Patterns

### Recommended File Structure
```
viaggio.php                    # Main trip detail page (new — primary deliverable)
api/
  generate-form.php            # AI form_config generator endpoint (new)
  submit-form.php              # Quote form webhook proxy endpoint (new)
  .gitkeep                     # Already exists — remove or keep
data/
  trips.json                   # UPDATE: add pricing constants to West America form_config
```

No new directories needed. No new CSS files — page-specific styles go in a `<style>` block at the top of viaggio.php (or appended to style.css under a `/* === PHASE 4: TRIP DETAIL === */` comment — both patterns are acceptable; inline style block avoids touching shared CSS).

### Pattern 1: Hero with $hero_page Flag (Established)
**What:** Set `$hero_page = true` before including header.php to get transparent-to-scrolled header.
**When to use:** Every full-viewport hero page.
```php
<?php
require_once __DIR__ . '/includes/config.php';
require_once ROOT . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$trip = get_trip_by_slug($slug);
if (!$trip) {
    header('Location: /404');
    exit;
}

$page_title = htmlspecialchars($trip['title']) . ' — Viaggia col Baffo';
$hero_page  = true;
require_once ROOT . '/includes/header.php';
?>
```

### Pattern 2: Sticky Element Appearing on Scroll
**What:** Element hidden by default, shown after scrolling past a threshold using a scroll listener (same pattern as `header.php`'s `.scrolled` class).
**When to use:** Sticky top bar (TRIP-03) and sticky tab nav (TRIP-05).
```javascript
// Source: Established pattern from includes/header.php
(function() {
  var stickyBar  = document.getElementById('trip-sticky-bar');
  var tabNav     = document.getElementById('trip-tab-nav');
  var heroHeight = document.querySelector('.trip-hero').offsetHeight;

  window.addEventListener('scroll', function() {
    var pastHero = window.scrollY > heroHeight - 80;
    stickyBar.classList.toggle('is-visible', pastHero);
    tabNav.classList.toggle('is-visible', pastHero);
  }, { passive: true });
})();
```

### Pattern 3: Accordion (Single-Open)
**What:** Clicking a day row expands its content; previously open row closes.
**When to use:** TRIP-06 itinerary section.
```javascript
(function() {
  var items = document.querySelectorAll('.accordion-item');
  var openItem = items[0]; // Day 1 open by default

  function openAccordion(item) {
    if (openItem && openItem !== item) {
      openItem.classList.remove('is-open');
      openItem.querySelector('.accordion-body').style.maxHeight = null;
    }
    item.classList.add('is-open');
    item.querySelector('.accordion-body').style.maxHeight =
      item.querySelector('.accordion-body').scrollHeight + 'px';
    openItem = item;
  }

  items.forEach(function(item) {
    item.querySelector('.accordion-trigger').addEventListener('click', function() {
      if (item === openItem) {
        item.classList.remove('is-open');
        item.querySelector('.accordion-body').style.maxHeight = null;
        openItem = null;
      } else {
        openAccordion(item);
      }
    });
  });

  if (items.length) openAccordion(items[0]);
})();
```
CSS: `.accordion-body { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }`

### Pattern 4: Vanilla JS Lightbox
**What:** Full-screen overlay with image, prev/next, keyboard, swipe, counter.
**When to use:** TRIP-08 gallery section.
```javascript
(function() {
  var images  = Array.from(document.querySelectorAll('.gallery-thumb'));
  var overlay = document.getElementById('lightbox');
  var imgEl   = document.getElementById('lightbox-img');
  var counter = document.getElementById('lightbox-counter');
  var current = 0;
  var touchStartX = 0;

  function show(idx) {
    current = (idx + images.length) % images.length;
    imgEl.src = images[current].dataset.full;
    counter.textContent = (current + 1) + ' / ' + images.length;
    overlay.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function close() {
    overlay.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  images.forEach(function(img, i) {
    img.addEventListener('click', function() { show(i); });
  });

  document.getElementById('lb-prev').addEventListener('click', function() { show(current - 1); });
  document.getElementById('lb-next').addEventListener('click', function() { show(current + 1); });
  overlay.addEventListener('click', function(e) { if (e.target === overlay) close(); });
  document.getElementById('lb-close').addEventListener('click', close);

  document.addEventListener('keydown', function(e) {
    if (!overlay.classList.contains('is-open')) return;
    if (e.key === 'ArrowLeft')  show(current - 1);
    if (e.key === 'ArrowRight') show(current + 1);
    if (e.key === 'Escape')     close();
  });

  overlay.addEventListener('touchstart', function(e) {
    touchStartX = e.touches[0].clientX;
  }, { passive: true });
  overlay.addEventListener('touchend', function(e) {
    var delta = e.changedTouches[0].clientX - touchStartX;
    if (Math.abs(delta) > 50) show(current + (delta < 0 ? 1 : -1));
  });
})();
```

### Pattern 5: Live Price Calculation
**What:** State object mirrors all form inputs; recalculated on every change event.
**When to use:** FORM-02 live price estimate.
```javascript
// PHP outputs the form_config as a JS object
var CONFIG = <?php echo json_encode($trip['form_config']); ?>;

var state = {
  adults: 1,
  children: 0,
  roomSlug: CONFIG.room_types[0]?.slug ?? 'doppia',
  addons: [],
  isB2B: false
};

function calcPrice() {
  var base = CONFIG.price_per_person * state.adults;
  var room = (CONFIG.room_types.find(function(r) { return r.slug === state.roomSlug; }) || {}).price_delta || 0;
  var addonsTotal = state.addons.reduce(function(sum, slug) {
    var a = CONFIG.addons.find(function(a) { return a.slug === slug; });
    return sum + (a ? a.price * (state.adults + state.children) : 0);
  }, 0);
  return base + room + addonsTotal;
}

function updatePriceDisplay() {
  var total = calcPrice();
  var perPerson = Math.round(total / Math.max(1, state.adults));
  document.getElementById('price-total').textContent = total.toLocaleString('it-IT') + ' €';
  document.getElementById('price-per-person').textContent = perPerson.toLocaleString('it-IT') + ' €';
  if (CONFIG.competitor_benchmark) {
    var saving = CONFIG.competitor_benchmark - total;
    document.getElementById('competitor-saving').textContent =
      saving > 0 ? 'Risparmia ' + saving.toLocaleString('it-IT') + '€ rispetto al prezzo medio' : '';
  }
}
```

### Pattern 6: SHA-256 Agency Code Validation (Web Crypto)
**What:** Hash the entered code client-side, compare against stored hash.
**When to use:** FORM-03 B2B agency code field.
```javascript
// CONFIG.agency_code_hash set by PHP from form_config or config
async function validateAgencyCode(code) {
  var encoder = new TextEncoder();
  var data = encoder.encode(code.trim().toUpperCase());
  var hashBuffer = await crypto.subtle.digest('SHA-256', data);
  var hashArray = Array.from(new Uint8Array(hashBuffer));
  var hashHex = hashArray.map(function(b) { return b.toString(16).padStart(2, '0'); }).join('');
  return hashHex === CONFIG.agency_code_hash;
}
```
Note: `crypto.subtle` requires HTTPS. The site forces HTTPS via .htaccess, so this is safe.

### Pattern 7: fetch() Form Submission
**What:** POST JSON payload to api/submit-form.php; swap form for success message on 200.
**When to use:** FORM-04.
```javascript
document.getElementById('quote-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  var btn = document.getElementById('submit-btn');
  btn.disabled = true;
  btn.textContent = 'Invio in corso...';
  try {
    var payload = collectFormData(); // builds object from form state
    var resp = await fetch('/api/submit-form.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    var data = await resp.json();
    if (data.success) {
      document.getElementById('quote-form-wrap').innerHTML =
        '<div class="form-success"><i class="fas fa-check-circle"></i>' +
        '<h3>Richiesta inviata!</h3><p>Lorenzo ti risponderà entro 24 ore.</p></div>';
    } else {
      document.getElementById('form-error').textContent = data.error || 'Errore nell\'invio. Riprova.';
      btn.disabled = false;
      btn.textContent = 'Invia Richiesta';
    }
  } catch (err) {
    document.getElementById('form-error').textContent = 'Errore di rete. Riprova.';
    btn.disabled = false;
    btn.textContent = 'Invia Richiesta';
  }
});
```

### Pattern 8: api/submit-form.php Webhook Proxy
**What:** Receive JSON from client, forward via cURL to webhook_url, return JSON response.
**When to use:** FORM-04 server-side endpoint.
```php
<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

// Determine webhook target
$webhook = !empty($data['webhook_url']) ? $data['webhook_url'] : DEFAULT_WEBHOOK_URL;
if (!$webhook) {
    echo json_encode(['success' => false, 'error' => 'Webhook not configured']);
    exit;
}

unset($data['webhook_url']); // Don't forward internal field

$ch = curl_init($webhook);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($data),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
]);
$result = curl_exec($ch);
$code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($result === false || $code >= 400) {
    echo json_encode(['success' => false, 'error' => 'Webhook delivery failed']);
    exit;
}

echo json_encode(['success' => true]);
```

### Pattern 9: api/generate-form.php OpenAI Endpoint
**What:** POST Italian description, return form_config JSON. Graceful fallback when no key.
**When to use:** FORM-06 endpoint (admin UI calls this in Phase 6).
```php
<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$description = trim($body['description'] ?? '');
$price_from  = (int)($body['price_from'] ?? 3000);

if (!$description) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'description required']);
    exit;
}

// Fallback: no OpenAI key
if (!OPENAI_API_KEY) {
    echo json_encode(['success' => true, 'form_config' => defaultFormConfig($price_from)]);
    exit;
}

// OpenAI call
$systemPrompt = 'Sei un assistente che genera configurazioni JSON per moduli di preventivo viaggi. ' .
    'Dato una descrizione di un viaggio in italiano, restituisci SOLO un oggetto JSON valido con questa struttura: ' .
    '{"price_per_person": number, "single_supplement": number, "third_bed_price": number, ' .
    '"fourth_bed_price": number, "competitor_benchmark": number, ' .
    '"room_types": [{"slug":"...","label":"...","price_delta": number}], ' .
    '"addons": [{"slug":"...","label":"...","price": number}], ' .
    '"fields": ["nome","cognome","email","telefono","tipo_cliente","numero_partecipanti","room_type","note"]}. ' .
    'Assicurazione viaggio deve essere sempre il primo addon. Non aggiungere testo fuori dal JSON.';

$payload = json_encode([
    'model'    => 'gpt-4o-mini',
    'messages' => [
        ['role' => 'system',  'content' => $systemPrompt],
        ['role' => 'user',    'content' => $description],
    ],
    'response_format' => ['type' => 'json_object'],
    'max_tokens' => 800,
]);

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY,
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
]);
$result = curl_exec($ch);
$code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code !== 200 || !$result) {
    echo json_encode(['success' => false, 'error' => 'OpenAI API error: HTTP ' . $code]);
    exit;
}

$response   = json_decode($result, true);
$formConfig = json_decode($response['choices'][0]['message']['content'] ?? '{}', true);

if (!$formConfig) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON from AI']);
    exit;
}

echo json_encode(['success' => true, 'form_config' => $formConfig]);

function defaultFormConfig(int $price): array {
    return [
        'price_per_person'    => $price,
        'single_supplement'   => (int)($price * 0.2),
        'third_bed_price'     => (int)($price * -0.1),
        'fourth_bed_price'    => (int)($price * -0.15),
        'competitor_benchmark'=> (int)($price * 1.15),
        'room_types' => [
            ['slug' => 'doppia',  'label' => 'Camera Doppia',  'price_delta' => 0],
            ['slug' => 'singola', 'label' => 'Camera Singola', 'price_delta' => (int)($price * 0.2)],
            ['slug' => 'tripla',  'label' => 'Camera Tripla',  'price_delta' => -(int)($price * 0.08)],
        ],
        'addons' => [
            ['slug' => 'assicurazione', 'label' => 'Assicurazione viaggio completa', 'price' => 180],
        ],
        'fields' => ['nome','cognome','email','telefono','tipo_cliente','numero_partecipanti','room_type','note'],
    ];
}
```

### Pattern 10: trips.json form_config Extension
**What:** Add the required pricing constants to the West America entry's `form_config`.
**When to use:** Data update task (FORM-01, FORM-02).

The existing `form_config` for West America must be extended with:
```json
{
  "price_per_person": 3490,
  "single_supplement": 650,
  "third_bed_price": -200,
  "fourth_bed_price": -350,
  "competitor_benchmark": 4200,
  "agency_code_hash": "<SHA-256 of agency code>",
  "room_types": [...existing...],
  "addons": [...existing...],
  "fields": [...existing...]
}
```

### Pattern 11: Related Trips (TRIP-10)
```php
<?php
$all = array_filter(load_trips(), fn($t) =>
    $t['published'] &&
    $t['slug'] !== $trip['slug'] &&
    ($t['continent'] === $trip['continent'] ||
     count(array_intersect($t['tags'] ?? [], $trip['tags'] ?? [])) > 0)
);
$related = array_slice(array_values($all), 0, 3);
?>
```

### Anti-Patterns to Avoid
- **Separate JS files for page-specific logic:** The project uses inline `<script>` per page. Do not create `/assets/js/viaggio.js`.
- **CSS `grid-template-rows: masonry`:** This is an experimental feature with poor browser support as of 2026. Use CSS `columns` property instead.
- **Opening new window for lightbox:** The lightbox must be an in-page overlay, not `window.open()`.
- **Using `scrollIntoView({ behavior: 'smooth' })` without CSS `scroll-behavior: smooth`:** Either approach works; pick one consistently. `scrollIntoView` with behavior option is simpler.
- **Checking `form_config` is non-empty before rendering the quote form — sold-out trips suppress it:** The Japan trip has `"form_config": {}`. The viaggio.php template must check `!empty($trip['form_config']['fields'])` (or similar) before rendering the form widget. Sold-out trips should show a "Sold Out — join waitlist" message instead.
- **Modifying shared `style.css` for every page-specific component:** Prefer a `<style>` block at the top of viaggio.php for all Phase 4 page-specific CSS to avoid polluting the shared stylesheet and to keep the plan atomic.

---

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| SHA-256 hashing | Custom hash function or CRC | `crypto.subtle.digest('SHA-256', ...)` | Browser-native Web Crypto API; correct, no library needed |
| Webhook relay | Direct client-to-webhook call | `api/submit-form.php` server-side proxy | Direct client call exposes webhook URL; CORS will block cross-origin fetches from most webhook providers |
| Number formatting for Italian locale | Manual string manipulation | `Number.toLocaleString('it-IT')` | Handles thousands separators (1.234,00) correctly for Italian |
| OpenAI response_format enforcement | Prompt engineering alone | `"response_format": {"type": "json_object"}` | GPT-4o-mini supports JSON mode; ensures parseable response |
| Masonry layout | JavaScript height measurement + positioning | CSS `columns` property | Pure CSS, no reflow-thrashing JS required |
| body scroll lock during lightbox | `overflow: hidden` on html | `document.body.style.overflow = 'hidden'` + restore on close | Simpler; works consistently on mobile and desktop |

**Key insight:** Every "complex" frontend problem in this phase has a browser-native or CSS-native solution. The project constraint against external libraries is not a hardship here — the Web APIs are sufficient for everything required.

---

## Common Pitfalls

### Pitfall 1: Sticky Tab Bar Z-Index Conflict with Lightbox
**What goes wrong:** The lightbox overlay (z-index should be ~1000) renders below the sticky tab nav (z-index ~200 if set carelessly).
**Why it happens:** Both elements use `position: fixed` or `position: sticky`. Without explicit z-index stacking order, browser painting order determines layering.
**How to avoid:** Set z-index deliberately: header = 100, sticky trip bar = 90, sticky tab nav = 80, lightbox overlay = 1000. The lightbox must win.
**Warning signs:** Sticky nav partially visible through lightbox, or lightbox nav arrows unreachable.

### Pitfall 2: form_config Empty Object on Sold-Out Trips
**What goes wrong:** The Japan trip has `"form_config": {}`. If viaggio.php unconditionally renders the quote form section, it will crash or produce an empty form.
**Why it happens:** Established by Phase 1 decision: "sold-out trips suppress the Phase 4 quote form widget."
**How to avoid:** Guard all form_config access: `$hasForm = !empty($trip['form_config']['fields']);`. Render either the form section or a sold-out waitlist message, never both, never neither.
**Warning signs:** PHP notices for undefined index `fields` on Japan trip page.

### Pitfall 3: crypto.subtle Not Available on HTTP
**What goes wrong:** `crypto.subtle` is only available in secure contexts (HTTPS). On localhost or HTTP, it is `undefined`.
**Why it happens:** Browser security restriction on sensitive cryptographic APIs.
**How to avoid:** The production site forces HTTPS via .htaccess — this is not a production issue. For local development, test over HTTPS or use a fallback that skips hash validation on non-secure context. Add a guard: `if (!crypto || !crypto.subtle) { /* show error or skip validation */ }`.
**Warning signs:** `TypeError: Cannot read properties of undefined (reading 'digest')` in browser console on non-HTTPS dev environment.

### Pitfall 4: Accordion Max-Height Transition Requires Known Height
**What goes wrong:** `max-height: 0` to `max-height: auto` does not animate — CSS cannot tween to `auto`.
**Why it happens:** CSS `transition` requires both values to be numeric.
**How to avoid:** Set `max-height` to the element's `scrollHeight` in pixels via JS on open: `el.style.maxHeight = el.scrollHeight + 'px'`. On close, set to `null` (which reverts to CSS-defined `max-height: 0`).
**Warning signs:** Accordion snaps open/closed with no animation despite CSS transition being defined.

### Pitfall 5: Sticky Tab Bar Anchor Scroll Lands Under the Bar
**What goes wrong:** Clicking "Itinerario" tab scrolls to the section, but the section header is hidden behind the sticky tab nav and sticky site header.
**Why it happens:** `scrollIntoView()` and `href="#section"` anchor links scroll the element to the very top of the viewport, not accounting for sticky elements above.
**How to avoid:** Use JS to calculate target offset and subtract sticky heights: `window.scrollTo({ top: el.offsetTop - 160, behavior: 'smooth' })` (80px header + 80px tab nav). Or use CSS `scroll-margin-top: 160px` on each section.
**Warning signs:** Clicking tabs always shows the section title buried under navigation.

### Pitfall 6: fetch() to Webhook Without proxy Returns CORS Error
**What goes wrong:** If the client tries to POST directly to a third-party webhook URL (Make.com, Zapier, n8n, etc.), the browser blocks the request with a CORS error.
**Why it happens:** Cross-origin requests require the target server to emit `Access-Control-Allow-Origin` headers. Most webhook services do not do this for direct browser requests.
**How to avoid:** Always proxy through `api/submit-form.php`. The PHP server-to-server cURL call is not subject to CORS restrictions.
**Warning signs:** Network panel shows OPTIONS preflight request failing with CORS error.

### Pitfall 7: Per-Child Age Inputs Accumulate in DOM on Counter Decrease
**What goes wrong:** Clicking "–" on child counter hides or removes age input visually, but the hidden inputs still serialize and get sent in the form payload.
**Why it happens:** Removing DOM nodes requires explicit JS; `display: none` does not prevent form serialization.
**How to avoid:** When decreasing child count, `remove()` the last age input from DOM rather than hiding it. When collecting form data for submission, rebuild the array from current DOM state.
**Warning signs:** Form payload contains more `child_age_N` fields than the displayed child count.

### Pitfall 8: WhatsApp URL Contains Spaces or Plus Sign
**What goes wrong:** `WHATSAPP_NUMBER` is stored as `+39 333 1234567`. The wa.me URL requires a digit-only format: `https://wa.me/393331234567`.
**Why it happens:** Stored format is human-readable; URL format requires no spaces or +.
**How to avoid:** In PHP: `preg_replace('/[^0-9]/', '', WHATSAPP_NUMBER)` to strip all non-digits before building the URL.
**Warning signs:** WhatsApp link opens an error page saying "phone number not found."

---

## Code Examples

### CSS: Masonry Gallery Grid
```css
/* Source: CSS columns property — MDN Web Docs */
.gallery-grid {
  columns: 3;
  column-gap: 1rem;
}

.gallery-grid__item {
  break-inside: avoid;
  margin-bottom: 1rem;
}

.gallery-grid__img {
  width: 100%;
  display: block;
  border-radius: 8px;
  cursor: pointer;
  transition: opacity 0.2s ease;
}

.gallery-grid__img:hover {
  opacity: 0.85;
}

@media (max-width: 1023px) { .gallery-grid { columns: 2; } }
@media (max-width: 767px)  { .gallery-grid { columns: 1; } }
```

### CSS: Accordion Chevron Rotation
```css
/* Source: established pattern using var(--transition) */
.accordion-trigger .chevron {
  transition: transform var(--transition);
}

.accordion-item.is-open .accordion-trigger .chevron {
  transform: rotate(90deg);
}
```

### CSS: Lightbox Overlay
```css
.lightbox {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.92);
  z-index: 1000;
  align-items: center;
  justify-content: center;
}

.lightbox.is-open {
  display: flex;
}

.lightbox__img {
  max-width: 90vw;
  max-height: 85vh;
  object-fit: contain;
  border-radius: 4px;
}

.lightbox__counter {
  position: absolute;
  top: 1rem;
  right: 1.5rem;
  color: rgba(255,255,255,0.8);
  font-size: 0.9rem;
}
```

### PHP: Status Label Helper (Already Used in Prior Pages)
```php
<?php
function statusLabel(string $status): string {
    return match($status) {
        'confermata'   => 'Confermata',
        'ultimi-posti' => 'Ultimi Posti',
        'sold-out'     => 'Sold Out',
        'programmata'  => 'Programmata',
        default        => ucfirst($status),
    };
}
?>
```

### PHP: Safe WhatsApp URL
```php
<?php
$wa_number = preg_replace('/[^0-9]/', '', WHATSAPP_NUMBER);
$wa_url    = 'https://wa.me/' . $wa_number;
?>
<a href="<?= htmlspecialchars($wa_url) ?>" target="_blank" rel="noopener" class="btn btn--outline-white">
  <i class="fab fa-whatsapp"></i> Preferisci scrivere su WhatsApp?
</a>
```

### PHP: Related Trips with Continent-First Priority
```php
<?php
$all_trips = load_trips();
$same_continent = [];
$other_related  = [];

foreach ($all_trips as $t) {
    if (!$t['published'] || $t['slug'] === $trip['slug']) continue;
    if ($t['continent'] === $trip['continent']) {
        $same_continent[] = $t;
    } elseif (count(array_intersect($t['tags'] ?? [], $trip['tags'] ?? [])) > 0) {
        $other_related[] = $t;
    }
}

$related = array_slice(array_merge($same_continent, $other_related), 0, 3);
?>
```

---

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|-----------------|--------------|--------|
| External lightbox libraries (Fancybox, GLightbox) | Custom vanilla JS lightbox | Phase 4 decision (project constraint) | Eliminates JS dependency, saves ~30KB |
| Separate `.js` file per page | Inline `<script>` at page bottom | Established Phase 2 pattern | All page logic in one file, easier to trace |
| Bootstrap modal for accordions | CSS `max-height` transition + vanilla JS | Phase 4 decision | Zero framework dependency |
| jQuery $.ajax for form submission | Native `fetch()` + `async/await` | Current standard (2024+) | Cleaner, no jQuery dependency |
| OpenAI `text-davinci-003` | `gpt-4o-mini` with `response_format: json_object` | gpt-4o-mini released May 2024 | More reliable JSON output, cheaper, faster |

**Deprecated/outdated:**
- `XMLHttpRequest` for AJAX: replaced by `fetch()` — use `fetch()` for FORM-04
- `grid-template-rows: masonry` (CSS grid masonry): still experimental/flagged in all browsers as of 2026 — use CSS `columns` instead
- `SHA-1` for hashing: deprecated for security use — use SHA-256 via Web Crypto
- OpenAI `functions` parameter: superseded by `tools` and `response_format: json_object` — use `response_format` for cleaner JSON generation

---

## Open Questions

1. **Agency code hash storage location**
   - What we know: CONTEXT.md says "validated client-side with SHA-256 hash comparison against a stored hash in `form_config` or `config.php`."
   - What's unclear: Should one global agency code hash live in `config.php` (applies to all trips), or per-trip in `form_config.agency_code_hash`?
   - Recommendation: Store in `form_config.agency_code_hash` (per-trip) as the primary source. If absent, fall back to a `AGENCY_CODE_HASH` constant in config.php. This gives flexibility for per-trip agency codes in the future while keeping a global fallback. The planner should make this concrete.

2. **404 handling for invalid slug**
   - What we know: `.htaccess` does not define a custom 404 error document. `header("Location: /404")` redirects to a non-existent `/404` page.
   - What's unclear: Is there a `404.php` in the project? The ls output shows none.
   - Recommendation: Use `http_response_code(404)` + display an inline error message (matching site design) rather than redirecting, since `/404` does not exist. Alternatively, create a minimal `404.php` as part of this phase. The planner should include a task for one of these two options.

3. **Addon price calculation basis (per-person or flat)**
   - What we know: The West America addons show `"price": 180` for insurance and `"price": 450` for premium economy. The CONTEXT.md live price description is ambiguous about whether addons are per-person or flat.
   - What's unclear: Insurance is typically per-person; flight upgrades (premium economy) are typically per-person too. But the price calculation code example above assumes flat per-addon.
   - Recommendation: Treat addon price as per-person (multiply by `adults + children`). This matches typical travel industry convention and gives higher perceived value. The planner should make this explicit in the price calculation task.

---

## Validation Architecture

### Test Framework
| Property | Value |
|----------|-------|
| Framework | Manual browser testing (no automated test framework detected in project) |
| Config file | none — SiteGround shared hosting, no test runner configured |
| Quick run command | Load `http://localhost/viaggio/west-america-aprile-2026` in browser |
| Full suite command | Manual checklist per requirement ID |

No `pytest.ini`, `jest.config.*`, `vitest.config.*`, `phpunit.xml`, or `tests/` directory detected in the project. The project is pure PHP + vanilla JS on shared hosting with no build tooling. All validation is manual browser testing and PHP syntax checks.

### Phase Requirements → Test Map
| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|-------------|
| TRIP-01 | Slug lookup returns trip; invalid slug shows 404 | manual | `php -l viaggio.php` (syntax only) | ❌ Wave 0 |
| TRIP-02 | Full-viewport hero renders with correct trip data | manual-visual | — | ❌ Wave 0 |
| TRIP-03 | Sticky top bar appears after scrolling past hero | manual-visual | — | ❌ Wave 0 |
| TRIP-04 | Highlights bar shows correct date, duration, price, status | manual-visual | — | ❌ Wave 0 |
| TRIP-05 | Tab nav scrolls to correct section with offset | manual-interactive | — | ❌ Wave 0 |
| TRIP-06 | Accordion: Day 1 open by default; single-open behavior | manual-interactive | — | ❌ Wave 0 |
| TRIP-07 | Includes/excludes two-column layout with correct icons | manual-visual | — | ❌ Wave 0 |
| TRIP-08 | Gallery masonry layout; lightbox opens, navigates, closes | manual-interactive | — | ❌ Wave 0 |
| TRIP-09 | Tag pills render with correct links | manual-visual | — | ❌ Wave 0 |
| TRIP-10 | Related trips show same-continent trips, limit 3 | manual-visual | — | ❌ Wave 0 |
| FORM-01 | form_config renders correct fields and options | manual-visual | — | ❌ Wave 0 |
| FORM-02 | Live price updates on every input change | manual-interactive | — | ❌ Wave 0 |
| FORM-03 | B2B toggle reveals agency fields; code validates | manual-interactive | — | ❌ Wave 0 |
| FORM-04 | Fetch POST to api/submit-form.php; success state shows | manual-interactive | `php -l api/submit-form.php` | ❌ Wave 0 |
| FORM-05 | WhatsApp button links to correct wa.me URL | manual-visual | — | ❌ Wave 0 |
| FORM-06 | api/generate-form.php returns valid form_config JSON | manual + curl | `php -l api/generate-form.php` + `curl -X POST ...` | ❌ Wave 0 |

### Sampling Rate
- **Per task commit:** `php -l {file}.php` (syntax check for PHP files)
- **Per wave merge:** Full manual browser walkthrough of viaggio/west-america-aprile-2026
- **Phase gate:** All 16 requirements manually verified before `/gsd:verify-work`

### Wave 0 Gaps
- [ ] `viaggio.php` — primary file, does not exist yet — covers all TRIP-* reqs
- [ ] `api/submit-form.php` — does not exist — covers FORM-04
- [ ] `api/generate-form.php` — does not exist — covers FORM-06
- [ ] `data/trips.json` form_config update for West America — covers FORM-01, FORM-02

---

## Sources

### Primary (HIGH confidence)
- Direct codebase inspection: `includes/config.php`, `includes/functions.php`, `includes/header.php`, `assets/css/style.css`, `data/trips.json`, `.htaccess`, `index.php`, `viaggi.php` — all patterns confirmed by reading actual project files
- MDN Web Docs (CSS `columns` property) — masonry layout approach
- MDN Web Docs (`crypto.subtle.digest`) — SHA-256 in browser
- OpenAI API reference (gpt-4o-mini, `response_format: json_object`) — confirmed supported as of training cutoff Aug 2025

### Secondary (MEDIUM confidence)
- `.planning/phases/04-trip-detail-quote-form/04-CONTEXT.md` — all locked decisions confirmed verbatim
- `.planning/REQUIREMENTS.md` — requirement descriptions cross-referenced

### Tertiary (LOW confidence)
- CSS Grid `masonry` support: marked as experimental/flagged in browsers as of 2026 — this claim is based on training data (Aug 2025); verify at caniuse.com if implementation date is much later than March 2026

---

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH — all tools confirmed in existing codebase; PHP, vanilla JS, Web Crypto, fetch() all verifiable from project files
- Architecture: HIGH — patterns extracted directly from three prior phases of working code
- Pitfalls: HIGH — CORS/proxy, crypto.subtle HTTPS requirement, accordion max-height, sold-out trip guard all verified from known browser/PHP behaviors
- AI endpoint: MEDIUM — OpenAI API structure verified against training knowledge (Aug 2025); gpt-4o-mini JSON mode confirmed stable at that date

**Research date:** 2026-03-06
**Valid until:** 2026-04-06 (stable stack; OpenAI API section should be re-checked if implementation is delayed)
