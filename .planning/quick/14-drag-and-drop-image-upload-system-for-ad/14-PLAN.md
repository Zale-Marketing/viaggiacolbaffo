---
phase: quick-14
plan: 01
type: execute
wave: 1
depends_on: []
files_modified:
  - admin/upload.php
  - uploads/.gitkeep
  - uploads/.htaccess
  - admin/edit-trip.php
autonomous: true
requirements: [QUICK-14]

must_haves:
  truths:
    - "Admin can drag-drop or click to upload an image; a preview appears immediately"
    - "Uploaded file lands in /uploads/ with a safe randomised filename"
    - "Hero, gallery, itinerary, hotel, and accompagnatore fields all use the uploader widget"
    - "Gallery uploader manages multiple images; individual items can be removed"
    - "Non-image uploads and files > 8 MB are rejected with an error message"
    - "PHP execution inside /uploads/ is blocked by .htaccess"
  artifacts:
    - path: "admin/upload.php"
      provides: "Authenticated upload endpoint returning JSON {success, url}"
    - path: "uploads/.htaccess"
      provides: "Block PHP execution in uploads directory"
    - path: "admin/edit-trip.php"
      provides: "All image fields replaced with .img-uploader / .gallery-uploader widgets"
  key_links:
    - from: "admin/edit-trip.php (JS fetch)"
      to: "admin/upload.php"
      via: "POST FormData, reads response.url into hidden input"
      pattern: "fetch.*upload\\.php"
    - from: "admin/upload.php"
      to: "uploads/"
      via: "move_uploaded_file"
      pattern: "move_uploaded_file"
---

<objective>
Replace all plain URL inputs in admin/edit-trip.php with drag-and-drop upload widgets backed by a new PHP upload endpoint. Covers hero image, gallery, itinerary day images, hotel photos, and accompagnatore photo.

Purpose: Operators upload images directly from their desktop instead of pasting Unsplash URLs.
Output: admin/upload.php, uploads/ directory with security rules, fully wired edit-trip.php.
</objective>

<execution_context>
@./.claude/get-shit-done/workflows/execute-plan.md
@./.claude/get-shit-done/templates/summary.md
</execution_context>

<context>
@.planning/STATE.md
</context>

<tasks>

<task type="auto">
  <name>Task 1: Create upload endpoint and uploads/ directory</name>
  <files>admin/upload.php, uploads/.gitkeep, uploads/.htaccess</files>
  <action>
Create admin/upload.php:

```php
<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

if (empty($_SESSION['admin'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorizzato']);
    exit;
}

header('Content-Type: application/json');

$upload_dir = __DIR__ . '/../uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Write .htaccess if missing
$htaccess = $upload_dir . '.htaccess';
if (!file_exists($htaccess)) {
    file_put_contents($htaccess,
        "php_flag engine off\n<FilesMatch \"\\.php$\">\n    Require all denied\n</FilesMatch>\n");
}

if (empty($_FILES['file']['tmp_name'])) {
    echo json_encode(['success' => false, 'error' => 'Nessun file ricevuto']);
    exit;
}

$file      = $_FILES['file'];
$max_bytes = 8 * 1024 * 1024; // 8 MB
$allowed   = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

if ($file['size'] > $max_bytes) {
    echo json_encode(['success' => false, 'error' => 'File troppo grande (max 8 MB)']);
    exit;
}

// Verify MIME via finfo (not Content-Type header)
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);
if (!in_array($mime, $allowed, true)) {
    echo json_encode(['success' => false, 'error' => 'Tipo file non consentito (solo jpg/png/webp/gif)']);
    exit;
}

$ext_map  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
$ext      = $ext_map[$mime];
$safename = bin2hex(random_bytes(12)) . '.' . $ext;
$dest     = $upload_dir . $safename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode(['success' => false, 'error' => 'Errore salvataggio file']);
    exit;
}

echo json_encode(['success' => true, 'url' => '/uploads/' . $safename]);
```

Create uploads/.gitkeep as an empty file.

Create uploads/.htaccess:
```
php_flag engine off
<FilesMatch "\.php$">
    Require all denied
</FilesMatch>
```
  </action>
  <verify>
    <automated>test -f /c/Users/Zanni/viaggiacolbaffo/admin/upload.php && test -f /c/Users/Zanni/viaggiacolbaffo/uploads/.htaccess && echo "OK"</automated>
  </verify>
  <done>admin/upload.php exists with session auth + finfo MIME check + random filename. uploads/.htaccess blocks PHP execution.</done>
</task>

<task type="auto">
  <name>Task 2: Add uploader CSS and JS to edit-trip.php</name>
  <files>admin/edit-trip.php</files>
  <action>
STEP A — Insert CSS inside the existing `<style>` block (place just before the closing `</style>` tag).

Add these rules:

```css
/* ── Image uploader widget ─────────────────────────────────────── */
.img-uploader {
    border: 2px dashed var(--border);
    border-radius: 8px;
    padding: 16px;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    background: #f9fafb;
    position: relative;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.img-uploader:hover,
.img-uploader.drag-over { border-color: #000744; background: #eef0f8; }
.img-uploader .uploader-placeholder { color: #888; font-size: 13px; pointer-events: none; }
.img-uploader .uploader-placeholder i { font-size: 28px; display: block; margin-bottom: 4px; color: #bbb; }
.img-uploader .uploader-preview {
    width: 100%;
    max-height: 200px;
    object-fit: contain;
    border-radius: 6px;
    display: none;
}
.img-uploader .uploader-remove {
    position: absolute; top: 6px; right: 6px;
    background: #CC0031; color: #fff;
    border: none; border-radius: 50%;
    width: 22px; height: 22px;
    font-size: 12px; cursor: pointer;
    display: none; align-items: center; justify-content: center;
}
.img-uploader.has-image .uploader-preview { display: block; }
.img-uploader.has-image .uploader-placeholder { display: none; }
.img-uploader.has-image .uploader-remove { display: flex; }
.img-uploader .uploader-error { color: #CC0031; font-size: 12px; }

/* ── Gallery uploader ───────────────────────────────────────────── */
.gallery-uploader { border: 2px dashed var(--border); border-radius: 8px; padding: 12px; background: #f9fafb; }
.gallery-uploader .gallery-add-btn {
    display: flex; align-items: center; gap: 8px;
    cursor: pointer; color: #000744; font-size: 13px;
    padding: 8px 12px; border-radius: 6px;
    border: 1px solid #000744; background: #fff;
    width: fit-content; margin-bottom: 10px;
    transition: background .15s;
}
.gallery-uploader .gallery-add-btn:hover { background: #eef0f8; }
.gallery-uploader-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
}
@media (max-width: 600px) { .gallery-uploader-grid { grid-template-columns: repeat(2, 1fr); } }
.gallery-uploader-item {
    position: relative; aspect-ratio: 16/9;
    border-radius: 6px; overflow: hidden;
    border: 1px solid var(--border);
}
.gallery-uploader-item img { width: 100%; height: 100%; object-fit: cover; }
.gallery-uploader-item .gallery-item-remove {
    position: absolute; top: 4px; right: 4px;
    background: #CC0031; color: #fff;
    border: none; border-radius: 50%;
    width: 20px; height: 20px; font-size: 11px;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
}
```

STEP B — Insert JS functions before the closing `</script>` tag (at the very end of the script block, but BEFORE the DOMContentLoaded block).

Add:

```javascript
// ─────────────────────────────────────────────────────────────────────────────
// Image upload helpers
// ─────────────────────────────────────────────────────────────────────────────
async function uploadFile(file, statusEl) {
    const fd = new FormData();
    fd.append('file', file);
    if (statusEl) statusEl.textContent = 'Caricamento...';
    try {
        const res  = await fetch('/admin/upload.php', { method: 'POST', body: fd });
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Errore upload');
        return data.url;
    } catch (e) {
        if (statusEl) statusEl.textContent = e.message;
        return null;
    }
}

function initSingleUploader(wrapper) {
    if (!wrapper || wrapper.dataset.uploaderInit) return;
    wrapper.dataset.uploaderInit = '1';
    const input   = wrapper.querySelector('input[type=hidden]');
    const preview = wrapper.querySelector('.uploader-preview');
    const errEl   = wrapper.querySelector('.uploader-error');
    const removeBtn = wrapper.querySelector('.uploader-remove');
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/jpeg,image/png,image/webp,image/gif';
    fileInput.style.display = 'none';
    wrapper.appendChild(fileInput);

    // Restore preview if hidden input already has a value
    if (input && input.value && preview) {
        preview.src = input.value;
        wrapper.classList.add('has-image');
    }

    wrapper.addEventListener('click', e => {
        if (e.target === removeBtn || removeBtn.contains(e.target)) return;
        fileInput.click();
    });
    wrapper.addEventListener('dragover', e => { e.preventDefault(); wrapper.classList.add('drag-over'); });
    wrapper.addEventListener('dragleave', () => wrapper.classList.remove('drag-over'));
    wrapper.addEventListener('drop', async e => {
        e.preventDefault();
        wrapper.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file) await handleSingleFile(file, wrapper, input, preview, errEl);
    });
    fileInput.addEventListener('change', async () => {
        if (fileInput.files[0]) await handleSingleFile(fileInput.files[0], wrapper, input, preview, errEl);
    });
    if (removeBtn) {
        removeBtn.addEventListener('click', e => {
            e.stopPropagation();
            input.value = '';
            preview.src = '';
            preview.style.display = 'none';
            wrapper.classList.remove('has-image');
        });
    }
}

async function handleSingleFile(file, wrapper, input, preview, errEl) {
    if (errEl) errEl.textContent = '';
    const url = await uploadFile(file, errEl);
    if (!url) return;
    input.value = url;
    if (preview) { preview.src = url; }
    wrapper.classList.add('has-image');
}

function initGalleryUploader(wrapper) {
    if (!wrapper || wrapper.dataset.uploaderInit) return;
    wrapper.dataset.uploaderInit = '1';
    const textarea = wrapper.querySelector('textarea');
    const grid     = wrapper.querySelector('.gallery-uploader-grid');
    const addBtn   = wrapper.querySelector('.gallery-add-btn');
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/jpeg,image/png,image/webp,image/gif';
    fileInput.multiple = true;
    fileInput.style.display = 'none';
    wrapper.appendChild(fileInput);

    // Restore existing gallery from textarea
    if (textarea && textarea.value.trim()) {
        textarea.value.split('\n').filter(u => u.trim()).forEach(u => addGalleryItem(u, grid, textarea));
    }

    addBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', async () => {
        for (const f of fileInput.files) {
            const url = await uploadFile(f, null);
            if (url) addGalleryItem(url, grid, textarea);
        }
        fileInput.value = '';
    });

    wrapper.addEventListener('dragover', e => { e.preventDefault(); wrapper.classList.add('drag-over'); });
    wrapper.addEventListener('dragleave', () => wrapper.classList.remove('drag-over'));
    wrapper.addEventListener('drop', async e => {
        e.preventDefault();
        wrapper.classList.remove('drag-over');
        for (const f of e.dataTransfer.files) {
            const url = await uploadFile(f, null);
            if (url) addGalleryItem(url, grid, textarea);
        }
    });
}

function addGalleryItem(url, grid, textarea) {
    const item = document.createElement('div');
    item.className = 'gallery-uploader-item';
    item.dataset.url = url;
    item.innerHTML = `<img src="${escHtml(url)}" alt="gallery" loading="lazy">
        <button type="button" class="gallery-item-remove" title="Rimuovi"><i class="fa-solid fa-xmark"></i></button>`;
    item.querySelector('.gallery-item-remove').addEventListener('click', () => {
        item.remove();
        syncGalleryTextarea(grid, textarea);
    });
    grid.appendChild(item);
    syncGalleryTextarea(grid, textarea);
}

function syncGalleryTextarea(grid, textarea) {
    const urls = Array.from(grid.querySelectorAll('.gallery-uploader-item')).map(d => d.dataset.url);
    textarea.value = urls.join('\n');
}
```

STEP C — Inside the existing DOMContentLoaded handler, REMOVE these old calls and functions:
- Remove the block that calls `updateGalleryPreview(galleryTa.value)` (lines referencing `galleryTa`)
- Remove the block that calls `updateHeroPreview(heroInput.value.trim())` (lines referencing `heroInput` / `updateHeroPreview`)
- Remove functions `updateHeroPreview`, `updateGalleryPreview`, `removeGalleryUrl` (their full function bodies)

Add at the bottom of DOMContentLoaded (before the closing `}`):
```javascript
    // Init all single uploaders
    document.querySelectorAll('.img-uploader').forEach(initSingleUploader);
    // Init gallery uploader
    const galleryUp = document.querySelector('.gallery-uploader');
    if (galleryUp) initGalleryUploader(galleryUp);
```
  </action>
  <verify>
    <automated>grep -c "initSingleUploader\|initGalleryUploader\|img-uploader\|gallery-uploader" /c/Users/Zanni/viaggiacolbaffo/admin/edit-trip.php</automated>
  </verify>
  <done>CSS classes .img-uploader and .gallery-uploader present in style block. JS functions initSingleUploader() and initGalleryUploader() present. Old functions updateHeroPreview/updateGalleryPreview/removeGalleryUrl removed. DOMContentLoaded initialises all widgets.</done>
</task>

<task type="auto">
  <name>Task 3: Replace image fields in edit-trip.php HTML</name>
  <files>admin/edit-trip.php</files>
  <action>
Make the following targeted replacements in admin/edit-trip.php. Preserve all surrounding HTML exactly; only swap the image-input portions.

--- HERO IMAGE ---
Replace the entire `<div class="form-group" ...>` for hero_image input AND the `<div class="hero-preview-wrap">` block (lines ~1001-1014) with:

```html
<div class="img-uploader" id="hero-uploader">
    <img class="uploader-preview" src="<?= htmlspecialchars($trip['hero_image'] ?? '') ?>" alt="Hero">
    <div class="uploader-placeholder"><i class="fa-solid fa-cloud-arrow-up"></i>Trascina o clicca per caricare l'immagine hero</div>
    <div class="uploader-error"></div>
    <button type="button" class="uploader-remove" title="Rimuovi"><i class="fa-solid fa-xmark"></i></button>
    <input type="hidden" name="hero_image" value="<?= htmlspecialchars($trip['hero_image'] ?? '') ?>">
</div>
```

--- GALLERY ---
Replace the entire `<div class="form-group" ...>` for the gallery textarea AND the `<div class="gallery-grid" id="gallery-grid">` block (lines ~1016-1035) with:

```html
<div class="gallery-uploader">
    <button type="button" class="gallery-add-btn"><i class="fa-solid fa-plus"></i> Aggiungi immagini</button>
    <div class="gallery-uploader-grid"></div>
    <textarea name="gallery" style="display:none"><?= htmlspecialchars($gallery_text) ?></textarea>
</div>
```

--- ITINERARY IMAGE (PHP foreach, existing rows) ---
Find the `<input type="url" name="itinerary_image[]"` line inside the PHP foreach that renders existing itinerary rows (around line 1097). Replace that single input with:

```html
<div class="img-uploader">
    <img class="uploader-preview" src="<?= htmlspecialchars($day['image_url'] ?? '') ?>" alt="Immagine giorno">
    <div class="uploader-placeholder"><i class="fa-solid fa-cloud-arrow-up"></i>Immagine giorno</div>
    <div class="uploader-error"></div>
    <button type="button" class="uploader-remove" title="Rimuovi"><i class="fa-solid fa-xmark"></i></button>
    <input type="hidden" name="itinerary_image[]" value="<?= htmlspecialchars($day['image_url'] ?? '') ?>">
</div>
```

--- ITINERARY addItineraryRow() JS TEMPLATE ---
Inside the `addItineraryRow()` function find the string template that contains the `<input type="url" name="itinerary_image[]"` placeholder string (around line 1758). Replace that input string with:

```javascript
'<div class="img-uploader"><img class="uploader-preview" alt="Immagine giorno"><div class="uploader-placeholder"><i class="fa-solid fa-cloud-arrow-up"></i>Immagine giorno</div><div class="uploader-error"></div><button type="button" class="uploader-remove" title="Rimuovi"><i class="fa-solid fa-xmark"></i></button><input type="hidden" name="itinerary_image[]" value=""></div>'
```

After `document.getElementById('itinerary-rows').appendChild(row)` (or the equivalent appendChild call), add:
```javascript
    row.querySelectorAll('.img-uploader').forEach(initSingleUploader);
```

--- HOTEL Foto URL (PHP foreach, existing rows) ---
Find the `<input type="url" name="hotel_foto[]"` inside the PHP foreach for existing hotels (around line 1408). Replace that entire `<div class="form-group" ...>` for Foto URL with:

```html
<div class="form-group" style="margin-bottom:12px;"><label>Foto</label>
    <div class="img-uploader">
        <img class="uploader-preview" src="<?= htmlspecialchars($h['image_url'] ?? '') ?>" alt="Foto hotel">
        <div class="uploader-placeholder"><i class="fa-solid fa-cloud-arrow-up"></i>Foto hotel</div>
        <div class="uploader-error"></div>
        <button type="button" class="uploader-remove" title="Rimuovi"><i class="fa-solid fa-xmark"></i></button>
        <input type="hidden" name="hotel_foto[]" value="<?= htmlspecialchars($h['image_url'] ?? '') ?>">
    </div>
</div>
```

--- addHotelRow() JS TEMPLATE ---
Inside the `addHotelRow()` JS function find the string containing `Foto URL` / `hotel_foto[]` (around line 1784). Replace that Foto form-group string with:

```javascript
+ '<div class="form-group" style="margin-bottom:12px;"><label>Foto</label><div class="img-uploader"><img class="uploader-preview" alt="Foto hotel"><div class="uploader-placeholder"><i class="fa-solid fa-cloud-arrow-up"></i>Foto hotel</div><div class="uploader-error"></div><button type="button" class="uploader-remove" title="Rimuovi"><i class="fa-solid fa-xmark"></i></button><input type="hidden" name="hotel_foto[]" value=""></div></div>'
```

After `document.getElementById('hotels-rows').appendChild(div)`, add:
```javascript
    div.querySelectorAll('.img-uploader').forEach(initSingleUploader);
```

--- ACCOMPAGNATORE Foto ---
Find the `<div class="form-group" ...>` containing `<label>Foto URL</label>` and the `<input type="url" name="accompagnatore_foto"` (around line 1324-1329). Also find and remove the separate `<img>` preview element for accompagnatore if one exists nearby. Replace the entire form-group with:

```html
<div class="form-group" style="margin-bottom:8px;"><label>Foto</label>
    <div class="img-uploader" id="acc-foto-uploader">
        <img class="uploader-preview" src="<?= htmlspecialchars($acc['foto'] ?? '') ?>" alt="Foto accompagnatore">
        <div class="uploader-placeholder"><i class="fa-solid fa-cloud-arrow-up"></i>Foto accompagnatore</div>
        <div class="uploader-error"></div>
        <button type="button" class="uploader-remove" title="Rimuovi"><i class="fa-solid fa-xmark"></i></button>
        <input type="hidden" name="accompagnatore_foto" value="<?= htmlspecialchars($acc['foto'] ?? '') ?>">
    </div>
</div>
```
  </action>
  <verify>
    <automated>grep -c "name=\"hero_image\"\|name=\"gallery\"\|name=\"itinerary_image\[]\"\|name=\"hotel_foto\[]\"\|name=\"accompagnatore_foto\"" /c/Users/Zanni/viaggiacolbaffo/admin/edit-trip.php</automated>
  </verify>
  <done>All five image field groups use .img-uploader or .gallery-uploader markup. No type="url" inputs remain for image fields. Hidden inputs carry the field names. JS templates for addItineraryRow and addHotelRow include uploader markup and call initSingleUploader after appendChild.</done>
</task>

</tasks>

<verification>
1. `admin/upload.php` exists, starts with `session_start()`, uses `finfo` for MIME, `bin2hex(random_bytes(12))` for filename, returns `json_encode(['success'=>true,'url'=>...])`.
2. `uploads/.htaccess` contains `php_flag engine off` and `Require all denied` for .php files.
3. `admin/edit-trip.php` contains zero occurrences of `type="url"` for image fields (hero_image, gallery, itinerary_image, hotel_foto, accompagnatore_foto).
4. `admin/edit-trip.php` contains `initSingleUploader` and `initGalleryUploader` function definitions.
5. `admin/edit-trip.php` does NOT contain `updateHeroPreview`, `updateGalleryPreview`, or `removeGalleryUrl`.
</verification>

<success_criteria>
- Drag a JPEG onto the hero uploader area → file uploads, preview shows, hidden `name="hero_image"` carries `/uploads/xxx.jpg`
- Click "Aggiungi immagini" in gallery → multiple files upload → thumbnails appear in grid → hidden textarea updated
- Save trip → hero_image and gallery stored correctly in trips.json (same POST field names as before)
- Itinerary and hotel rows (existing and newly added) show uploader widgets
- Accompagnatore photo field shows uploader widget
- Uploading a .pdf or a >8MB file returns an error message inside the widget
</success_criteria>

<output>
After completion, create `.planning/quick/14-drag-and-drop-image-upload-system-for-ad/14-SUMMARY.md`
</output>
```
