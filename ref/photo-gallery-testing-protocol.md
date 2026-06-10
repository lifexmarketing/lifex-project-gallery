# Photo Gallery — Testing Protocol

**Applies to:** `photo-gallery-prototype.html` (now and through the WordPress build)
**Automated suite:** `photo-gallery-tests.mjs`
**Date:** May 29, 2026

This protocol exists because a real rendering bug slipped through: gallery tiles were invisible until hovered, then disappeared again. That was caused by a CSS multi-column layout combined with a static `transform: translateZ(0)` on each tile, a known browser compositing bug. The protocol below is built to catch that class of issue and the functional regressions that matter for a gallery serving both the Photo Gallery and the Project Gallery.

---

## 1. Automated tests (run these first, every change)

The suite uses Node + jsdom to boot the prototype, exercise the engine, and inspect the rendered DOM.

**Setup and run:**

```
npm i jsdom
HTML_PATH=/absolute/path/photo-gallery-prototype.html node photo-gallery-tests.mjs
```

It exits non-zero on any failure (CI-friendly). Current status: 44 checks passing.

**What it covers:**

- **Regression guards (static):** the grid uses `display:grid` and not CSS multi-column; no static `translateZ` on tiles; tiles carry an always-visible caption; images use `object-fit:cover`; lightbox controls have `aria-label`s; images use native lazy loading.
- **Engine logic:** image URL builder (`-thumb` 480px / `-web` 1920px), the full main-category and sub-tag taxonomy, photo data integrity, and that every sub-tag legitimately belongs to its photo's category.
- **Two-tier filtering:** main category filters; sub-tag narrows within a category; category + sub-tag combinations; location filter; empty/all cases; a category with no photos returns none.
- **Projects:** grouping, covers, and category/sub-tag filtering across a project's photos.
- **Rendered DOM:** cards actually render (the exact failure mode that was missed), every card has a Tussey-host `-thumb` image and a visible caption, the main chip row has All + 7 categories, the sub-tag row is hidden until a category is chosen and then shows All + that category's sub-tags, and the admin tables populate.

**Add a new automated test whenever** a bug is found in manual testing, so it can never silently return.

---

## 2. Manual QA checklist (per release, before sharing)

### 2a. The rendering regression (highest priority)
- [ ] On first load, **all visible tiles show their image immediately, with no hover required.**
- [ ] Images stay visible after the cursor moves away.
- [ ] Verified in Chrome, Safari, Firefox, and Edge.
- [ ] Verified on a real phone and at 360px, 768px, 1024px, and 1440px widths.
- [ ] Scrolling and loading more does not blank previously shown tiles.

### 2b. Two-tier tag filtering
- [ ] Main category chips render (All + the 7 live categories).
- [ ] Selecting a category reveals its sub-tag row; "All" hides it.
- [ ] Each category shows the correct sub-tags (cross-check against the live site list).
- [ ] Selecting a sub-tag narrows results; the count updates.
- [ ] Switching category resets the sub-tag selection.
- [ ] A category with no matching photos shows the empty state, not a blank page.

### 2c. Both gallery modes
- [ ] Photo mode lists individual photos; Project mode lists projects with a cover and a photo count.
- [ ] Filters (category, sub-tag, location, search) work in both modes.
- [ ] Switching modes resets filters cleanly and updates the heading.

### 2d. Lightbox
- [ ] Opens on click; shows the full `-web` image, title, location, category/project context, and sub-tags.
- [ ] Prev/Next cycles; counter is correct; in Project mode it cycles only that project's photos.
- [ ] Esc closes; left/right arrow keys navigate; clicking the backdrop closes.

### 2e. Team / tagging view
- [ ] Add-photo form renders; the sub-tag list updates when the category changes.
- [ ] "Suggest from photo" fills descriptive alt text.
- [ ] Taxonomy table shows all categories and sub-tags; photo table flags the un-tagged photo.

### 2f. Images and resilience
- [ ] All thumbnails load; spot-check several full-size lightbox images.
- [ ] A broken image URL degrades gracefully (no layout collapse).
- [ ] Lazy loading defers off-screen images (check the network panel).

### 2g. Accessibility
- [ ] Every image has meaningful alt text (not a filename).
- [ ] Tiles and controls are keyboard reachable and operable; focus is visible.
- [ ] Text/background contrast meets WCAG AA.
- [ ] Lightbox is escapable by keyboard and announces controls via aria-labels.

### 2h. Performance
- [ ] Gallery CSS/JS load only on gallery pages, not site-wide.
- [ ] One lightbox library only (no Fancybox + Magnific + lightGallery stack).
- [ ] Grid uses responsive sizes; Lighthouse mobile LCP, CLS, and total weight are recorded.

---

## 3. Combined Photo + Project test matrix

One engine serves both galleries, but they filter differently, matching the live site: the **Photo Gallery** uses two-tier category + sub-tag filtering, while the **Project Showcase** filters by category + town only (no sub-tags). Project mode runs on the real 23 projects harvested from `/project-showcase/`.

| Case | Photo mode | Project mode |
| --- | --- | --- |
| No filter (All) | all photos | all projects |
| Main category only | photos in category | projects in category |
| Sub-tag within category | photos with sub-tag | n/a — sub-tag row is hidden in Project mode |
| Location only | photos in town | projects in town (parsed from real titles) |
| Category + location | intersection | intersection |
| Search text | matches title/sub-tags/town | matches project title + town |
| Empty result | empty state shown | empty state shown |
| Open item | single-photo lightbox | project cover + "View full project" link to the live page |

Note: the prototype's Project lightbox shows the project cover and links out to the real project page (only the cover was harvested). The production plugin will show each project's full image set. Project covers currently load the original full-size image in the lightbox; production should serve a sized variant (≈1600px) instead of the raw original for speed.

---

## 4. Pre-production / cutover testing (WordPress build)

Carried into the live plugin, on staging, before any cutover:

- [ ] Parity: the new gallery shows the same photos, categories, sub-tags, and towns as the live site (spot-check counts per category).
- [ ] The hardened Project Gallery still renders its existing projects and category archives.
- [ ] Server-side pagination works and is crawlable (`/photo-gallery/<category>/page/2/`).
- [ ] Category and location archive URLs resolve, render, and carry canonical tags.
- [ ] ImageObject schema validates (Rich Results Test); image sitemap includes gallery images.
- [ ] Works with WP Rocket caching on (filters and load-more behave on cached pages).
- [ ] Migration import verified: counts match, alt text and captions present, sub-tags mapped to the controlled taxonomy, un-tagged photos flagged.
- [ ] Mobile and Core Web Vitals measured against the current page (should improve).
- [ ] Rollback path confirmed (old version retained).

---

## 5. Tooling notes

- The automated suite runs headless via jsdom, which validates structure and logic but not pixels. For true pixel/layout regression testing in the real repo, add Playwright (or Puppeteer) with a couple of screenshot/computed-style assertions, the key one being: a grid tile image has non-zero rendered size and `opacity:1` **without** any hover. (Headless Chromium was not available in the environment where this prototype was built, which is why that check is currently documented as manual.)
- Keep `photo-gallery-tests.mjs` next to the prototype and run it on every edit; treat a red suite as a blocker.

---

## 6. Definition of done

A gallery change ships only when: the automated suite is green, the section 2 manual checklist passes in the four target browsers and at mobile width, the section 3 matrix passes in both modes, and (for production) the section 4 cutover checklist passes on staging.
