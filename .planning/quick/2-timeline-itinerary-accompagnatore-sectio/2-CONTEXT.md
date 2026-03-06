# Quick Task 2: Timeline itinerary, accompagnatore section, flight details, tag pills - Context

**Gathered:** 2026-03-06
**Status:** Ready for planning

<domain>
## Task Boundary

Multiple improvements to viaggio.php and trips.json (data schema):
1. FIX: Tags section rendered as styled pill badges (centered, dark bg, white border)
2. FIX: Replace itinerary accordion with alternating visual timeline
3. NEW: Accompagnatore section (between highlights bar and tab nav)
4. NEW: Dettagli Volo collapsible section (between accompagnatore and tab nav)
5. DATA: Update trips.json schema to support new fields + populate West America sample data

EXPLICITLY OUT OF SCOPE: No changes to /admin/ folder — admin panel updates deferred to Phase 6.

</domain>

<decisions>
## Implementation Decisions

### Section Order (between highlights bar and tab nav)
- Accompagnatore → Volo → Tabs (in that order)
- Lead with Lorenzo (personal/emotional), then practical flight details, then content tabs

### Photo URLs
- Lorenzo photo: https://placehold.co/120x120/000744/ffffff?text=Lorenzo
- Day 1 (LA): https://images.unsplash.com/photo-1534430480872-3498386e7856?w=800
- Day 2 (PCH): https://images.unsplash.com/photo-1449034446853-66c86144b0ad?w=800
- Day 3 (Vegas): https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800
- Days 4-15: empty image_url string (photo section conditionally hidden)

### Commit Strategy
- GSD atomic commits: one commit per logical change (CSS, viaggio.php sections, data)

### Admin Panel
- DO NOT touch /admin/ folder — all admin field additions are deferred to Phase 6

</decisions>

<specifics>
## Specific Requirements

### FIX 1 — Tags Section CSS
```css
.tag-pill {
  display: inline-block;
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.3);
  color: #FFFFFF;
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
  margin: 4px;
  text-decoration: none;
  transition: all 0.2s ease;
  text-transform: capitalize;
}
.tag-pill:hover { background: #000744; border-color: #000744; color: #fff; }
.tags-section { text-align: center; padding: 60px 24px; }
.tags-cloud {
  display: flex; flex-wrap: wrap; justify-content: center;
  gap: 8px; margin-top: 24px; max-width: 700px;
  margin-left: auto; margin-right: auto;
}
```

### FIX 2 — Timeline CSS (full spec in user message)
- Alternating left/right on desktop, single-column on mobile
- Center vertical line (#000744, 2px)
- Navy circle dots with white day numbers
- Dark card (.timeline-card, bg #1a1f3e) with conditional photo
- See user-provided CSS in task description

### NEW SECTION 1 — Accompagnatore
Data schema in trips.json:
```json
"accompagnatore": {
  "nome": "Lorenzo D'Alessandro",
  "titolo": "Il Baffo — Fondatore e Accompagnatore",
  "foto": "https://placehold.co/120x120/000744/ffffff?text=Lorenzo",
  "bio": "Partirà personalmente con questo gruppo. 48 stati americani visitati, oltre 40 anni di esperienza. Con Lorenzo non sei mai solo.",
  "instagram": "",
  "whatsapp": ""
}
```
Design: dark card, horizontal (photo left + info right), circular 120px photo with navy border, Playfair name, "● Accompagna questo viaggio" green badge. Hidden entirely if null/empty.

### NEW SECTION 2 — Dettagli Volo
Data schema in trips.json:
```json
"volo": {
  "incluso": true,
  "andata": { "data": "17 Aprile 2026", "partenza_aeroporto": "Milano Malpensa (MXP)", "arrivo_aeroporto": "Los Angeles (LAX)", "compagnia": "Lufthansa", "numero_volo": "LH 234", "orario_partenza": "10:30", "orario_arrivo": "14:45", "scalo": "Frankfurt (FRA) — 2h layover" },
  "ritorno": { "data": "1 Maggio 2026", "partenza_aeroporto": "San Francisco (SFO)", "arrivo_aeroporto": "Milano Malpensa (MXP)", "compagnia": "Lufthansa", "numero_volo": "LH 456", "orario_partenza": "16:20", "orario_arrivo": "12:10 +1", "scalo": "Frankfurt (FRA) — 1h 45min layover" }
}
```
Design: collapsed by default, "✈ Dettagli Volo" toggle button. Two flight cards side-by-side. Dark card, navy border-left, red airline name. volo.incluso=false → "Volo non incluso". null → hidden.

### DATA — Updated itinerary day schema
```json
{ "day": 1, "title": "...", "location": "Los Angeles, California", "date": "", "description": "...", "image_url": "https://images.unsplash.com/..." }
```

### West America sample data additions
- accompagnatore: Lorenzo with placehold.co photo
- volo: MXP→LAX Lufthansa, SFO→MXP Lufthansa
- itinerary days: add location to all 15 days, image_url to days 1-3

</specifics>
