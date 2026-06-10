# Photo Gallery Rebuild Plan

**Reference:** tusseylandscaping.com/photo-gallery
**Prepared for:** Cliff Stoltzfoos, LifeX Marketing
**Date:** May 29, 2026
**Status:** Plan / spec for review (no build yet)
**Confirmed constraints:** Ships in WordPress and must run light; hundreds of photos requiring pagination; category/location URLs are in scope; back-catalog data pass is time-and-material with owner still to be assigned.

---

## 1. The short answer

Yes, this is buildable, and we can make it meaningfully better than what is there now without changing the experience enough to upset the client. The current page is a fine-looking gallery, but the part that should be doing the SEO work, the images and the text around them, is mostly invisible to Google today. That is where the opportunity is, and it is also the cheapest part to fix because it is data and markup, not redesign.

The recommendation is to keep the look and the interaction model the visitor already knows (filter tabs, a grid of photos, a "Load more" button, and a click-to-enlarge lightbox) and rebuild the layer underneath it so the gallery is fast, indexable, tied to local service areas, and simple for the team to keep current.

---

## 2. How the current page works

The page is a WordPress page running on Beaver Builder, with the gallery powered by LifeX's own custom plugin (see the stack breakdown just below). From the public markup, the interaction model is:

- A **filter row** beginning with an "All" tab, which implies category filters by service type (patios, ponds, lighting, and so on).
- A **grid of photos** pulled from the WordPress media library as full-size JPEGs.
- A **"Load more" button**, meaning images come in batches rather than all at once.
- **Prev / Next controls**, which almost certainly drive a **lightbox** popup when a photo is clicked.

That is a sound, familiar pattern. We should preserve it.

### The actual tech stack (confirmed by code review)

I inspected the live site's loaded code. It runs on WordPress 7.0 behind Cloudflare, and splits cleanly into design tooling and functional plugins.

**Design / page building (Beaver Builder ecosystem):**

- **Beaver Builder** (`bb-plugin`) is the page builder behind the whole site.
- **Beaver Builder Theme** plus a **child theme** (`bb-theme`, `bb-theme-child`) provide the base design, with customizations isolated in the child theme.
- **Beaver Themer** (active in the page classes) builds the header, footer, and template layouts.
- **PowerPack for Beaver Builder** (`bbpowerpack`), **Ultimate Addons for Beaver Builder** (`bb-ultimate-addon`), and **BB Sticky Column** (`bb-sticky-column`) add extra design modules on top of Beaver Builder.

**Function / utility plugins:**

- **LifeX Project Gallery** (`lifex-project-gallery`) is LifeX's own custom plugin. Code review shows it powers the **Project Gallery / Showcase** (the `project` post type and `[project_gallery]` shortcode). It does **not** render the Photo Gallery, see the note below.
- **The Photo Gallery is a separate, externally-hosted widget.** The live `/photo-gallery/` page is drawn by a custom `gallery.js` and `gallery.css` served from a third-party domain (`craftsmen.tech`), using **lightGallery** from a public CDN (jsDelivr) and **justifiedGallery** for the grid. This is not a WordPress plugin and is not hosted on the client's own site, which is a real ownership, security, and reliability risk: if that external domain changes or goes away, the gallery can break, and a third party is loading code onto the client site. The image set and tags that feed it most likely live in that external system rather than in WordPress, which directly affects migration. (The LifeX plugin's files also load on this page only because of the global-enqueue bug noted under Speed; they are not what renders the grid.)
- **WP Rocket** (`wp-rocket`) handles caching and performance.
- **PixelYourSite** (`pixelyoursite`) manages the Meta Pixel and analytics event tracking.
- **HandL UTM Grabber** (`handl-utm-grabber`) captures UTM and marketing-attribution data into forms.
- **CleanTalk** (`cleantalk-spam-protect`) handles form spam protection.

**What loads on these pages:** the LifeX plugin uses jQuery, **Fancybox**, and **Magnific Popup** for its lightboxes (two lightbox libraries where one would do). The external Photo Gallery separately loads **lightGallery** and **justifiedGallery**. Across the site this stacks up to multiple overlapping lightbox and layout libraries, all jQuery-dependent, which is heavier than needed and a clear, cheap speed win in the rebuild.

This shapes the integration story: "blend seamlessly" does not mean adopting a new tool, and for the Photo Gallery it specifically means getting **off** the external `craftsmen.tech` dependency and into a gallery hosted on the client's own WordPress. The chosen approach (Section 7) is to tag photos individually and build the gallery-and-filter layer in WordPress, exposed as a native Beaver Builder module so the client edits it the same way they edit everything else on the site.

### What is working against the SEO goal right now

The single clearest signal: the page's share image carries the alt text *"A table with orange juice and a newspaper."* That is auto-generated placeholder text from the plugin or theme, and it tells us the image library is very likely not described in a way a search engine can use. For a landscaping company whose entire pitch is visual proof of work, that is the most valuable and most underused asset on the site. The good news is that this is fixable, and fixing it is the highest-return, lowest-cost move in this whole project.

---

## 3. What we are optimizing for

Based on your priorities, the rebuild is judged against four things:

1. **Local SEO** across three levers you flagged: local/geo context, image-level data, and page speed.
2. **Ease for visitors**: fast, smooth, obvious on a phone.
3. **Ease for the team/client**: adding and maintaining photos cannot be a chore, or it will not happen.
4. **Lean cost** throughout: build, hosting, and maintenance.

These mostly reinforce each other. The one real tension is between SEO depth and team effort, which Section 6 addresses head-on.

---

## 4. The SEO layer (where the value is)

This is the core of the project, so it gets the most detail. Three buckets, in priority order.

### 4a. Image-level data

Every photo should carry, as structured fields rather than guesswork:

- **Descriptive, keyword-aware alt text** that names the work and the place, e.g. *"Flagstone patio with built-in fire pit installed in State College, PA by Tussey Landscaping"* instead of a filename or blank.
- **Real filenames** (a descriptive slug like `flagstone-patio-firepit-state-college.jpg`) rather than `state-college-display34.jpg`. Filenames are a genuine image-search ranking signal.
- **A visible caption** under or over the photo. Visible text near an image is something Google can actually read and associate, and it doubles as useful context for the visitor.
- **ImageObject schema** per photo (contentUrl, caption, and where it makes sense, `locationCreated`), so the images are eligible for richer image-search treatment.
- **Inclusion in an image sitemap** so the crawler finds them reliably.

### 4b. Local / geo context

This is the lever most landscaping galleries ignore, and it is where local rankings are won. The idea is to connect each photo to a real place Tussey serves and a real service they sell.

This is now confirmed in scope: each category and location gets its **own indexable URL** instead of one monolithic gallery page. For example, `/photo-gallery/patios/` or `/photo-gallery/state-college/`. A single gallery page can only realistically rank for one or two phrases. A set of category-and-location pages can each rank for the specific "patio installation State College PA" style searches that actually convert.

There is a clean way to do this in WordPress that costs almost nothing extra: these URLs are **taxonomy archive pages**, which WordPress generates and paginates natively (see Section 7a). That means the category/location URLs and the pagination requirement are solved by the same mechanism, server-side and cache-friendly, with no heavy code. The one thing to watch is **thin or duplicate pages**: a category that only has three photos, or photos that appear under several locations, can create weak near-duplicate pages. The plan handles this by only creating a URL once a category/location has enough distinct photos to justify it, and by setting canonical and indexing rules deliberately rather than auto-publishing every combination.

The gallery should also act as an **internal-linking hub**, pointing photos and categories back to the matching service pages and service-area pages that already exist on the site.

### 4c. Page speed / Core Web Vitals

Speed is both a ranking factor and the thing that makes the gallery feel good to use, so it serves two of your goals at once:

- **Responsive images** (`srcset`/`sizes`) so phones download phone-sized files, not 1920px originals.
- **Modern formats** (WebP or AVIF) with sensible compression on thumbnails.
- **Native lazy loading** (`loading="lazy"`) so off-screen photos do not block the initial paint.
- **Batched loading** (the existing "Load more" pattern) to keep the first payload small.
- **Minimal JavaScript**: a lightweight lightbox and filter rather than a heavy plugin stack, with the lightbox code deferred until it is needed.

A lighter codebase here is also the affordability win: less plugin weight, fewer moving parts, less to break.

---

## 5. Visitor experience (kept familiar on purpose)

We hold the interaction model the client and their visitors already know:

- **Filter tabs across the top** that look and feel the same as today, but now each tab is a real link to a category or location URL rather than a piece of client-side JavaScript. The visitor experience is unchanged; underneath, every filter is a crawlable, rankable page. This is the key shift the new constraints make possible, and it is both lighter and better for SEO than the current JS-only filtering.
- A responsive grid (the existing layout, cleaned up).
- **Pagination** for additional photos. Because we have hundreds of images, this is real server-side pagination (`/page/2/`, and so on) that the crawler can follow, with an optional "Load more" button layered on top as a convenience for visitors. Crawlers always have real paginated URLs to follow; visitors get the smooth button.
- A keyboard-accessible lightbox with Prev/Next and swipe on touch.

The visible change should feel like "the same gallery, but faster and tidier," not a redesign. That directly respects your concern about not frustrating the client with a big UI shift.

---

## 6. Team / client maintenance (the part that quietly decides success)

The SEO plan in Section 4 only pays off if the per-photo data actually gets entered, and keeps getting entered as new project photos roll in. If that is hard, it will not happen. So the maintenance workflow is a first-class part of the design, not an afterthought.

Recommended approach: photos live in the WordPress media library the team already uses, with a small, structured set of fields attached to each one (category, service area/location, alt text, caption). Adding a photo becomes: upload, pick a category and a location, confirm the alt text and caption, publish.

To remove the friction that kills these efforts, we can **draft the alt text and captions automatically** from the photo and its category/location, then have a person approve or tweak. That turns a blank-box writing chore into a quick review, which is the difference between this being maintained and abandoned. It also fits LifeX's existing capabilities well.

One honest flag: someone has to do a first pass over the existing back-catalog of photos. That is real labor and the main hidden cost of the project. The realistic plan is to batch it, AI-draft it, and human-review it rather than pretend it is free. Section 7 builds that in.

---

## 7. How it is built in WordPress (lightweight)

**Decision: tag photos individually and add a new gallery layer alongside the existing system (revised May 29, 2026 after team review).** An earlier draft of this plan recommended a standalone photo post type on the mistaken belief that images bundled into a project could not carry their own tags or SEO data. That was wrong: in WordPress every image is an attachment, which is itself a real object that can hold its own taxonomy terms, filename, alt text, and caption. So a photo can stay attached to its project and still be individually tagged and individually optimized. The revised architecture takes advantage of that. It is built native to the stack: a Beaver Builder module, served through WP Rocket, matched to the child theme.

What the code review found in the existing plugin:

- **One post type, `project`, modeled as a case study.** Each project holds a title, description, project meta (ID, square footage, price range, color, manufacturer), and a bundle of images in an ACF gallery field (`additional_images`). The images underneath are ordinary attachments, which means they *can* be tagged and optimized individually even while staying attached to the project.
- **Locations are free-text meta, not a taxonomy** (`project_city`, `project_state`, `project_zip`), de-duplicated at runtime by string match and filtered via query string. There are no clean, rankable location pages yet, and free text is fragile to filter on. We replace this with a real location tag.
- **A global `pre_get_posts` hook (priority 9999) forces every category archive to load all posts with no pagination.** This is risky to modify because it is global, so the new gallery layer is built additively and does not touch it.
- **Aged code carrying risk:** a PHP4-style widget constructor (`parent::WP_Widget(...)`) and `get_currentuserinfo()`, both removed or deprecated in modern PHP. We leave this code alone rather than build on it.
- **Dependencies:** Advanced Custom Fields and Beaver Builder, with Fancybox and Magnific Popup for the lightbox.

Why this approach: it gives the sales team photo-level tag filtering and gives each photo its own local-SEO data (distinct filename, alt, caption, and a location tag), while keeping each photo as a single source of truth under its project, no duplicate copies to maintain. Crucially, it is additive: a new tagging-and-filter layer plus a Beaver Builder module, sitting alongside the project system rather than rewriting the fragile parts of it.

The one risk to manage: mass-tagging near-identical photos with names that differ only by location can create thin, doorway-style pages that search engines discount. The mitigation is genuinely descriptive, varied alt text and captions per photo, and only publishing a category or location page once it holds enough distinct photos to be worth indexing (handled in Phase 4).

Open item: the file reviewed renders the Project Gallery (the `[project_gallery]` shortcode and single-project template). The live Photo Gallery page is rendered by a Beaver Builder module at `bsp-project-gallery-module/bsp-project-gallery-module.php`, which was not in the upload. Worth obtaining to confirm whether its filter and grid front end can be reused for this layer.

The recommended technical shape:

- **Two new taxonomies registered on images (attachments)**: **Service Category** (patios, ponds, lighting, and so on) and **Service Area / Location** (State College, Hollidaysburg, Bedford). Because attachments already carry their own filename, alt text, and caption, each photo becomes individually filterable by the sales team and individually optimized for local search, while still living under its project. Slugs are namespaced so they do not collide with the existing `project_category` taxonomy.
- **The category and location URLs are taxonomy archives**, which WordPress builds and paginates natively. `/photo-gallery/patios/` and `/photo-gallery/state-college/` come essentially for free, render server-side, and are cached by the WP Rocket install already on the site. This is what keeps it light: the work is page rendering plus caching, not browser JavaScript.
- **Expose it as a native Beaver Builder module** so the client adds and arranges galleries with the same drag-and-drop interface they already use for the rest of the site. Nothing new to learn.
- **Consolidate the front-end libraries.** Today the gallery loads justifiedGallery plus three separate lightbox libraries (lightGallery, Fancybox, Magnific Popup). The rebuild keeps one lightbox and the layout library and drops the rest, which is a direct page-speed gain at no design cost.
- **Schema, sitemap, and internal links** are generated from the taxonomy fields, so the SEO markup is produced automatically and consistently rather than typed by hand per page.

The net effect is a gallery that is mostly server-rendered, cached HTML inside the tooling the site already runs, which is the lightest, fastest, and cheapest-to-maintain shape this can take.

## 8. Rebuilding the existing plugin properly

The question was raised whether, rather than only adding a layer alongside the old code, we should fix the underlying problems and rebuild the plugin so it runs the way it should: fast, SEO-sound, cleanly integrated with WordPress and Beaver Builder, more secure, and sustainable for years. The answer is yes, and it is the better long-term call. There are two related deliverables here: modernizing the LifeX plugin that powers the Project Gallery / Showcase (the code reviewed below), and building the new in-house Photo Gallery that replaces the externally-hosted `craftsmen.tech` widget. Both should share one clean, modern codebase so the client has a single, owned, maintainable gallery system instead of an aging plugin plus an external script.

What "properly" means, grouped by the problems found in the code:

**Stability (remove the PHP fatals and deprecated calls).** Replace the PHP4-style widget constructor (`parent::WP_Widget(...)`) with a modern constructor, swap the removed `get_currentuserinfo()` for `wp_get_current_user()`, and drop `query_posts()` in favor of properly scoped queries. These are the items that break or warn on the modern PHP the site now runs.

**Security (treat all input as hostile).** Today the filter form passes `$_REQUEST` values straight into queries and echoes them back into the page and into inline JavaScript, which is an injection and cross-site-scripting exposure. The rebuild sanitizes every input (integers for term IDs, text sanitization for strings), escapes every output, verifies the form with a nonce, and standardizes on clean GET parameters instead of the current mixed POST-and-query-string approach. The single-project template's share links and map embed get the same escaping.

**Speed.** Three fixes matter most. Stop loading the gallery CSS and JavaScript on every page of the site (it is currently enqueued globally) and load it only where a gallery appears. Replace the load-everything queries (`posts_per_page => -1`, including the global hook that forces it on every category archive) with real pagination. Consolidate the three lightbox libraries and the layout library down to one modern, lightweight lightbox, and move images to responsive `srcset` with modern formats and lazy loading. Together these are a large, measurable page-weight reduction.

**SEO.** Clean, crawlable filter URLs; enforced descriptive alt text and captions at the data layer; ImageObject schema and an image sitemap; and proper canonical handling so individual image pages do not become thin duplicates (detail in Section 4).

**Sustainability and integration.** Re-architect from loose global functions with collision-prone names into a small, namespaced, class-based plugin; guard the hard dependencies (Advanced Custom Fields, Beaver Builder) so a missing plugin degrades gracefully instead of fataling; remove dead and external dependencies (the defunct AddThis share widget, the external `via.placeholder.com` images); fix the latent admin-column bug (the column filter is hooked to the wrong screen id and never fires); and expose the gallery as a first-class Beaver Builder module with theme-overridable templates. The result is a plugin a future developer can read, extend, and trust.

**How we do it without breaking the live site.** This plugin runs the production Project Gallery, so the rebuild is done on a staging copy, behavior-tested for parity against the current galleries (same projects, same filters, same images render correctly), checked on mobile and for Core Web Vitals, and only then cut over, with the old version kept for rollback. This is non-negotiable: the upside of a clean rebuild is not worth a broken live gallery, so the testing step is part of the scope, not an optional extra.

**What is needed to scope it precisely.** A proper rebuild of the Project Gallery side needs the complete plugin folder: the JavaScript (`keystone-custom.js`) and CSS, the ACF field definitions, and confirmation of where the `Featured_Post` classes are actually instantiated. (The Beaver Builder module, `bsp-project-gallery-module`, has been reviewed; it is a thin styling wrapper that outputs the `[project_gallery]` shortcode.) With the full folder, this section becomes an exact, file-by-file build spec.

### Migrating the existing photos off the external system

Inspecting the live `/photo-gallery/` page revealed how the external (`craftsmen.tech`) gallery stores each photo, which tells us exactly what migration involves:

- **Per-photo data:** a numeric ID, a thumbnail file and a full-size file on the external host (naming pattern `name-ID-thumb.jpg`, with the full size being the same minus `-thumb`), and a packed caption string holding a title, a location, and a comma-separated tag list (for example, `46: Koi Fish | Hollidaysburg, PA` followed by `pond, water feature, fire...`). Alt text follows a `Subject | Location, State` pattern.
- **The tagging concept already exists**, in primitive form (subject, location, tags per photo), and maps almost directly onto the Service Category and Service Area taxonomies in this plan.
- **Much of the library is not enriched.** A meaningful share of photos still carry raw camera filenames as title and alt (`PANA9641`, `PANA5307`) with no location and no tags. This is concrete confirmation of the data-pass labor, it is real and it is a sizable portion of the catalog.

**Migration approach, and why vendor access is not a blocker.** Because the live page exposes every image URL plus its metadata string, the full catalog can be harvested directly from the rendered gallery: page through all photos, capture each image and its ID, title, location, and tags, download the full-size files, import them into the WordPress media library, and parse the metadata into the new taxonomies. Account access to `craftsmen.tech` would make this cleaner (and is worth pursuing), but it is not required, which removes the main risk from cutting the vendor loose.

**Migration caveats:** confirm the full-size URL pattern before bulk download; normalize the free-form, comma-separated tags into a clean, controlled set of taxonomy terms rather than importing them verbatim; and treat enriching the un-tagged, raw-filename photos as the genuine labor it is (AI-drafted alt text and captions with human review, as in Phase 1).

## 9. Recommended path and phases

A staged path keeps cost lean and lets us prove value before committing to the larger scope.

**Phase 0 — Prototype in Claude (validate before WordPress).**
Build a single self-contained working gallery page here: real filtering, lazy-loaded responsive images, a lightbox, and the full SEO markup (alt text, captions, ImageObject schema) wired in on a sample set of Tussey photos. This is cheap, fast, and gives you something clickable to react to and to show the client. It also becomes the exact reference spec for the WordPress build, so nothing gets lost in translation. *(You chose plan-only for now; this is the natural first build step whenever you want to green-light it.)*

**Phase 1 — Image data foundation.**
Define the category and location taxonomy, then run the back-catalog through AI-drafted alt text + captions with human review. This phase delivers SEO value even before any code ships, because better alt text and filenames help the current page too.

**Phase 2 — Rebuild the plugin properly (on staging).**
Rebuild the plugin as a clean, namespaced, class-based plugin per Section 8: stability and security fixes, conditional asset loading, real pagination, one consolidated lightbox, responsive images, the two image taxonomies, schema, sitemap, and a first-class Beaver Builder module. This serves both the Project Gallery and the new Photo Gallery. All of it on a staging copy.

**Phase 3 — Parity-test and cut over.**
Behavior-test the rebuild against the current live galleries (same projects, filters, and images render correctly), validate mobile and Core Web Vitals, then cut over with the old version retained for rollback. Treat this as a gate, not a formality.

**Phase 4 — Tune and prune.**
Once photos are populated and tagged, decide which category and location URLs have enough distinct images to deserve indexing, set canonicals, and avoid thin or near-duplicate pages. This is where the local-SEO payoff is protected from its own scope.

---

## 10. Decisions locked, and the one still open

**Locked from your answers:**

- Ships in WordPress, built light: custom post type plus taxonomy archives, server-rendered and cached, minimal JavaScript.
- Hundreds of photos: real server-side pagination, with an optional "Load more" for visitors.
- Category/location URLs are in: delivered as taxonomy archives in Phase 2, then pruned in Phase 3 to protect against thin pages.

**Still open, and worth closing before Phase 1:**

**Access to the external Photo Gallery system (`craftsmen.tech`).** Helpful but no longer a blocker. The current photos and metadata can be harvested directly from the live page (see the migration notes in Section 8), so we can leave the vendor without losing the catalog. Account access would make the export cleaner and is worth pursuing, but the project does not depend on it.

The **back-catalog data pass** is time-and-material, but no one currently owns it. That is the honest risk in this project. An unowned T&M task tends to stall, and if the photos never get good local alt text and captions, the whole SEO rationale for the rebuild goes soft. The recommendation is to name an owner now (LifeX or the client), agree on whether we AI-draft with human review to cut the hours, and put a rough hour estimate against it so it is a real line item rather than a someday. The gallery code can ship without it, but the value cannot.

A second, smaller open item worth a quick decision: do the **filter tabs map to categories, to locations, or both?** Both is the strongest for SEO but means more URLs to populate and prune. This can be settled when we define the taxonomy at the start of Phase 1.

---

## 11. Honest assessment

There are now two real efforts here, and it is worth being clear-eyed about both. The first is the discipline of the image data: getting good, local, descriptive text onto every photo and keeping it that way. Any plan that hand-waves that will produce a prettier gallery that ranks no better. The second, added deliberately, is the proper rebuild of the plugin. That is the higher-value, longer-lived investment, but it carries more risk because the plugin runs the live Project Gallery, which is why staging and parity testing (Phases 2 and 3) are written in as a gate rather than a nicety.

The honest tradeoff: the rebuild costs more up front than a bolt-on would, and it touches a working production feature. In exchange you stop paying interest on aging, insecure code, you remove an external third-party dependency from the client's site, and you get a single owned gallery system a future developer can maintain without fear. Given the goals of speed, SEO, security, and sustainability, the rebuild is the right call, provided the testing discipline is respected. The plan stages everything so you see value early (Phases 0 and 1 deliver SEO gains before the rebuild ships) without overcommitting budget. That is the version worth doing.
