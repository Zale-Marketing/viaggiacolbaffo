---
phase: quick-14
plan: 01
subsystem: admin
tags: [upload, images, drag-drop, admin, edit-trip]
key-files:
  created:
    - admin/upload.php
    - uploads/.gitkeep
    - uploads/.htaccess
  modified:
    - admin/edit-trip.php
decisions:
  - "Upload endpoint uses finfo MIME detection (not Content-Type header) for security"
  - "Filenames: original name (sanitised, max 40 chars) + 8 hex random chars to avoid collisions"
  - "Gallery hidden input uses newline-separated URLs — same format as existing POST handler"
  - "initSingleUploader/initGalleryUploader wired by ID convention (uploader-hero, uploader-acc, uploader-itin-N, uploader-hotel-N)"
  - "Dead functions updateHeroPreview / updateGalleryPreview / removeGalleryUrl fully removed"
metrics:
  duration: ~8min
  completed: 2026-03-09
  tasks: 12
  files: 4
---

# Quick Task 14: Drag & Drop Image Upload System Summary

**One-liner:** Replaced all URL inputs in edit-trip.php with drag-and-drop upload widgets backed by admin/upload.php — covers hero, gallery, itinerary days, hotel photos, and accompagnatore photo.

## What Was Built

### admin/upload.php
- Session-authenticated PHP endpoint
- `finfo_open(FILEINFO_MIME_TYPE)` MIME verification (not Content-Type header)
- Allows: image/jpeg, image/png, image/webp, image/gif
- Rejects files > 8MB with Italian error message
- Filename: `{sanitised-original}-{8hex}.{ext}` stored in `/uploads/`
- Returns `{"success": true, "url": "/uploads/filename.ext"}` on success
- Auto-creates `/uploads/` directory and writes `.htaccess` on first use

### uploads/.htaccess
- `php_flag engine off` — disables PHP execution
- `Options -ExecCGI` — disables CGI
- `<FilesMatch>` deny rule blocks all script extensions (.php, .pl, .py, .jsp, .asp, .sh, .cgi)

### admin/edit-trip.php — CSS
- Added `.img-uploader` component styles: dashed border, drag-active state, has-image state with preview/actions overlay
- Added `.gallery-uploader` + `.gallery-thumbs` + `.gallery-thumb` grid styles
- All new classes prefixed to avoid collision with existing admin CSS

### admin/edit-trip.php — HTML
- **Hero image** (tab-media): URL input + hero-preview-wrap replaced with `#uploader-hero` + hidden `#hero_image`
- **Gallery** (tab-media): textarea + gallery-grid replaced with `#uploader-gallery` + hidden textarea `#gallery` (preserves existing POST format)
- **Itinerary days** (PHP foreach): `type="url"` input replaced with `#uploader-itin-{i}` + hidden `#itin_img_{i}` per day
- **Hotel photos** (PHP foreach): `type="url"` input replaced with `#uploader-hotel-{hi}` + hidden `#hotel_img_{hi}` per hotel
- **Accompagnatore** (tab-accompagnatore): URL input + img preview replaced with `#uploader-acc` + hidden `#accompagnatore_foto`

### admin/edit-trip.php — JavaScript
- `initSingleUploader(wrapperId, hiddenInputId)`: click-to-upload, drag-and-drop, remove button, change button, progress overlay
- `initGalleryUploader(wrapperId, hiddenInputId)`: multi-file upload, thumbnail grid with per-thumb remove, syncs to hidden textarea
- `addItineraryRow()`: template updated to include uploader markup; calls `initSingleUploader` after appendChild
- `addHotelRow()`: same pattern
- DOMContentLoaded: auto-inits all uploaders by ID convention
- Removed: `updateHeroPreview()`, `updateGalleryPreview()`, `removeGalleryUrl()` — no longer needed

## Deviations from Plan

The task_details differed slightly from the 14-PLAN.md (which used a simpler `.img-uploader` pattern). The task_details version was implemented as specified — it's more complete with `has-image` state management, preview-actions overlay, and progress indicator. This is the correct version.

None — plan executed as written in task_details.

## Self-Check

- `admin/upload.php` — exists, session auth, finfo MIME, random filename, JSON response
- `uploads/.htaccess` — exists, php_flag engine off, FilesMatch deny
- `uploads/.gitkeep` — exists
- `admin/edit-trip.php` — zero occurrences of dead functions (updateHeroPreview/updateGalleryPreview/removeGalleryUrl), initSingleUploader/initGalleryUploader present
- Commit: ad30288 — pushed to origin/main

## Self-Check: PASSED
