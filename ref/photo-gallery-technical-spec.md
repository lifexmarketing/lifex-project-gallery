# Photo Gallery — Technical Build Spec

**Companion to:** `photo-gallery-rebuild-plan.md` (strategy) and `photo-gallery-prototype.html` (the working UX and data-model reference)
**Audience:** the developer building this
**Date:** May 29, 2026

This is the implementation-level spec for a new, standalone, WordPress-native photo gallery for Tussey Landscaping that replaces the externally-hosted (`craftsmen.tech`) widget, plus the parallel hardening of the existing Project Gallery plugin. It is grounded in the actual code reviewed and the live data model harvested from the current gallery.

---

## 1. Principles and constraints

- **Standalone, cleanly namespaced.** Working name `lifex-photo-gallery`, prefix `lxpg_`, text domain `lxpg`. It does not modify or depend on `lifex-project-gallery`, and it does not touch that plugin's global query hooks.
- **PHP 8+ safe, class-based, no global query hacks.** No `query_posts()`, no `pre_get_posts` priority-9999 force-load, no PHP4 constructors.
- **Native to the stack.** Beaver Builder module, works with the WP Rocket cache, styled to the child theme.
- **Dependencies:** Beaver Builder required for the module; Advanced Custom Fields not required (the new plugin uses native attachment fields). All dependencies guarded so a missing plugin degrades gracefully.
- **Photos stay a single source of truth.** Images remain ordinary WordPress attachments (and can still live inside `project` posts); the new plugin adds tagging and presentation on top, it does not duplicate image records.

---

## 2. Data model

**Photos = WordPress attachments.** No new post type is required; attachments are already first-class objects with their own title, alt text, caption, description, and file. Tagging is added by registering taxonomies against the `attachment` object type.

**Two taxonomies, registered on `attachment`:**

- `lxpg_category` (hierarchical) — Service Category. Example terms: Ponds & Water Features, Hardscaping, Outdoor Living, Landscapes, Outdoor Lighting, Behind the Scenes. Rewrite slug `photo-gallery/{term}`.
- `lxpg_location` (hierarchical) — Service Area. Parent = state, child = town (State College, Hollidaysburg, Altoona, Duncansville, Huntingdon, Ebensburg, Martinsburg, Roaring Spring). Rewrite slug `photo-gallery/area/{term}`.

**Per-photo data (all native attachment fields):**

- Title → `post_title`
- Alt text → `_wp_attachment_image_alt` meta
- Caption → `post_excerpt`
- Description → `post_content`
- Category and location → the two taxonomy terms above
- Tags (the free descriptive keywords) → reuse the same `lxpg_category` as non-hierarchical labels, or a third flat taxonomy `lxpg_tag` if we want loose keyword filtering separate from the SEO categories. Recommended: keep a small controlled `lxpg_category` set for SEO pages, and a flat `lxpg_tag` for the sales team's finer filtering.

**Why this satisfies both goals:** the sales team filters by any term (category, location, or tag); each photo simultaneously carries its own SEO data (alt, caption, descriptive filename) and rolls up into category and location archive pages that can rank locally.

---

## 3. File structure

```
lifex-photo-gallery/
  lifex-photo-gallery.php          bootstrap: constants, dependency guards, includes, activation (flush rewrites)
  uninstall.php                    optional cleanup
  includes/
    class-taxonomies.php           register lxpg_category, lxpg_location, lxpg_tag on 'attachment'; admin columns + media-library filters
    class-query.php                LXPG_Query: paginated WP_Query for attachments by term(s); sanitized inputs
    class-assets.php               conditional enqueue (only when module/shortcode present); one lightbox; layout lib
    class-shortcode.php            [photo_gallery] with sanitized GET filters + nonce; renders grid + controls
    class-schema.php               ImageObject JSON-LD per photo; image sitemap entries
    class-archive.php              maps lxpg_category / lxpg_location term archives to the gallery template + canonical rules
    class-migrator.php             import harvested catalog (see Section 6); WP-CLI command
  templates/
    gallery.php                    grid + filter controls markup (shared by shortcode, module, archives)
    archive-photo.php              taxonomy archive wrapper (server-side paginated)
  modules/photo-gallery/
    photo-gallery.php              FLBuilderModule registration + style fields (mirror existing module's styling options)
    includes/frontend.php          module template -> outputs [photo_gallery] with settings
    css/frontend.css               grid + overlay styles
    js/frontend.js                 filtering + lightbox (vanilla, ~10KB, deferred)
    icon.svg
  assets/
    lightbox/                      one chosen lightbox lib (e.g. GLightbox) — no Fancybox/Magnific/lightGallery stack
```

---

## 4. Front-end behavior (matches the prototype)

The prototype (`photo-gallery-prototype.html`) is the visual and interaction reference. Production parity points:

- **Filter controls:** category chips, a location dropdown, and a tag search box. Each category chip is a real link to its `lxpg_category` term archive URL (crawlable), with client-side filtering layered on for instant response when staying on one page.
- **Grid:** responsive masonry/justified layout. Use CSS columns or a lightweight justified layout; do not pull in a heavy jQuery layout plugin if a CSS or small-JS solution suffices.
- **Pagination:** real server-side pagination on archive pages (`/photo-gallery/patios/page/2/`) so crawlers follow it, with an optional AJAX "Load more" button as a visitor convenience. Initial page small (about 9-12), increment about 6-12.
- **Lightbox:** one library, deferred until first use. Caption shows title, location, and tag chips (the current gallery already packs these into a caption string, so parity is straightforward).
- **Responsive images:** serve the `-thumb` (480px) in the grid and `-web` (1920px) in the lightbox, and after import generate native WordPress sizes with `srcset`/`sizes` and `loading="lazy"`. Prefer WebP/AVIF.
- **Team / tagging view (admin):** the prototype's "Team view" mocks the real WordPress experience: upload, choose category and town, accept AI-suggested alt text and caption, publish. In production this is the media-library edit screen plus the taxonomy boxes, optionally an "AI suggest alt/caption" helper.

---

## 5. SEO implementation

- **ImageObject JSON-LD** emitted per photo (contentUrl, name, caption, and `locationCreated` from the location term).
- **Image sitemap** entries for gallery images.
- **Clean term-archive URLs** for category and location (Section 2), which are the rankable local pages.
- **Canonical / thin-page handling:** redirect bare attachment pages to their parent or `noindex` them; the value lives in the term-archive listings and image search, not in one-image pages. Only expose a category/location archive once it holds enough distinct, well-described photos (the Phase 4 prune in the strategy doc).
- **Descriptive alt and filename enforcement** at the data layer; the importer writes real alt text rather than leaving camera filenames.

---

## 6. Migration from the external system

The live page exposes everything needed, so vendor account access is helpful but not required.

**Source data model (confirmed by inspection):** each photo has a numeric ID, a thumbnail `…-thumb.jpg` (480px) and a full-size `…-web.jpg` (1920px) on `images.tusseylandscaping.com/gallery/public/`, and a `data-sub-html` caption string of the form `ID: Title | Town, ST<br> tag1, tag2, …`. Alt text follows `Title | Town, ST`.

**Harvest step:** page through the live gallery (the "Load more" button), and for every `.jg-entry` capture: the entry's `href` (`-web` full size), the `img` `src` (`-thumb`), the `alt`, and the parsed `data-sub-html` (ID, title, town/state, tags). Output a JSON manifest.

**Import step (`class-migrator.php`, ideally a WP-CLI command `wp lxpg migrate manifest.json`):**

1. For each record, sideload the `-web` image into the media library (`media_sideload_image` / `wp_insert_attachment` + `wp_generate_attachment_metadata`), deduping by source filename.
2. Set `post_title` = Title, `_wp_attachment_image_alt` = alt, `post_excerpt` = caption.
3. Parse `Town, ST` into a `lxpg_location` term (create parent state / child town as needed).
4. Normalize the free-form comma tags into a **controlled** set: map known synonyms (e.g. "waterfall", "waterfalls", "cascading water" → one term), assign `lxpg_category` for the SEO set and `lxpg_tag` for the rest. Do not import raw tag strings verbatim.
5. Flag records with no town or no tags (raw camera-filename photos like `PANA9641`) into a report for the enrichment pass.

**Enrichment pass:** the flagged photos get AI-drafted alt text and captions with human review (the strategy doc's data pass). This is the real labor and should be estimated as time-and-material with a named owner.

---

## 7. Parallel workstream: harden the existing Project Gallery plugin

Separate from the new gallery, the reviewed `lifex-project-gallery` needs these fixes (all on staging, parity-tested before cutover):

- **Stability:** replace `parent::WP_Widget(...)` with `__construct()`; replace `get_currentuserinfo()` with `wp_get_current_user()`; remove `query_posts()` in the shortcode in favor of a scoped `WP_Query` for the location list.
- **Pagination:** remove or scope the global `pre_get_posts` priority-9999 hook that forces `posts_per_page = -1` on every `project_category` archive; paginate.
- **Security:** sanitize all `$_REQUEST`/`$_POST` filter inputs (`absint` term IDs, `sanitize_text_field` strings), escape all output, add a nonce to the filter form, and standardize filtering on GET.
- **Performance:** stop the global `init`-hook enqueue that loads gallery CSS/JS site-wide; enqueue only where the gallery renders. Consolidate Fancybox + Magnific Popup down to one lightbox (ideally the same library the new gallery uses).
- **Cleanup:** remove the defunct AddThis share widget and the external `via.placeholder.com` images; fix the admin-column filter hooked to the wrong screen id (`manage_edit-project_gallery_columns` vs the `project` post type).
- **Inputs still needed to finalize this list:** the ACF field definitions and `keystone-custom.js`, plus confirmation of where the `Featured_Post` classes are instantiated (they appear to be included but unused).

---

## 8. Security checklist (applies to both)

- All request input sanitized at entry (`absint`, `sanitize_text_field`, `sanitize_title`).
- All output escaped (`esc_html`, `esc_attr`, `esc_url`).
- Nonce verification on every form submission.
- No raw request values in query args or inline JavaScript.
- Capability checks on any admin action.

---

## 9. Rollout

1. Build on a staging copy.
2. Behavior-test the new gallery and the hardened Project Gallery against the live site: same images, filters, and pages render correctly.
3. Validate mobile and Core Web Vitals (the consolidated assets and conditional loading should measurably improve both).
4. Run the migration import on staging; verify the catalog and tags.
5. Cut over; keep the old plugin version and the external widget reachable until the new gallery is confirmed, then retire the `craftsmen.tech` dependency.

---

## 10. Open inputs

- Remaining `lifex-project-gallery` files (ACF field defs, `keystone-custom.js`, instantiation of `Featured_Post`) to finalize Section 7.
- Decision on the controlled taxonomy term list (final category names and the synonym map for tags), settled when the catalog is harvested.
- Confirmation of whether `craftsmen.tech` account access is obtainable (cleaner export) or we rely fully on the page harvest.
