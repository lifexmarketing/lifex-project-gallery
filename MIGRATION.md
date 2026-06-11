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

## Shortcode reference

The shortcode is `[project-gallery]` (hyphen, not underscore).

### Attributes

| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `filters` | `on`, `off` | `off` | Show the filter bar above the gallery. Category filter is always included when `on`. |
| `filter_fields` | comma-separated list | _(empty)_ | Controls which additional filters appear. See filter field resolution below. |
| `count` | integer or `-1` | `-1` | Number of projects to display. `-1` shows all. |
| `category` | taxonomy slug | _(empty)_ | Pre-filters the gallery to a specific project category. Does not show a category filter dropdown. |
| `card_label` | `title`, `project_id` | `title` | What appears in the caption bar below each card. `title` shows the project title; `project_id` shows "Project #[ID]". |

### Filter field resolution

Each value in `filter_fields` is resolved in this order:

1. **Reserved keywords** -- `location` and `sqft` have built-in behavior.
   - `location` -- builds a dropdown of unique City, State combinations from project meta.
   - `sqft` -- renders a fixed set of square footage range options.
2. **Taxonomy slug** -- if `taxonomy_exists($value)` is true, renders a term dropdown for that taxonomy.
3. **ACF / post meta field** -- falls through to query distinct values of that meta key across published projects and renders a dropdown.

### Examples

**Basic gallery, no filters:**
```
[project-gallery]
```

**Gallery with filters (category always included):**
```
[project-gallery filters="on" filter_fields="location,sqft"]
```

**Filters including a custom taxonomy and an ACF field:**
```
[project-gallery filters="on" filter_fields="location,design_type,project_manufacturer"]
```

**Pre-filtered to a category, with additional filters:**
```
[project-gallery filters="on" filter_fields="location,sqft" category="decks"]
```

**Show only 6 projects:**
```
[project-gallery count="6"]
```

**Show project ID in card caption instead of title:**
```
[project-gallery filters="on" filter_fields="location,sqft" card_label="project_id"]
```

---

## Migration steps

### 1. Stage first

Run this on a staging copy of the site before touching production.

### 2. Rename the old plugin folder

On the server, rename the existing plugin folder from `lifex-project-gallery` to `lifex-project-gallery-v1`. This deactivates the old plugin without deleting any data.

### 3. Install and activate v2.0

Upload or clone this repository to `wp-content/plugins/lifex-project-gallery/`.
Activate the plugin from the Plugins screen.

### 4. Flush permalinks

Go to **Settings > Permalinks** and click **Save Changes**.
This is required because the post type rewrite is re-registered by the new plugin.

### 5. Configure the CTA section

Go to **Settings > Project Gallery** and set the contact page, CTA heading, and button text.
The default values mirror the old template text.

### 6. Replace shortcodes in content

The old shortcode was `[project_gallery]` (underscore). Find every page or post where it appears and replace it using the shortcode reference above.

**Minimum replacement** (equivalent behavior to the old Beaver Builder module):
```
[project-gallery filters="on" filter_fields="location,sqft"]
```

The old shortcode attributes `buttontext` and `lightboxicon` are not used in v2.0 and can be removed.

### 7. Remove the Beaver Builder module (if used)

Any BB rows containing the old project gallery module can be replaced with a BB HTML module containing the new `[project-gallery]` shortcode.

### 8. Verify

- Gallery page renders with project images and correct links.
- Filters narrow results correctly.
- Individual project pages open with images, details, CTA, and map.
- Lightbox opens on single project image thumbnails.

### 9. Delete the old plugin

Once verified on staging and confirmed on production, the old `lifex-project-gallery-v1` folder can be deleted.

---

## What was removed

The new plugin intentionally drops the following from v1.x:

- **Beaver Builder module** -- replaced by the `[project-gallery]` shortcode.
- **`Featured_Post` / `Featured_Post_Widget` classes** -- unused. The ACF `featured_project` field still controls display order.
- **AddThis / social share inline scripts** -- replaced with clean inline SVG share links.
- **FancyBox + Magnific Popup** -- replaced by a small vanilla JS lightbox with no dependencies.
- **Global `pre_get_posts` hook at priority 9999** -- removed. It was forcing `posts_per_page = -1` on every `project_category` archive, which broke pagination site-wide.
- **`query_posts()` in the shortcode** -- replaced by a scoped `WP_Query`.
- **External placeholder images** -- removed.
- **Unsanitized `$_REQUEST` / `$_POST` usage** -- all inputs are now sanitized and outputs escaped.
