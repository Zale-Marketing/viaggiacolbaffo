# Viaggia Col Baffo

PHP + vanilla JS website for Viaggia Col Baffo, a premium Italian tour operator. Lorenzo is personally present on every trip — the site conveys intimacy, trust, and a premium experience.

Built on SiteGround shared hosting with no database. All trip data lives in `data/trips.json`, deployed automatically via GitHub Actions FTP.

---

## 1. Setup

### Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/viaggiacolbaffo.git
cd viaggiacolbaffo
```

### Add GitHub Secrets

In your GitHub repository, go to **Settings → Secrets and variables → Actions** and add:

| Secret | Value |
|--------|-------|
| `FTP_SERVER` | Your SiteGround FTP hostname (e.g. `ftp.viaggiacolbaffo.com`) |
| `FTP_USERNAME` | Your SiteGround FTP username |
| `FTP_PASSWORD` | Your SiteGround FTP password |

These credentials are never stored in the repository — only in GitHub's encrypted secret store.

### Deploy

Push to the `main` branch to trigger an automatic deploy:

```bash
git push origin main
```

The GitHub Actions workflow (`.github/workflows/deploy.yml`) will upload all site files to `/nuovo.viaggiacolbaffo.com/public_html/` on SiteGround. The `.git/`, `.claude/`, `.planning/` directories and `README.md` are excluded from the upload.

---

## 2. Trip Management

All trips are stored in `data/trips.json` as a JSON array. Each trip object supports the following fields:

| Field | Type | Description |
|-------|------|-------------|
| `slug` | string | URL slug (e.g. `west-america-aprile-2026`). Used in `/viaggio/{slug}` |
| `title` | string | Trip title displayed in cards and detail page |
| `continent` | string | Continent tag (e.g. `america`, `asia`, `europa`) |
| `tags` | array | Theme tags (e.g. `["road-trip", "coppia", "avventura"]`) |
| `status` | string | One of: `disponibile`, `ultimi-posti`, `sold-out` |
| `published` | boolean | `true` to show on site, `false` to hide |
| `price_from` | number | Starting price in EUR |
| `dates` | string | Human-readable date range (e.g. `"5–20 Aprile 2026"`) |
| `hero_image` | string | Unsplash direct URL for the hero/card image |
| `short_description` | string | One or two sentences shown in catalog cards |
| `description` | string | Full HTML description shown on trip detail page |
| `itinerary` | array | Day-by-day itinerary objects (`{ "day": 1, "title": "...", "description": "..." }`) |
| `includes` | array | What is included in the price (list of strings) |
| `excludes` | array | What is not included (list of strings) |
| `gallery` | array | Unsplash direct URLs for gallery photos |
| `form_config` | object | Quote form configuration (fields, options, price calculation) |
| `webhook_url` | string | Optional. Per-trip webhook override. Falls back to `DEFAULT_WEBHOOK_URL` in config.php |

**Status values:**
- `disponibile` — trip has availability, shown with a green badge
- `ultimi-posti` — last spots remaining, shown with an amber/urgency badge
- `sold-out` — trip is full, shown with a grey badge; still visible in catalog for social proof

To add or edit a trip, edit `data/trips.json` directly and push to `main`. The deploy pipeline will upload the updated file to SiteGround.

> **Note (after Phase 6):** Once the admin panel is live, Lorenzo will edit trips through the admin UI which writes directly to the server. At that point, add `data/trips.json` to the FTP deploy exclude list in `.github/workflows/deploy.yml` to avoid overwriting admin-created changes on the next code deploy.

---

## 3. Webhook Configuration

Quote form submissions are sent to a webhook URL via HTTP POST (JSON body). Configure webhooks in two ways:

**Global default** — set `DEFAULT_WEBHOOK_URL` in `includes/config.php`:

```php
define('DEFAULT_WEBHOOK_URL', 'https://hook.eu1.make.com/YOUR_WEBHOOK_ID');
```

**Per-trip override** — add a `webhook_url` field to the trip object in `data/trips.json`:

```json
{
  "slug": "west-america-aprile-2026",
  "webhook_url": "https://hook.eu1.make.com/SPECIFIC_TRIP_WEBHOOK",
  ...
}
```

If a trip has its own `webhook_url`, that is used instead of the default. This lets you route different trips to different Make/Zapier scenarios or email addresses.

---

## 4. Anthropic Key Setup (AI Form Generator)

The admin panel includes an AI-powered form generator: paste a plain Italian trip description and the AI returns a `form_config` JSON that you can save directly to the trip.

To enable it, set your Anthropic API key via the admin panel settings page, or add it to `data/admin-config.json`:

```json
{ "anthropic_api_key": "sk-ant-..." }
```

Leave the value as an empty string `""` to disable the feature. The rest of the site works normally without an API key.

---

## Tech Stack

- **Backend:** PHP (no framework, no Composer)
- **Frontend:** Vanilla JS, custom CSS (no build step)
- **Icons:** Font Awesome 6 (CDN)
- **Fonts:** Google Fonts — Playfair Display + Inter (CDN)
- **Images:** Unsplash direct URLs (no local uploads)
- **Data:** `data/trips.json` and `data/tags.json` (flat files, no database)
- **Hosting:** SiteGround shared hosting (Apache)
- **Deploy:** GitHub Actions → FTP
