# Migration Guide: v1.x to v2.0

This guide covers converting a site running the old `lifex-project-gallery-standard-1.3` plugin to the new v2.0 plugin.

## What stays the same

The new plugin registers the **same post type, taxonomy, and meta keys** as the old one:

| Item | Slug / Key |
|------|-----------|
| Post type | `project` |
| Taxonomy | `project_category` |
| Image size | `project-gallery` (735x489) |
| Meta: project ID | `project_id` |
| Meta: sq ft | `project_sqft` |
| Meta: color | `project_color` |
| Meta: price range | `project_price_range` |
| Meta: manufacturer | `project_manufacturer` |
| Meta: city | `project_city` |
| Meta: state | `project_state` |
| Meta: ZIP | `project_zip` |
| ACF: gallery images | `additional_images` |
| ACF: featured flag | `featured_project` |
| ACF: featured color | `featured_color` |

All existing project data survives the migration without any database changes.

---

## Steps

### 1. Stage first

Run this on a staging copy of the site before touching production.

### 2. Deactivate the old plugin

In **Plugins**, deactivate (do not delete) `LifeX Project Gallery` v1.x.
Your project posts and all meta data remain intact.

### 3. Install and activate v2.0

Upload or clone this repository to `wp-content/plugins/lifex-project-gallery/`.
Activate the plugin from the Plugins screen.

### 4. Flush permalinks

Go to **Settings > Permalinks** and click **Save Changes**.
This is required because the post type rewrite is re-registered by the new plugin.

### 5. Configure the CTA section

Go to **Settings > Project Gallery** and set the contact page, CTA heading, subheading, and button text.
The default values mirror the old template text.

### 6. Replace shortcodes in content

The old shortcode was `[project_gallery]` (underscore). Find every page or post where it appears and replace it.

**Minimum replacement** (category filter + location + sqft, identical behavior to the old BB module):

```
[project-gallery filters="on" filter_fields="location,sqft"]
```

**With a preset category** (e.g., show only "Decks"):

```
[project-gallery filters="on" filter_fields="location,sqft" category="decks"]
```

**Gallery only, no filters:**

```
[project-gallery]
```

**Limited count:**

```
[project-gallery count="6"]
```

The old shortcode attributes `buttontext` and `lightboxicon` are not used in v2.0 and can be removed.

### 7. Remove the Beaver Builder module (if used)

The old plugin included a BB module that output `[project_gallery]`. Any BB rows containing that module can be replaced with a BB HTML module containing the new `[project-gallery]` shortcode above.

### 8. Verify

- Gallery page renders correctly with filters.
- Individual project pages open with images, details, CTA, and map.
- Lightbox opens on single project image thumbnails.
- Filters narrow results correctly.

### 9. Delete the old plugin

Once verified on staging and confirmed on production, the old plugin can be deleted from the Plugins screen.

---

## What was removed

The new plugin intentionally drops the following from v1.x:

- **Beaver Builder module** -- replaced by the `[project-gallery]` shortcode.
- **`Featured_Post` / `Featured_Post_Widget` classes** -- these were included but unused. The ACF `featured_project` field still controls display order.
- **AddThis / social share inline scripts** -- replaced with clean inline SVG share links.
- **FancyBox + Magnific Popup** -- replaced by a small vanilla JS lightbox with no dependencies.
- **Global `pre_get_posts` hook at priority 9999** -- removed. It was forcing `posts_per_page = -1` on every `project_category` archive, which broke pagination site-wide.
- **`query_posts()` in the shortcode** -- replaced by a scoped `WP_Query`.
- **External placeholder images** -- removed.
- **Unsanitized `$_REQUEST` / `$_POST` usage** -- all inputs are now sanitized and outputs escaped.
