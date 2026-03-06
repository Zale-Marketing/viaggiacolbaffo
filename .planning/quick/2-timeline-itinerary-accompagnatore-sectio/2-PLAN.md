---
phase: quick-2
plan: 01
type: execute
wave: 1
depends_on: []
files_modified:
  - data/trips.json
  - assets/css/style.css
  - viaggio.php
autonomous: true
requirements: []
must_haves:
  truths:
    - "Itinerary renders as alternating left/right timeline with navy dots and dark cards"
    - "Accompagnatore card appears between highlights bar and tab nav with circular photo, name, bio, and green badge"
    - "Dettagli Volo section appears between accompagnatore and tab nav, collapsed by default, expands to show two flight cards"
    - "Tags render as centered pill badges (light border, white text, navy hover) not as plain links"
    - "All sections are hidden when their corresponding data field is null/absent"
  artifacts:
    - path: "data/trips.json"
      provides: "accompagnatore, volo, and enriched itinerary day objects for west-america trip"
      contains: "accompagnatore"
    - path: "assets/css/style.css"
      provides: "Timeline, tag-pill, accompagnatore, and volo CSS rules"
      contains: ".tag-pill"
    - path: "viaggio.php"
      provides: "Timeline HTML, accompagnatore section, dettagli volo section"
      contains: "accompagnatore"
  key_links:
    - from: "viaggio.php"
      to: "data/trips.json"
      via: "PHP $trip['accompagnatore'] and $trip['volo'] reads"
      pattern: "\\$trip\\['accompagnatore'\\]"
    - from: "viaggio.php volo toggle button"
      to: "#volo-details div"
      via: "inline JS toggle on click"
      pattern: "volo-details"
---

<objective>
Add timeline itinerary view, accompagnatore section, Dettagli Volo collapsible section, and redesigned tag pills to the trip detail page (viaggio.php). Data schema extended with new fields; West America trip populated with sample data.

Purpose: Elevate the trip detail page from generic to premium — Lorenzo's personal presence (accompagnatore) and concrete flight details build trust and reduce friction to enquiry.
Output: Updated trips.json, style.css with new CSS blocks, viaggio.php with new sections replacing the accordion itinerary.
</objective>

<execution_context>
@./.claude/get-shit-done/workflows/execute-plan.md
@./.claude/get-shit-done/templates/summary.md
</execution_context>

<context>
@.planning/STATE.md
@.planning/quick/2-timeline-itinerary-accompagnatore-sectio/2-CONTEXT.md
</context>

<tasks>

<task type="auto">
  <name>Task 1: Extend trips.json data schema and populate West America sample data</name>
  <files>data/trips.json</files>
  <action>
Edit the west-america-aprile-2026 trip object in data/trips.json. Add three new top-level fields alongside the existing ones (do NOT touch giappone-classico-2025 entry):

1. Add "accompagnatore" object after "webhook_url":
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

2. Add "volo" object after "accompagnatore":
```json
"volo": {
  "incluso": true,
  "andata": {
    "data": "17 Aprile 2026",
    "partenza_aeroporto": "Milano Malpensa (MXP)",
    "arrivo_aeroporto": "Los Angeles (LAX)",
    "compagnia": "Lufthansa",
    "numero_volo": "LH 234",
    "orario_partenza": "10:30",
    "orario_arrivo": "14:45",
    "scalo": "Frankfurt (FRA) — 2h layover"
  },
  "ritorno": {
    "data": "1 Maggio 2026",
    "partenza_aeroporto": "San Francisco (SFO)",
    "arrivo_aeroporto": "Milano Malpensa (MXP)",
    "compagnia": "Lufthansa",
    "numero_volo": "LH 456",
    "orario_partenza": "16:20",
    "orario_arrivo": "12:10 +1",
    "scalo": "Frankfurt (FRA) — 1h 45min layover"
  }
}
```

3. Replace the existing "itinerary" array entirely with 15 days. Use the existing 7 day descriptions as source text for days 1-7. Expand to 15 days total (days 8-15 can have brief placeholder descriptions). Each day object must include "day", "title", "location", "date" (empty string), "description", and "image_url" fields.

Day locations and image_url values:
- Day 1: location "Los Angeles, California", image_url "https://images.unsplash.com/photo-1534430480872-3498386e7856?w=800"
- Day 2: location "Malibu — Pacific Coast Highway", image_url "https://images.unsplash.com/photo-1449034446853-66c86144b0ad?w=800"
- Day 3: location "Los Angeles → Las Vegas, Nevada", image_url "https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800"
- Days 4-15: location appropriate to the stage, image_url "" (empty string)

Day 4 title: "Tour di Las Vegas", location: "Las Vegas, Nevada"
Day 5 title: "Grand Canyon South Rim", location: "Grand Canyon, Arizona"
Day 6 title: "Zion e Bryce Canyon", location: "Zion / Bryce Canyon, Utah"
Day 7 title: "Monument Valley e Antelope Canyon", location: "Monument Valley / Page, Arizona"
Day 8 title: "Page e Lake Powell", location: "Page, Arizona", description: "Esplorazione di Horseshoe Bend e Lake Powell. Lorenzo conduce un'escursione in kayak sul lago."
Day 9 title: "Sedona — Rocce Rosse", location: "Sedona, Arizona", description: "Giornata a Sedona tra le formazioni rocciose rossastre. Trekking a Cathedral Rock al tramonto."
Day 10 title: "Phoenix e Desert Botanical Garden", location: "Phoenix, Arizona", description: "Visita al Desert Botanical Garden e passeggiata nel quartiere artistico di Roosevelt Row."
Day 11 title: "Joshua Tree National Park", location: "Joshua Tree, California", description: "Esplorazione del parco tra i caratteristici alberi di Joshua e formazioni granitiche mozzafiato."
Day 12 title: "San Diego", location: "San Diego, California", description: "Visita al Balboa Park e al quartiere storico del Gaslamp Quarter. Cena di pesce fresco sul lungomare."
Day 13 title: "Big Sur e Monterey", location: "Big Sur — Monterey, California", description: "Guida lungo la leggendaria Highway 1 attraverso Big Sur, con sosta a McWay Falls. Pernottamento a Monterey."
Day 14 title: "San Francisco", location: "San Francisco, California", description: "Arrivo a San Francisco: Golden Gate Bridge, Fisherman's Wharf e giro in cable car. Cena d'addio con Lorenzo."
Day 15 title: "Rientro da San Francisco", location: "San Francisco (SFO), California", description: "Mattinata libera per gli ultimi acquisti. Transfer all'aeroporto SFO per il volo di rientro su Milano Malpensa."

Keep all other west-america fields exactly as they are (form_config, gallery, tags, etc.). Write the full JSON file with JSON_UNESCAPED_UNICODE equivalent (literal UTF-8 Italian text, no \\u escapes). Validate JSON is syntactically correct (matching brackets/braces) before saving.
  </action>
  <verify>
    <automated>node -e "const d=require('./data/trips.json'); const t=d.find(x=>x.slug==='west-america-aprile-2026'); console.log('accompagnatore:', !!t.accompagnatore, '| volo:', !!t.volo, '| itinerary days:', t.itinerary.length, '| day1 image:', !!t.itinerary[0].image_url)"</automated>
  </verify>
  <done>west-america trip has accompagnatore object, volo object, and itinerary with 15 days each having location and image_url fields. Day 1-3 have non-empty image_url. JSON is valid.</done>
</task>

<task type="auto">
  <name>Task 2: Add CSS — tag pills, timeline, accompagnatore card, volo section</name>
  <files>assets/css/style.css</files>
  <action>
In assets/css/style.css, make two changes:

CHANGE A — Replace the existing "trip-tags" and "trip-tag" rules (section "--- 10. Tags section ---") with the new pill design. Find and replace lines:

```css
/* --- 10. Tags section --- */

.trip-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
  margin-top: 1rem;
}

.trip-tag {
  display: inline-block;
  background: rgba(0,7,68,0.5);
  border: 1px solid rgba(0,7,68,0.8);
  color: rgba(255,255,255,0.85);
  border-radius: 20px;
  padding: 4px 14px;
  font-size: 0.8rem;
  font-weight: 600;
  transition: var(--transition);
}

.trip-tag:hover {
  background: #000744;
  color: var(--white);
}
```

Replace with:

```css
/* --- 10. Tags section --- */

.tags-section { text-align: center; padding: 60px 24px; }

.tags-cloud {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 8px;
  margin-top: 24px;
  max-width: 700px;
  margin-left: auto;
  margin-right: auto;
}

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

/* Legacy alias — keep for any existing .trip-tag references */
.trip-tags { display: flex; flex-wrap: wrap; gap: 0.6rem; margin-top: 1rem; }
.trip-tag { display: inline-block; background: rgba(0,7,68,0.5); border: 1px solid rgba(0,7,68,0.8); color: rgba(255,255,255,0.85); border-radius: 20px; padding: 4px 14px; font-size: 0.8rem; font-weight: 600; transition: var(--transition); }
.trip-tag:hover { background: #000744; color: var(--white); }
```

CHANGE B — Append the following new CSS blocks at the very end of style.css (after all existing rules):

```css
/* ================================================================
   TIMELINE ITINERARY
   ================================================================ */

.timeline {
  position: relative;
  padding: 2rem 0;
}

.timeline::before {
  content: '';
  position: absolute;
  left: 50%;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #000744;
  transform: translateX(-50%);
}

.timeline-item {
  display: flex;
  justify-content: flex-end;
  padding-right: calc(50% + 2.5rem);
  margin-bottom: 2.5rem;
  position: relative;
}

.timeline-item:nth-child(even) {
  justify-content: flex-start;
  padding-right: 0;
  padding-left: calc(50% + 2.5rem);
}

.timeline-dot {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #000744;
  border: 3px solid rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 0.75rem;
  font-weight: 700;
  z-index: 1;
  top: 1.25rem;
}

.timeline-card {
  background: #1a1f3e;
  border-radius: 12px;
  overflow: hidden;
  max-width: 420px;
  width: 100%;
  box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.timeline-card__photo {
  width: 100%;
  height: 180px;
  object-fit: cover;
  display: block;
}

.timeline-card__body {
  padding: 1.25rem 1.5rem;
}

.timeline-card__location {
  font-size: 0.75rem;
  color: var(--gold);
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 0.35rem;
}

.timeline-card__title {
  font-family: var(--font-heading);
  font-size: 1.1rem;
  color: #fff;
  margin-bottom: 0.6rem;
}

.timeline-card__desc {
  font-size: 0.9rem;
  color: rgba(255,255,255,0.75);
  line-height: 1.6;
}

@media (max-width: 767px) {
  .timeline::before { left: 20px; }

  .timeline-item,
  .timeline-item:nth-child(even) {
    justify-content: flex-start;
    padding-right: 0;
    padding-left: 60px;
  }

  .timeline-dot {
    left: 20px;
    width: 32px;
    height: 32px;
    font-size: 0.7rem;
  }

  .timeline-card { max-width: 100%; }
}

/* ================================================================
   ACCOMPAGNATORE SECTION
   ================================================================ */

.accompagnatore-section {
  background: var(--dark-bg, #0a0d1e);
  padding: 3rem 0;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}

.accompagnatore-card {
  background: #1a1f3e;
  border-radius: 12px;
  padding: 2rem;
  display: flex;
  align-items: flex-start;
  gap: 2rem;
  max-width: 760px;
  margin: 0 auto;
}

.accompagnatore-card__photo {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #000744;
  flex-shrink: 0;
}

.accompagnatore-card__info { flex: 1; }

.accompagnatore-card__badge {
  display: inline-block;
  background: rgba(46,204,113,0.15);
  color: #2ecc71;
  border: 1px solid rgba(46,204,113,0.4);
  border-radius: 20px;
  padding: 3px 12px;
  font-size: 0.75rem;
  font-weight: 600;
  margin-bottom: 0.75rem;
}

.accompagnatore-card__name {
  font-family: var(--font-heading);
  font-size: 1.4rem;
  color: #fff;
  margin-bottom: 0.2rem;
}

.accompagnatore-card__titolo {
  font-size: 0.85rem;
  color: var(--gold);
  margin-bottom: 0.75rem;
}

.accompagnatore-card__bio {
  font-size: 0.95rem;
  color: rgba(255,255,255,0.8);
  line-height: 1.65;
}

@media (max-width: 600px) {
  .accompagnatore-card {
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 1.5rem;
  }
}

/* ================================================================
   DETTAGLI VOLO SECTION
   ================================================================ */

.volo-section {
  background: var(--dark-bg, #0a0d1e);
  padding: 1.5rem 0 2.5rem;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}

.volo-toggle {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  background: none;
  border: 1px solid rgba(255,255,255,0.15);
  border-radius: 8px;
  color: rgba(255,255,255,0.85);
  font-size: 0.95rem;
  padding: 0.6rem 1.25rem;
  cursor: pointer;
  transition: all 0.2s ease;
  margin: 0 auto;
  display: flex;
}

.volo-toggle:hover {
  background: rgba(255,255,255,0.05);
  border-color: rgba(255,255,255,0.35);
}

.volo-toggle__chevron {
  transition: transform 0.3s ease;
  font-size: 0.7rem;
}

.volo-toggle.open .volo-toggle__chevron { transform: rotate(180deg); }

.volo-details {
  display: none;
  margin-top: 1.5rem;
}

.volo-details.open { display: block; }

.volo-non-incluso {
  text-align: center;
  color: rgba(255,255,255,0.5);
  font-size: 0.9rem;
  padding: 1rem 0;
}

.volo-cards {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
  max-width: 800px;
  margin: 0 auto;
}

.volo-card {
  background: #1a1f3e;
  border-radius: 10px;
  border-left: 3px solid #000744;
  padding: 1.25rem 1.5rem;
}

.volo-card__label {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: rgba(255,255,255,0.45);
  margin-bottom: 0.6rem;
}

.volo-card__compagnia {
  color: #CC0031;
  font-weight: 700;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.volo-card__route {
  font-family: var(--font-heading);
  font-size: 1rem;
  color: #fff;
  margin-bottom: 0.5rem;
}

.volo-card__meta {
  font-size: 0.82rem;
  color: rgba(255,255,255,0.65);
  line-height: 1.7;
}

@media (max-width: 600px) {
  .volo-cards { grid-template-columns: 1fr; }
}
```
  </action>
  <verify>
    <automated>node -e "const fs=require('fs'); const css=fs.readFileSync('./assets/css/style.css','utf8'); console.log('tag-pill:', css.includes('.tag-pill'), '| timeline:', css.includes('.timeline-item'), '| accompagnatore:', css.includes('.accompagnatore-card'), '| volo:', css.includes('.volo-card'))"</automated>
  </verify>
  <done>style.css contains .tag-pill, .timeline-item, .accompagnatore-card, and .volo-card CSS rules.</done>
</task>

<task type="auto">
  <name>Task 3: Update viaggio.php — timeline itinerary, accompagnatore section, Dettagli Volo section, tag pills</name>
  <files>viaggio.php</files>
  <action>
Make four targeted changes to viaggio.php:

CHANGE 1 — Tags section: Replace the existing tags section block (the `<?php if (!empty($trip['tags'])): ?>` section with `.trip-tags` / `.trip-tag` classes) with the new pill layout using `.tags-section` / `.tags-cloud` / `.tag-pill`:

```php
<?php if (!empty($trip['tags'])): ?>
<section class="trip-section tags-section" id="tags">
  <div class="container">
    <h2 class="section-header__title">Questo viaggio è perfetto per:</h2>
    <?php
    $continent_slugs = ['america','asia','europa','africa','oceania','medio-oriente'];
    ?>
    <div class="tags-cloud">
      <?php foreach ($trip['tags'] as $tag): ?>
        <?php
        if (in_array($tag, $continent_slugs)) {
            $href = '/viaggi?continent=' . urlencode($tag);
        } else {
            $href = '/viaggi?tipo=' . urlencode($tag);
        }
        ?>
        <a href="<?php echo $href; ?>" class="tag-pill"><?php echo htmlspecialchars($tag); ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
```

CHANGE 2 — Itinerary section: Replace the existing itinerary section (with `.itinerary`, `.itinerary__item`, `.itinerary__header`, etc.) with the timeline layout:

```php
<!-- ========================================================
     ITINERARY SECTION — TIMELINE
     ======================================================== -->
<section class="trip-section" id="itinerario">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Itinerario</h2>
    </div>
    <div class="timeline">
      <?php foreach ($trip['itinerary'] as $day): ?>
      <div class="timeline-item">
        <div class="timeline-dot"><?php echo str_pad((int)$day['day'], 2, '0', STR_PAD_LEFT); ?></div>
        <div class="timeline-card">
          <?php if (!empty($day['image_url'])): ?>
          <img class="timeline-card__photo" src="<?php echo htmlspecialchars($day['image_url']); ?>" alt="<?php echo htmlspecialchars($day['title']); ?>" loading="lazy">
          <?php endif; ?>
          <div class="timeline-card__body">
            <?php if (!empty($day['location'])): ?>
            <div class="timeline-card__location"><?php echo htmlspecialchars($day['location']); ?></div>
            <?php endif; ?>
            <div class="timeline-card__title"><?php echo htmlspecialchars($day['title']); ?></div>
            <p class="timeline-card__desc"><?php echo htmlspecialchars($day['description']); ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
```

CHANGE 3 — Accompagnatore + Volo sections: Insert the following two new sections AFTER the highlights bar block and BEFORE the sticky tab navigation block. The insertion point is between `</div><!-- end trip-highlights -->` and `<!-- STICKY TAB NAVIGATION -->`:

```php
<?php
$accompagnatore = $trip['accompagnatore'] ?? null;
$volo           = $trip['volo'] ?? null;
?>

<?php if (!empty($accompagnatore['nome'])): ?>
<!-- ========================================================
     ACCOMPAGNATORE SECTION
     ======================================================== -->
<div class="accompagnatore-section">
  <div class="container">
    <div class="accompagnatore-card">
      <img class="accompagnatore-card__photo"
           src="<?php echo htmlspecialchars($accompagnatore['foto'] ?? ''); ?>"
           alt="<?php echo htmlspecialchars($accompagnatore['nome']); ?>">
      <div class="accompagnatore-card__info">
        <div class="accompagnatore-card__badge">&#9679; Accompagna questo viaggio</div>
        <div class="accompagnatore-card__name"><?php echo htmlspecialchars($accompagnatore['nome']); ?></div>
        <div class="accompagnatore-card__titolo"><?php echo htmlspecialchars($accompagnatore['titolo'] ?? ''); ?></div>
        <p class="accompagnatore-card__bio"><?php echo htmlspecialchars($accompagnatore['bio'] ?? ''); ?></p>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if (!is_null($volo)): ?>
<!-- ========================================================
     DETTAGLI VOLO SECTION
     ======================================================== -->
<div class="volo-section">
  <div class="container">
    <button class="volo-toggle" id="volo-toggle" type="button">
      &#9992; Dettagli Volo <i class="fa-solid fa-chevron-down volo-toggle__chevron"></i>
    </button>
    <div class="volo-details" id="volo-details">
      <?php if (!($volo['incluso'] ?? false)): ?>
      <div class="volo-non-incluso">Il volo non è incluso nel prezzo del viaggio.</div>
      <?php else: ?>
      <div class="volo-cards">
        <?php if (!empty($volo['andata'])): $a = $volo['andata']; ?>
        <div class="volo-card">
          <div class="volo-card__label">Volo Andata</div>
          <div class="volo-card__compagnia"><?php echo htmlspecialchars($a['compagnia'] ?? ''); ?> &middot; <?php echo htmlspecialchars($a['numero_volo'] ?? ''); ?></div>
          <div class="volo-card__route"><?php echo htmlspecialchars($a['partenza_aeroporto'] ?? ''); ?> &rarr; <?php echo htmlspecialchars($a['arrivo_aeroporto'] ?? ''); ?></div>
          <div class="volo-card__meta">
            <?php echo htmlspecialchars($a['data'] ?? ''); ?><br>
            Partenza <?php echo htmlspecialchars($a['orario_partenza'] ?? ''); ?> &mdash; Arrivo <?php echo htmlspecialchars($a['orario_arrivo'] ?? ''); ?><br>
            <?php if (!empty($a['scalo'])): ?>Scalo: <?php echo htmlspecialchars($a['scalo']); ?><?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($volo['ritorno'])): $r = $volo['ritorno']; ?>
        <div class="volo-card">
          <div class="volo-card__label">Volo Ritorno</div>
          <div class="volo-card__compagnia"><?php echo htmlspecialchars($r['compagnia'] ?? ''); ?> &middot; <?php echo htmlspecialchars($r['numero_volo'] ?? ''); ?></div>
          <div class="volo-card__route"><?php echo htmlspecialchars($r['partenza_aeroporto'] ?? ''); ?> &rarr; <?php echo htmlspecialchars($r['arrivo_aeroporto'] ?? ''); ?></div>
          <div class="volo-card__meta">
            <?php echo htmlspecialchars($r['data'] ?? ''); ?><br>
            Partenza <?php echo htmlspecialchars($r['orario_partenza'] ?? ''); ?> &mdash; Arrivo <?php echo htmlspecialchars($r['orario_arrivo'] ?? ''); ?><br>
            <?php if (!empty($r['scalo'])): ?>Scalo: <?php echo htmlspecialchars($r['scalo']); ?><?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php endif; ?>
```

CHANGE 4 — Remove accordion JS: In the `<script>` block at the bottom of viaggio.php, remove the entire "Itinerary accordion (single-open)" comment block and its associated JS code (from the comment `// --- Itinerary accordion (single-open) ---` through the closing `});` of the outer `document.querySelectorAll('.itinerary__header').forEach` block, approximately lines 462-487 in the original file). Replace it with the volo toggle JS:

```javascript
  // --- Volo toggle ---
  var voloToggle  = document.getElementById('volo-toggle');
  var voloDetails = document.getElementById('volo-details');
  if (voloToggle && voloDetails) {
    voloToggle.addEventListener('click', function () {
      var isOpen = voloDetails.classList.contains('open');
      voloDetails.classList.toggle('open', !isOpen);
      voloToggle.classList.toggle('open', !isOpen);
    });
  }
```

Place the volo toggle JS block right where the accordion JS was (after the tab navigation JS block, before the gallery lightbox JS block).
  </action>
  <verify>
    <automated>node -e "const fs=require('fs'); const php=fs.readFileSync('./viaggio.php','utf8'); console.log('timeline:', php.includes('timeline-item'), '| accompagnatore:', php.includes('accompagnatore-card'), '| volo-toggle:', php.includes('volo-toggle'), '| tag-pill:', php.includes('tag-pill'), '| old accordion removed:', !php.includes('itinerary__header'))"</automated>
  </verify>
  <done>viaggio.php contains timeline-item, accompagnatore-card, volo-toggle, tag-pill classes. The itinerary__header accordion is no longer present. Page renders without PHP parse errors (content inspection confirms balanced if/endif blocks).</done>
</task>

</tasks>

<verification>
After all tasks complete, run:

```bash
node -e "
const fs = require('fs');
// trips.json
const trips = require('./data/trips.json');
const wa = trips.find(t => t.slug === 'west-america-aprile-2026');
console.log('trips.json — accompagnatore:', !!wa.accompagnatore, '| volo:', !!wa.volo, '| itinerary count:', wa.itinerary.length);
// style.css
const css = fs.readFileSync('./assets/css/style.css', 'utf8');
console.log('style.css — tag-pill:', css.includes('.tag-pill'), '| timeline:', css.includes('.timeline-item'), '| volo-card:', css.includes('.volo-card'));
// viaggio.php
const php = fs.readFileSync('./viaggio.php', 'utf8');
console.log('viaggio.php — timeline:', php.includes('timeline-item'), '| accompagnatore:', php.includes('accompagnatore-card'), '| volo:', php.includes('volo-details'), '| pill:', php.includes('tag-pill'));
"
```

All values must be `true` / count >= 15.
</verification>

<success_criteria>
- trips.json: west-america has accompagnatore, volo, and 15-day itinerary with location + image_url fields
- style.css: contains .tag-pill, .timeline-item, .accompagnatore-card, .volo-card rules
- viaggio.php: renders timeline (not accordion), accompagnatore card, Dettagli Volo toggle, tag pills; no accordion JS remaining
- Both sections (accompagnatore, volo) are conditionally rendered and hidden when the field is null/absent
</success_criteria>

<output>
After completion, create `.planning/quick/2-timeline-itinerary-accompagnatore-sectio/2-SUMMARY.md` following the summary template.
</output>
