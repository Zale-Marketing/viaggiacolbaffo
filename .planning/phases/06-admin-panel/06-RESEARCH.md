# Phase 6: Admin Panel - Research

**Researched:** 2026-03-06
**Domain:** PHP vanilla admin panel, session auth, JSON data management, Anthropic Claude API
**Confidence:** HIGH

## Summary

Phase 6 is a self-contained PHP admin panel built with the same tools already used throughout the project: vanilla PHP, vanilla JS, Font Awesome 6, no external frameworks. The codebase is mature — `save_trips()`, `load_trips()`, `load_tags()` already exist and follow an `flock`-based write pattern that all admin saves must replicate. The `admin/` directory is currently empty (only a `.gitkeep`), so everything is new.

The two external integrations are: (1) the Anthropic Messages API replacing the existing OpenAI call in `api/generate-form.php`, and (2) a new `data/admin-config.json` that `includes/config.php` must read at runtime to override its constants. Both are well-understood and straightforward.

The most complex moving parts are the HTML5 drag-and-drop reordering in two places (dashboard trip table rows, itinerary day rows), the tabbed edit form with a sticky footer save bar, and the migration of `includes/destinations-data.php` to `data/destinations.json`. All are achievable with vanilla JS and the existing PHP file-lock write pattern.

**Primary recommendation:** Work in file-by-file waves — auth/bootstrap first, then dashboard, then edit form tabs, then supporting pages (settings, tags, destinations). Each wave is independently testable by Lorenzo.

---

<user_constraints>
## User Constraints (from CONTEXT.md)

### Locked Decisions

**Visual style**
- Light/neutral admin style: white or light grey background, dark text, gold (#C9A84C) accents on primary action buttons only
- Separate admin CSS file — does NOT inherit the public site's dark palette
- Admin nav header: logo/site name, Pannello link, Impostazioni link, "Vai al sito" (opens public site in new tab), Logout button
- Font: Inter body throughout (no Playfair Display). Font Awesome 6 for icons.

**Edit form layout**
- Tabbed sections: Info Base | Media | Contenuto | Itinerario | Form Config
- JS tab switching (vanilla, inline per page)
- Save buttons (Salva Bozza / Pubblica / Anteprima) fixed at bottom / sticky footer, always visible

**Dashboard trip table**
- Columns: Title, Continent, Tags (chips), Status pill (Pubblicato / Bozza), Actions (Modifica | Anteprima | Elimina)
- Stats bar at top: total / published / draft counts
- "Crea Nuovo Viaggio" gold button prominent
- Rows drag-and-drop reorderable (HTML5 drag API) — position index saved to trips.json
- "Cestino" tab/collapsible section shows soft-deleted trips with Ripristina and Svuota Cestino

**Config/settings persistence**
- Settings saved to `data/admin-config.json` (NOT rewriting config.php)
- `includes/config.php` reads admin-config.json at runtime and uses those values if present, falling back to its own constants
- Editable fields: ANTHROPIC_API_KEY, DEFAULT_WEBHOOK_URL, Waitlist webhook, Partner/B2B webhook, WHATSAPP_NUMBER, TALLY_CATALOG_URL, TALLY_B2B_URL, Admin password (hashed), urgency bar text, company data (P.IVA, company name, address)

**Itinerary builder (ADMIN-06)**
- "Aggiungi Giorno" button appends a new row
- Day number auto-numbered by row position — reordering renumbers automatically
- Each row: auto day number (read-only), title text input, description textarea, up/down arrow buttons, drag handle (HTML5 drag), remove button
- Both drag-and-drop AND arrow buttons available

**Delete behavior**
- Soft delete only: `deleted: true` flag in trips.json
- Published trips: modal confirms unpublish before delete
- Draft trips: confirmation modal then soft-deleted
- Trash section: Ripristina (deleted: false) + Svuota Cestino (hard deletes all trashed)

**Tag management (admin/tags.php)**
- Tags grouped by category: continente, tipo viaggio, mese, per chi
- Add new tag inline: name input + category select + Add button
- Delete cascades: removes tag from all trips in trips.json
- Saves to data/tags.json

**Destination content editing (admin/destinations.php)**
- `includes/destinations-data.php` migrated to `data/destinations.json`
- `destinazione.php` reads JSON instead of PHP include
- Editable: hero image URL, intro paragraphs (3), practical info boxes, sub-destination cards (4), curiosità facts (3)
- 6 fixed slugs only — no add/remove

**AI form generator (ADMIN-09)**
- Uses **Anthropic Claude API** (`claude-sonnet-4-6`) — NOT OpenAI/GPT
- ANTHROPIC_API_KEY stored in admin-config.json
- Endpoint: `api/generate-form.php` — must be updated from OpenAI to Anthropic
- If key empty: return sensible default form_config (existing fallback)
- Admin UI: Italian description textarea → "Genera Form con AI" button → loading state → JSON preview → save

**Per-trip commission rate**
- Field: "Commissione agenzie (%)" — admin-only, stored as `commission_rate` in trips.json
- Never shown on public trip page
- Included in webhook payload for agency quote submissions

**Trip preview**
- Random `preview_token` generated on trip creation/first save, stored in trips.json
- `viaggio.php?slug=xxx&preview=TOKEN` renders even unpublished trips
- Token regenerable from edit-trip.php (button: "Rigenera token anteprima")
- ADMIN-10 "Anteprima" button appends `&preview=TOKEN` to viaggio.php URL

**Save actions (ADMIN-10)**
- "Salva Bozza" — saves with published: false
- "Pubblica" — saves with published: true
- "Anteprima" — opens `viaggio.php?slug=xxx&preview=TOKEN` in new tab (works for drafts)
- Auto-slug: derived from title on blur (lowercase, spaces to hyphens, accents stripped), editable before first save, locked after first publish

### Claude's Discretion
- Exact light admin CSS (color palette, table styling, form layout spacing)
- Tab switching JS implementation details
- Drag handle visual treatment
- Form validation UX (inline errors vs submit-time summary)
- admin-config.json schema structure and bootstrap defaults
- Migration script or manual copy for destinations-data.php → destinations.json

### Deferred Ideas (OUT OF SCOPE)
- Image upload in admin (URL-only throughout)
- Dynamic destination add/remove
- Blog/editorial section management
- Per-agency account management
</user_constraints>

---

<phase_requirements>
## Phase Requirements

| ID | Description | Research Support |
|----|-------------|-----------------|
| ADMIN-01 | admin/login.php: session auth, password from config.php; all admin pages redirect to login if not authenticated | PHP `session_start()` + `$_SESSION['admin']` pattern confirmed in existing code |
| ADMIN-02 | admin/index.php dashboard: trip table, stats bar, Crea Nuovo, Edit/Preview/Delete/Toggle actions | Reuses `load_trips()` — drag-and-drop via HTML5 dragstart/dragover/drop events |
| ADMIN-03 | admin/edit-trip.php: basic info tab — title, slug, continent, status, published toggle, dates, price | All fields map directly to trips.json schema — `save_trips()` reused |
| ADMIN-04 | Admin edit: media tab — hero image URL + live preview, gallery (one URL per line + preview grid) | Vanilla JS `oninput` image preview; gallery parsed by newline split |
| ADMIN-05 | Admin edit: content tab — short description (160 char counter), full description textarea | Character counter via `input` event listener |
| ADMIN-06 | Admin edit: itinerary builder — add/remove/reorder rows, auto day number, drag handle | HTML5 drag API or arrow buttons; same pattern as dashboard row reorder |
| ADMIN-07 | Admin edit: includes/excludes — two textareas, one item per line | Simplest tab — direct textarea to JSON array (split on newline) |
| ADMIN-08 | Admin edit: tag chip input — predefined tags as gold pills, custom tags, removable | Load `load_tags()` → render pills; JS toggle selected state; hidden input carries comma-separated slugs |
| ADMIN-09 | Admin AI form generator: Italian textarea → Claude API → JSON preview → save | `api/generate-form.php` rewritten to call Anthropic Messages API with `claude-sonnet-4-6` |
| ADMIN-10 | Admin edit: save actions — Salva Bozza, Pubblica, Anteprima (preview token URL in new tab) | `preview_token` = `bin2hex(random_bytes(16))`; viaggio.php updated to bypass published check when token matches |
| ADMIN-11 | admin/settings.php (was admin/config.php): stores all webhook URLs, API key, passwords, site content | Writes to `data/admin-config.json`; `includes/config.php` reads and overrides constants at runtime |
</phase_requirements>

---

## Standard Stack

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| PHP | 7.4+ (SiteGround) | Server logic, form processing, JSON reads/writes | Already the project language |
| Vanilla JS | ES6 | Tab switching, drag-and-drop, image preview, AJAX | Project constraint: no external JS libraries |
| Font Awesome 6.5 | CDN | Icons (grip, edit, trash, eye, etc.) | Already loaded in header.php — admin pages load FA separately |
| Inter (Google Fonts) | CDN | Admin body font | Locked decision: admin does not use Playfair Display |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| Anthropic Messages API | REST | AI form generation | `api/generate-form.php` only — replaces OpenAI call |
| PHP `flock` | built-in | Concurrent-safe JSON writes | All admin saves — matches existing `save_trips()` pattern |
| PHP `password_hash` / `password_verify` | built-in | Admin password storage | Settings page password change — hash before writing to admin-config.json |
| PHP `bin2hex(random_bytes(16))` | built-in | Preview token generation | Unique, cryptographically random 32-char hex string |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| Vanilla JS drag-and-drop | SortableJS | SortableJS is cleaner but project forbids external JS libraries |
| Flat-file admin-config.json | Rewrite config.php | Rewriting config.php risks breaking the server-side constant definitions; JSON overlay is safer |
| PHP sessions | HTTP Basic Auth via .htaccess | Sessions allow password management from UI; .htaccess would require server file access |

### Installation
No npm or composer installs. All dependencies are either built into PHP or loaded via CDN.

---

## Architecture Patterns

### Recommended File Structure
```
admin/
├── login.php              # ADMIN-01 — session auth entry point
├── index.php              # ADMIN-02 — trip dashboard
├── edit-trip.php          # ADMIN-03-10 — full trip edit form (tabbed)
├── settings.php           # ADMIN-11 — site settings + config
├── tags.php               # ADMIN tag management
├── destinations.php       # ADMIN destination content editing
└── admin.css              # Admin-only stylesheet (light palette, Inter)

api/
├── generate-form.php      # MODIFIED: OpenAI → Anthropic
└── (existing files unchanged)

data/
├── trips.json             # MODIFIED: adds position, deleted, preview_token, commission_rate fields
├── tags.json              # MODIFIED: adds category field per tag
├── admin-config.json      # NEW — site settings overlay
└── destinations.json      # NEW — migrated from includes/destinations-data.php

includes/
├── config.php             # MODIFIED: reads admin-config.json and overrides constants
└── functions.php          # MODIFIED: adds save_tags(), save_destinations(), load_destinations()

index.php                  # MODIFIED: urgency bar reads from admin-config.json
includes/footer.php        # MODIFIED: company data reads from admin-config.json
viaggio.php                # MODIFIED: preview token bypass for unpublished trips
```

### Pattern 1: Session Auth Guard (every admin page)
**What:** Every admin PHP file begins with session_start() + $_SESSION['admin'] check.
**When to use:** Every file under admin/ without exception. Also admin API endpoints if any.
**Example:**
```php
// Source: existing codebase pattern (confirmed in CONTEXT.md code_context)
<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: /admin/login.php');
    exit;
}
require_once __DIR__ . '/../includes/config.php';
require_once ROOT . '/includes/functions.php';
```

### Pattern 2: File-Locked JSON Write (replicate for all saves)
**What:** `fopen` + `flock(LOCK_EX)` + `fwrite` + `flock(LOCK_UN)` + `fclose`. Prevents race conditions on concurrent saves.
**When to use:** `save_tags()` and `save_destinations()` — identical to `save_trips()` in functions.php.
**Example:**
```php
// Source: existing functions.php save_trips() — HIGH confidence
function save_tags(array $tags): bool {
    $file = DATA_DIR . 'tags.json';
    $json = json_encode($tags, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $fp = fopen($file, 'w');
    if (!$fp) return false;
    flock($fp, LOCK_EX);
    fwrite($fp, $json);
    flock($fp, LOCK_UN);
    fclose($fp);
    return true;
}
```

### Pattern 3: Admin-Config Overlay in config.php
**What:** After defining all constants, config.php reads `data/admin-config.json` and overrides constants using a helper that redefines them only if not already overridden.
**When to use:** Single location — at the bottom of `includes/config.php`.
**Example:**
```php
// Source: pattern designed for this project — MEDIUM confidence
$_admin_cfg_file = DATA_DIR . 'admin-config.json';
if (file_exists($_admin_cfg_file)) {
    $_admin_cfg = json_decode(file_get_contents($_admin_cfg_file), true) ?? [];
    // Override constants only if value is non-empty in admin-config
    foreach ([
        'ANTHROPIC_API_KEY'  => $_admin_cfg['anthropic_api_key'] ?? '',
        'DEFAULT_WEBHOOK_URL'=> $_admin_cfg['default_webhook_url'] ?? '',
        'WHATSAPP_NUMBER'    => $_admin_cfg['whatsapp_number'] ?? '',
        'TALLY_CATALOG_URL'  => $_admin_cfg['tally_catalog_url'] ?? '',
        'TALLY_B2B_URL'      => $_admin_cfg['tally_b2b_url'] ?? '',
        'WAITLIST_WEBHOOK_URL'=> $_admin_cfg['waitlist_webhook_url'] ?? '',
    ] as $const => $val) {
        if ($val !== '' && !defined($const)) define($const, $val);
        // Note: define() fails silently if already defined — use a wrapper
    }
}
```
**Important:** PHP `define()` cannot redefine a constant. Use a `defined($const) || define($const, $val)` pattern, but since config.php calls define() first and then reads the JSON, the override must use a conditional: if admin-config value is non-empty, use it. Cleanest approach: load admin-config.json BEFORE calling define() for each constant, then define with the merged value.

**Revised approach (simpler and correct):**
```php
// Load admin overrides FIRST, then use merged values in define()
$_acfg = [];
$_acfg_file = __DIR__ . '/../data/admin-config.json';
if (file_exists($_acfg_file)) {
    $_acfg = json_decode(file_get_contents($_acfg_file), true) ?? [];
}
define('ANTHROPIC_API_KEY', $_acfg['anthropic_api_key'] ?? '');
define('DEFAULT_WEBHOOK_URL', $_acfg['default_webhook_url'] ?? '');
// ... etc
```

### Pattern 4: Anthropic Messages API (PHP cURL)
**What:** Replace OpenAI call in `api/generate-form.php` with Anthropic Messages API.
**When to use:** Only in `api/generate-form.php` — when ANTHROPIC_API_KEY is non-empty.
**Example:**
```php
// Source: https://platform.claude.com/docs/en/api/messages — HIGH confidence
$payload = json_encode([
    'model'      => 'claude-sonnet-4-6',
    'max_tokens' => 1024,
    'system'     => $system_prompt,
    'messages'   => [
        ['role' => 'user', 'content' => $description],
    ],
    'temperature' => 0.3,
], JSON_UNESCAPED_UNICODE);

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'x-api-key: ' . ANTHROPIC_API_KEY,
        'anthropic-version: 2023-06-01',
    ],
]);
$result    = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Extract text from Anthropic response (different from OpenAI)
$body    = json_decode($result, true);
$content = $body['content'][0]['text'] ?? '';  // NOT choices[0].message.content
```
**Critical difference from OpenAI:** Response path is `$body['content'][0]['text']` not `$body['choices'][0]['message']['content']`.

### Pattern 5: Preview Token in viaggio.php
**What:** If `?preview=TOKEN` matches `$trip['preview_token']`, render the trip even if `published === false`.
**When to use:** Add near the top of viaggio.php, after `$trip` is loaded.
**Example:**
```php
// Source: project design — MEDIUM confidence
$preview_token = $_GET['preview'] ?? '';
$is_preview = $preview_token && ($trip['preview_token'] ?? '') === $preview_token;
if (!$trip['published'] && !$is_preview) {
    header("Location: /404");
    exit;
}
```
**Note:** Currently viaggio.php has NO published check at all — any slug loads regardless of published state. The preview token pattern should be added alongside a proper published gate.

### Pattern 6: HTML5 Drag-and-Drop Row Reorder (vanilla JS)
**What:** `draggable="true"` on rows + `dragstart`/`dragover`/`drop` event listeners. On drop, read new order, update position indices, optionally POST to save.
**When to use:** Dashboard trip table + itinerary day builder.
**Example:**
```javascript
// Source: MDN Web Docs HTML Drag and Drop API — HIGH confidence
let dragSrc = null;
document.querySelectorAll('.draggable-row').forEach(row => {
    row.addEventListener('dragstart', e => {
        dragSrc = row;
        e.dataTransfer.effectAllowed = 'move';
    });
    row.addEventListener('dragover', e => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    });
    row.addEventListener('drop', e => {
        e.preventDefault();
        if (dragSrc !== row) {
            // Insert dragSrc before or after row based on pointer position
            row.parentNode.insertBefore(dragSrc, row);
            renumberRows(); // update day numbers / position indices
        }
    });
});
function renumberRows() {
    document.querySelectorAll('.draggable-row').forEach((row, i) => {
        row.querySelector('.day-num').textContent = i + 1;
        row.dataset.position = i;
    });
}
```

### Anti-Patterns to Avoid
- **Redefining PHP constants:** `define()` in config.php cannot be called twice for the same constant name. Load admin-config.json BEFORE the first `define()` calls, not after.
- **Storing plaintext passwords in admin-config.json:** Always hash with `password_hash($pwd, PASSWORD_DEFAULT)` before writing; verify with `password_verify()` at login.
- **Hardcoding the admin/ directory path:** Use `ROOT . '/admin/'` constants. admin pages use `__DIR__ . '/../includes/config.php'` for includes.
- **Using header.php / footer.php from admin pages:** Admin pages must NOT include the public site header.php (it loads style.css and sets up the dark public nav). Admin builds its own lightweight HTML wrapper with admin.css.
- **Missing `array_values()` on save:** `save_trips()` wraps `array_values()` — this ensures 0-indexed JSON array after deletions. `save_tags()` and `save_destinations()` must do the same.

---

## Don't Hand-Roll

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Password storage | Custom hash scheme | PHP `password_hash()` / `password_verify()` | bcrypt by default, handles salting, future-proof |
| Preview token generation | Sequential IDs or `rand()` | `bin2hex(random_bytes(16))` | Cryptographically secure, 32-char hex, effectively unguessable |
| JSON concurrent writes | Custom locking | `flock(LOCK_EX)` pattern from `save_trips()` | Already battle-tested in this project |
| Slug generation | Complex regex | `strtolower` + `preg_replace` + `iconv` transliteration | Simple, handles Italian accents (à è ì ò ù) |
| AI JSON-only responses | Parsing markdown fences | Anthropic API with tight system prompt + `json_decode` | Cleaner than strip-and-parse approach |

**Key insight:** PHP's built-in functions cover all security-sensitive operations. No custom crypto or session management needed.

---

## Common Pitfalls

### Pitfall 1: PHP `define()` Cannot Be Redefined
**What goes wrong:** config.php calls `define('ANTHROPIC_API_KEY', '')` then later tries to override it from admin-config.json — the second call is silently ignored, so the override never takes effect.
**Why it happens:** PHP constants are immutable after first definition. Unlike variables, you cannot reassign them.
**How to avoid:** Read admin-config.json at the TOP of config.php, before any `define()` calls. Then `define('ANTHROPIC_API_KEY', $_acfg['anthropic_api_key'] ?? '')`.
**Warning signs:** Settings page saves correctly (200 response) but config values don't change in the running app.

### Pitfall 2: Anthropic Response Structure vs OpenAI
**What goes wrong:** Code copies the OpenAI response parsing `$body['choices'][0]['message']['content']` — this returns null for Anthropic responses.
**Why it happens:** Anthropic's API has a fundamentally different response shape.
**How to avoid:** Use `$body['content'][0]['text']` for Anthropic. Also note: Anthropic does NOT support `response_format: {type: json_object}` the way OpenAI does — use a tight system prompt and validate the result manually.
**Warning signs:** generate-form.php always falls through to default_parse_fallback even with a valid API key.

### Pitfall 3: `published` Check Missing in viaggio.php
**What goes wrong:** Currently viaggio.php does not check `$trip['published']` — all slugs render regardless. After Phase 6 adds soft-delete (`deleted: true`), deleted trips will still render if this is not addressed.
**Why it happens:** Phase 4 built the page before admin existed.
**How to avoid:** Add two guards at the top of viaggio.php: (1) if `$trip['deleted'] ?? false` is true, 404; (2) if `!$trip['published']` and preview token doesn't match, 404.
**Warning signs:** Trash-can trips remain publicly accessible at their URL.

### Pitfall 4: Tag Deletion Cascade Race Condition
**What goes wrong:** Deleting a tag requires loading trips.json, filtering all trip `tags` arrays, then saving. If another save happens concurrently, one write overwrites the other.
**Why it happens:** Two separate JSON files must be updated atomically for a tag delete.
**How to avoid:** Load trips, filter in memory, save trips (with flock), then save tags (with flock). Keep operations sequential. Since admin is single-user (Lorenzo), true concurrency is very unlikely — but still use flock.

### Pitfall 5: Slug Locking After First Publish
**What goes wrong:** Lorenzo edits a published trip title → slug auto-regenerates → URL changes → all external links and Google rankings break.
**Why it happens:** Auto-slug JS runs on every blur of the title field if not gated.
**How to avoid:** On edit-trip.php, if `$trip['published'] === true`, render slug as a read-only `<input readonly>` (or plain text) and add a note: "Slug bloccato dopo la prima pubblicazione." Only allow slug editing for drafts (never-published trips). Add a JS variable `const slugLocked = <?= json_encode((bool)($trip['published'] ?? false)) ?>;` and gate the auto-slug function behind `if (!slugLocked)`.

### Pitfall 6: admin-config.json Not Created on First Run
**What goes wrong:** config.php tries `file_exists(DATA_DIR . 'admin-config.json')` — on first deploy, this file doesn't exist. The `file_exists` check handles this, but if code tries `json_decode(file_get_contents(...))` without the check, it logs an E_WARNING.
**How to avoid:** Always guard with `file_exists()` before reading. On settings.php save, create the file if it doesn't exist (the `fopen('w')` call creates it automatically).

### Pitfall 7: destinations.json Migration — destinations-data.php Still Loaded
**What goes wrong:** `destinazione.php` still has `require_once ROOT . '/includes/destinations-data.php'` after migration — both old PHP and new JSON are loaded, causing variable conflicts or stale data.
**Why it happens:** Migration requires updating the consumer (destinazione.php) and the producer simultaneously.
**How to avoid:** In the same plan that creates destinations.json and `load_destinations()`, update destinazione.php to call `load_destinations()` instead of requiring destinations-data.php. Keep destinations-data.php in place as backup but remove the require_once.

---

## Code Examples

Verified patterns from official sources and existing codebase:

### Login Handler (admin/login.php)
```php
// Source: project pattern — MEDIUM confidence (standard PHP session)
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../includes/config.php';
    $submitted = $_POST['password'] ?? '';
    // ADMIN_PASSWORD in config.php may be plaintext (legacy) or hash
    // After Phase 6, settings.php stores hashed password in admin-config.json
    $stored = ADMIN_PASSWORD;
    $ok = (substr($stored, 0, 4) === '$2y$')
        ? password_verify($submitted, $stored)
        : ($submitted === $stored);
    if ($ok) {
        $_SESSION['admin'] = true;
        header('Location: /admin/');
        exit;
    }
    $error = 'Password errata.';
}
```

### Slug Generator (JS, admin/edit-trip.php)
```javascript
// Source: project design — MEDIUM confidence
function generateSlug(title) {
    return title
        .toLowerCase()
        .normalize('NFD')                     // decompose accented chars
        .replace(/[\u0300-\u036f]/g, '')      // strip accent marks
        .replace(/[^a-z0-9\s-]/g, '')         // remove non-alphanumeric
        .trim()
        .replace(/\s+/g, '-');                // spaces to hyphens
}
const titleInput = document.getElementById('title');
const slugInput  = document.getElementById('slug');
titleInput.addEventListener('blur', () => {
    if (!slugLocked && !slugInput.value) {
        slugInput.value = generateSlug(titleInput.value);
    }
});
```

### Image URL Live Preview (JS)
```javascript
// Source: project design — HIGH confidence (standard DOM manipulation)
document.getElementById('hero_image').addEventListener('input', function() {
    const preview = document.getElementById('hero-preview');
    preview.src = this.value;
    preview.style.display = this.value ? 'block' : 'none';
});
```

### Save Trip (PHP, admin/edit-trip.php POST handler)
```php
// Source: project pattern — HIGH confidence (mirrors save_trips() pattern)
$trips = load_trips();
$idx   = null;
foreach ($trips as $i => $t) {
    if ($t['slug'] === $slug) { $idx = $i; break; }
}

$trip_data = [
    'slug'            => $slug,
    'title'           => trim($_POST['title']),
    'published'       => isset($_POST['published']),
    'preview_token'   => $existing_token ?: bin2hex(random_bytes(16)),
    'commission_rate' => (float)($_POST['commission_rate'] ?? 0),
    // ... all other fields
];

if ($idx !== null) {
    $trips[$idx] = $trip_data;
} else {
    $trips[] = $trip_data;
}
save_trips($trips);
```

### admin-config.json Schema (Bootstrap Default)
```json
{
    "anthropic_api_key": "",
    "default_webhook_url": "",
    "waitlist_webhook_url": "",
    "b2b_webhook_url": "",
    "whatsapp_number": "",
    "tally_catalog_url": "",
    "tally_b2b_url": "",
    "admin_password": "",
    "urgency_bar_text": "West America Aprile 2026 — Ultimi 5 posti disponibili",
    "company_name": "Y86 Travel",
    "company_vat": "",
    "company_address": ""
}
```

### destinations.json — Top-Level Structure
```json
{
    "america": {
        "name": "America",
        "hero_image": "...",
        "intro_paragraphs": ["...", "...", "..."],
        "practical_info": [
            {"icon": "fa-solid fa-coins", "label": "Valuta", "value": "Dollaro USA (USD)"}
        ],
        "see_also": [
            {"name": "New York", "image": "...", "description": "..."}
        ],
        "curiosita": [
            {"icon": "fa-solid fa-mountain", "title": "...", "text": "..."}
        ]
    }
}
```
Top-level keyed object (not array) — access by slug: `$data[$slug]`. This matches the existing PHP array structure in destinations-data.php.

### tags.json Extended Schema (adds category)
```json
[
    {"slug": "america", "label": "America", "category": "continente"},
    {"slug": "road-trip", "label": "Road Trip", "category": "tipo viaggio"},
    {"slug": "coppia", "label": "Per Coppia", "category": "per chi"},
    {"slug": "aprile", "label": "Aprile", "category": "mese"}
]
```
The existing tags.json has no `category` field. Admin tags.php needs to add/respect this field for grouping. Existing tags without category should default gracefully (e.g. display under "Altro").

---

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| OpenAI GPT-4o-mini for form generation | Anthropic claude-sonnet-4-6 | Phase 6 (this phase) | api/generate-form.php must be rewritten |
| Hardcoded config.php constants | admin-config.json overlay | Phase 6 (this phase) | config.php reading order must be refactored |
| Hardcoded destinations-data.php | data/destinations.json | Phase 6 (this phase) | destinazione.php updated to call load_destinations() |
| No published gate in viaggio.php | published + deleted + preview_token gates | Phase 6 (this phase) | viaggio.php top section updated |
| Urgency bar and footer company data hardcoded | Read from admin-config.json | Phase 6 (this phase) | index.php and footer.php updated |

**Deprecated/outdated:**
- `OPENAI_API_KEY` constant: replaced by `ANTHROPIC_API_KEY` — rename in config.php, update README
- `includes/destinations-data.php` as data source: keep file as archive but stop requiring it

---

## Open Questions

1. **Password migration — legacy plaintext vs hashed**
   - What we know: config.php stores `ADMIN_PASSWORD = 'Admin2025!'` as plaintext. Settings page will write a hashed version to admin-config.json.
   - What's unclear: First login after Phase 6 deploy — config.php still has plaintext; admin-config.json may be empty. Login handler must handle both plain and hashed formats.
   - Recommendation: Login handler checks if stored password starts with `$2y$` (bcrypt prefix); if not, does plain string compare. On next settings save, password is hashed. This is the pattern shown in the login handler example above.

2. **Tags category backfill**
   - What we know: Existing tags.json has 21 tags with no `category` field. Admin tags.php groups by category.
   - What's unclear: Should existing tags get categories assigned manually during development, or auto-assigned?
   - Recommendation: Manually assign categories to all 21 existing tags during the Wave that builds tags.php. The mapping is obvious (america/asia/europa/africa/oceania/medio-oriente → continente; road-trip/avventura/etc → tipo viaggio; coppia/famiglia/gruppo → per chi; aprile/maggio/etc → mese).

3. **Position field in trips.json — initial values**
   - What we know: Dashboard rows are drag-reorderable; position index is saved to trips.json.
   - What's unclear: Existing trips.json has no `position` field. On first admin dashboard load, trips need an initial position.
   - Recommendation: If `position` is absent, treat array index as implicit position. On first drag-and-drop save, positions are written explicitly. No migration script needed.

---

## Validation Architecture

### Test Framework
| Property | Value |
|----------|-------|
| Framework | None — PHP CLI not available on SiteGround shared hosting (confirmed in STATE.md). Manual browser testing + Bash content inspection. |
| Config file | N/A |
| Quick run command | `grep -c "..." /path/to/file.php` (content inspection) |
| Full suite command | Human-verify checklist (matching Phase 5 pattern) |

### Phase Requirements → Test Map
| Req ID | Behavior | Test Type | Automated Command | File Exists? |
|--------|----------|-----------|-------------------|-------------|
| ADMIN-01 | Login form renders; wrong password rejected; session set on success | manual-only | Bash content inspection of login.php for session_start, password_verify | ❌ Wave 0 |
| ADMIN-02 | Dashboard shows all trips; stats bar counts match | manual-only | `grep -c "load_trips\|stats" admin/index.php` | ❌ Wave 0 |
| ADMIN-03 | Edit form: basic fields save to trips.json correctly | manual-only | Content inspection of edit-trip.php POST handler | ❌ Wave 0 |
| ADMIN-04 | Hero image URL → live preview thumbnail renders | manual-only | JS presence in edit-trip.php | ❌ Wave 0 |
| ADMIN-05 | Short description counter shows remaining chars | manual-only | JS presence + maxlength attribute | ❌ Wave 0 |
| ADMIN-06 | Itinerary rows reorder; day numbers renumber | manual-only | HTML structure + JS drag handler presence | ❌ Wave 0 |
| ADMIN-07 | Includes/excludes save as arrays split on newline | manual-only | Content inspection of save logic | ❌ Wave 0 |
| ADMIN-08 | Tag pills toggle; custom tags added; saved as array | manual-only | JS + hidden input inspection | ❌ Wave 0 |
| ADMIN-09 | Anthropic API called with correct endpoint/headers | manual-only | `grep -c "api.anthropic.com\|anthropic-version" api/generate-form.php` | ❌ Wave 0 (modify existing) |
| ADMIN-10 | Salva Bozza/Pubblica set correct published state; Anteprima opens preview URL | manual-only | Content inspection of save handler and preview_token logic | ❌ Wave 0 |
| ADMIN-11 | Settings form saves admin-config.json; config.php reads it and overrides constants | manual-only | `grep -c "admin-config.json" includes/config.php` | ❌ Wave 0 (modify existing) |

**Justification for manual-only:** PHP CLI unavailable on SiteGround (confirmed STATE.md). All prior phases verified by content inspection + human browser check. Same constraint applies here.

### Sampling Rate
- **Per task commit:** Bash content inspection grep confirming key function names / strings exist
- **Per wave merge:** Human browser check of that wave's pages
- **Phase gate:** Full human-verify checklist before `/gsd:verify-work` (same pattern as Phase 5)

### Wave 0 Gaps
- [ ] `admin/login.php` — covers ADMIN-01
- [ ] `admin/index.php` — covers ADMIN-02
- [ ] `admin/edit-trip.php` — covers ADMIN-03 through ADMIN-10
- [ ] `admin/settings.php` — covers ADMIN-11
- [ ] `admin/tags.php` — covers tag management
- [ ] `admin/destinations.php` — covers destination editing
- [ ] `admin/admin.css` — admin stylesheet
- [ ] `data/admin-config.json` — new data file (created by settings.php on first save)
- [ ] `data/destinations.json` — migrated from includes/destinations-data.php

---

## Sources

### Primary (HIGH confidence)
- Existing codebase: `includes/functions.php`, `includes/config.php`, `api/generate-form.php`, `data/trips.json`, `data/tags.json` — confirmed by direct file read
- `https://platform.claude.com/docs/en/api/messages` — Anthropic Messages API endpoint, headers, response structure, PHP cURL pattern
- MDN Web Docs: HTML Drag and Drop API — HTML5 dragstart/dragover/drop event model

### Secondary (MEDIUM confidence)
- `06-CONTEXT.md` — all locked decisions verified against existing code patterns
- PHP documentation: `password_hash()`, `password_verify()`, `bin2hex()`, `random_bytes()`, `flock()` — standard library functions, stable across PHP 7.4+

### Tertiary (LOW confidence)
- None

---

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH — all libraries are already in use or built into PHP
- Architecture: HIGH — patterns directly replicate existing codebase conventions
- Anthropic API: HIGH — verified against official docs
- Pitfalls: HIGH — derived from direct code inspection and known PHP gotchas
- Drag-and-drop: MEDIUM — vanilla HTML5 API, well-documented, but not yet tested in this project

**Research date:** 2026-03-06
**Valid until:** 2026-06-06 (stable PHP + Anthropic API; 90-day estimate)
