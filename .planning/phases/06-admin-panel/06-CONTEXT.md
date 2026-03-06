# Phase 6: Admin Panel - Context

**Gathered:** 2026-03-06
**Status:** Ready for planning

<domain>
## Phase Boundary

Build a complete admin panel (`admin/`) so Lorenzo can create, edit, publish, reorder, and delete trips — and manage all site configuration — without touching code or the server. Includes: session auth login, trip dashboard, full edit form, tag management, destination content editing, settings page, and soft-delete trash. No end-customer accounts. No image upload (URL-only throughout).

</domain>

<decisions>
## Implementation Decisions

### Visual style
- Light/neutral admin style: white or light grey background, dark text, gold (#C9A84C) accents on primary action buttons only
- Separate admin CSS file (does NOT inherit the public site's dark palette)
- Admin nav header includes: logo/site name, Pannello link, Impostazioni link, "Vai al sito" (opens public site in new tab), Logout button
- Font: Inter body throughout (no Playfair Display — that's for the public site). Font Awesome 6 for icons.

### Edit form layout
- Tabbed sections: **Info Base | Media | Contenuto | Itinerario | Form Config**
- JS tab switching (vanilla, inline per page)
- Save buttons (Salva Bozza / Pubblica / Anteprima) fixed at the bottom or sticky footer of the form, always visible regardless of active tab

### Dashboard trip table
- Columns: Title, Continent, Tags (as small chips), Status pill (Pubblicato / Bozza), Actions (Modifica | Anteprima | Elimina)
- Stats bar at top: total / published / draft counts
- "Crea Nuovo Viaggio" gold button prominent
- Rows are drag-and-drop reorderable (HTML5 drag API) — position index saved to trips.json, controls homepage carousel and catalog order
- "Cestino" tab/collapsible section shows soft-deleted trips with Ripristina and Svuota Cestino actions

### Config/settings persistence
- Settings saved to `data/admin-config.json` (NOT rewriting config.php)
- `includes/config.php` reads admin-config.json at runtime and uses those values if present, falling back to its own constants
- All of the following are editable from admin/settings.php:
  - ANTHROPIC_API_KEY (replaces OPENAI_API_KEY — we use Claude, not OpenAI)
  - DEFAULT_WEBHOOK_URL (quote form submissions)
  - Waitlist webhook URL
  - Partner/B2B webhook URL
  - WHATSAPP_NUMBER
  - TALLY_CATALOG_URL
  - TALLY_B2B_URL
  - Admin password (hashed before storage)
  - **Urgency bar text** (the red strip on homepage) — index.php reads from admin-config.json
  - **Company data** (P.IVA, company name, address) — footer.php reads from admin-config.json

### Itinerary builder (ADMIN-06)
- "Aggiungi Giorno" button appends a new row
- Day number auto-numbered by row position (1, 2, 3...) — reordering renumbers automatically
- Each row: auto day number (read-only display), title text input, description textarea, up/down arrow buttons, drag handle (HTML5 drag API), remove button
- Both drag-and-drop AND arrow buttons available (drag on desktop, arrows as fallback)

### Delete behavior
- Soft delete only: trips get `deleted: true` flag in trips.json and disappear from public site
- Published trips: clicking Delete triggers modal — "Questo viaggio è pubblicato. Vuoi prima renderlo bozza?" — must unpublish before deleting
- Draft trips: confirmation modal "Sicuro? Il viaggio andrà nel Cestino." then soft-deleted
- Trash section in dashboard: Ripristina (restores deleted: false) + Svuota Cestino (hard deletes all trashed trips permanently)

### Tag management (new page: admin/tags.php)
- Tags grouped by category: continente, tipo viaggio, mese, per chi
- Add new tag inline: name input + category select + Add button
- Delete tag: cascades — removes tag from all trips in trips.json automatically
- Saves to data/tags.json (existing file)

### Destination content editing (new page: admin/destinations.php)
- `includes/destinations-data.php` migrated to `data/destinations.json`
- `destinazione.php` reads JSON instead of PHP include
- Admin can edit per-destination: hero image URL, intro paragraphs (3), practical info boxes, sub-destination cards (4 per destination), curiosità facts (3)
- 6 fixed slugs only (america, asia, europa, africa, oceania, medio-oriente) — no add/remove

### AI form generator (ADMIN-09)
- Uses **Anthropic Claude API** (`claude-sonnet-4-6`) — NOT OpenAI/GPT
- ANTHROPIC_API_KEY stored in admin-config.json
- Endpoint: `api/generate-form.php` (already exists from Phase 4 — must be updated from OpenAI to Anthropic)
- If ANTHROPIC_API_KEY is empty: return sensible default form_config (existing fallback behavior)
- Admin UI: Italian description textarea → "Genera Form con AI" button → loading state → JSON preview → save to trip

### Per-trip commission rate
- Field in edit-trip.php: "Commissione agenzie (%)" — admin-only, stored in trips.json as `commission_rate`
- Never shown on the public trip page
- Included in the webhook payload when agency submits quote form with valid agency code
- Pabbly (or other automation) uses this to generate the commission PDF sent to the agency

### Trip preview
- Random `preview_token` generated on trip creation/first save, stored in trips.json
- `viaggio.php?slug=xxx&preview=TOKEN` renders even unpublished (draft) trips
- Token regenerable from edit-trip.php (button: "Rigenera token anteprima")
- Lorenzo can share the preview URL with a client before publishing
- ADMIN-10 "Anteprima" button appends `&preview=TOKEN` to the viaggio.php URL, opens in new tab

### Save actions (ADMIN-10)
- "Salva Bozza" — saves with published: false
- "Pubblica" — saves with published: true, makes live immediately
- "Anteprima" — opens `viaggio.php?slug=xxx&preview=TOKEN` in new tab (works for drafts)
- Auto-slug generation: derived from title on blur (Italian-friendly: lowercase, spaces to hyphens, accents stripped), editable before first save, locked after first publish (to avoid breaking live URLs)

### Claude's Discretion
- Exact light admin CSS (color palette, table styling, form layout spacing)
- Tab switching JS implementation details
- Drag handle visual treatment (grip icon vs row hover highlight)
- Form validation UX (inline errors vs submit-time summary)
- admin-config.json schema structure and bootstrap defaults
- Migration script or manual copy for destinations-data.php → destinations.json

</decisions>

<code_context>
## Existing Code Insights

### Reusable Assets
- `save_trips(array $trips): bool` in `includes/functions.php` — file-locked write to data/trips.json. Reuse for all trip saves from admin. Build equivalent `save_tags()` and `save_destinations()` functions using same pattern.
- `load_trips(): array` and `get_trip_by_slug()` — already available
- `includes/config.php` — has ADMIN_PASSWORD, all webhook/API constants. Admin-config.json will override these at runtime.
- `api/generate-form.php` — EXISTS but currently calls OpenAI. Must be updated to use Anthropic Claude API (claude-sonnet-4-6) in this phase.
- `data/tags.json` — exists, loaded by `load_tags()` in functions.php
- Font Awesome 6 CDN already in header.php — available for admin pages too

### Established Patterns
- PHP session: `session_start()` + `$_SESSION['admin']` check at top of every admin page, redirect to login if not set
- PHP includes: `includes/config.php` + `includes/functions.php` at top, then page-specific logic
- Inline `<script>` per page — no separate .js files
- JSON writes use `fopen` + `flock` + `fwrite` pattern (see `save_trips()`) — replicate for all admin saves
- All data in `/data/` directory (already protected by .htaccess — 403 for direct access)
- No external JS or CSS libraries — vanilla only, Font Awesome 6 CDN for icons

### Integration Points
- `admin/` directory is new — needs .htaccess auth check or PHP session redirect on every file
- `data/admin-config.json` is new — `includes/config.php` must be updated to read it and merge/override constants
- `data/destinations.json` is new — `destinazione.php` and `destinazioni.php` must be updated to read JSON instead of `includes/destinations-data.php`
- `api/generate-form.php` must be updated: swap OpenAI SDK call for Anthropic Messages API call
- `includes/config.php`: `OPENAI_API_KEY` constant should be renamed/replaced with `ANTHROPIC_API_KEY`
- `index.php` (homepage urgency bar) and `includes/footer.php` (company data) must be updated to read from admin-config.json

</code_context>

<specifics>
## Specific Ideas

- Lorenzo must be able to manage everything from the panel without ever touching files — this is the core requirement
- All webhook URLs are manageable from settings: preventivo (quote form), waitlist (destination sold-out), partner (B2B agencies)
- Urgency bar text and P.IVA / company footer data are site content, not just config — admin-config.json is the right home for both
- Commission rate is webhook-payload-only — never rendered on the public trip page. Pabbly handles the rest.
- Preview token enables sharing draft trip pages with clients before publishing — a real workflow need for Lorenzo
- The AI generator uses Claude (claude-sonnet-4-6) — this corrects the Phase 4 generate-form.php which was written for OpenAI

</specifics>

<deferred>
## Deferred Ideas

- Image upload in admin (currently URL-only) — ENH-01, v2
- Dynamic destination add/remove (7th continent, etc.) — v2
- Blog/editorial section management — ENH-06, v2
- Per-agency account management (separate login per agency) — out of scope for v1

</deferred>

---

*Phase: 06-admin-panel*
*Context gathered: 2026-03-06*
