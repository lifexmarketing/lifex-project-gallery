<?php
/**
 * Single project page template.
 * Mirrors the structure of the legacy project-gallery-single.php,
 * rebuilt with clean markup, no jQuery, inline SVG icons, and accessibility.
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>

<?php
// ── Collect data ─────────────────────────────────────────────────────────────
$post_id      = get_the_ID();
$title        = get_the_title();
$permalink    = get_the_permalink();
$manufacturer = esc_html( get_post_meta( $post_id, 'project_manufacturer', true ) );
$color        = esc_html( get_post_meta( $post_id, 'project_color',        true ) );
$project_id   = esc_html( get_post_meta( $post_id, 'project_id',           true ) );
$sqft         = esc_html( get_post_meta( $post_id, 'project_sqft',         true ) );
$city         = esc_html( get_post_meta( $post_id, 'project_city',         true ) );
$state        = esc_html( get_post_meta( $post_id, 'project_state',        true ) );
$zip          = esc_html( get_post_meta( $post_id, 'project_zip',          true ) );

// Price range: stored as "30000-60000" → format to "$30,000 - $60,000"
$price_raw    = get_post_meta( $post_id, 'project_price_range', true );
$price_parts  = array_map( 'trim', explode( '-', (string) $price_raw ) );
$price_min    = ( isset( $price_parts[0] ) && is_numeric( $price_parts[0] ) ) ? number_format( (float) $price_parts[0] ) : '';
$price_max    = ( isset( $price_parts[1] ) && is_numeric( $price_parts[1] ) ) ? number_format( (float) $price_parts[1] ) : '';

// Subtitle — parsed from template
$subtitle = LXPG_Settings::compute_subtitle( $post_id );

// ── Build gallery image list ──────────────────────────────────────────────────
$gallery = [];

if ( has_post_thumbnail() ) {
    $gallery[] = [
        'full'  => get_the_post_thumbnail_url( null, 'full' ),
        'thumb' => get_the_post_thumbnail_url( null, 'project-gallery' ),
        'alt'   => $title,
    ];
}

if ( function_exists( 'get_field' ) ) {
    $additional = get_field( 'additional_images' );
    if ( is_array( $additional ) ) {
        foreach ( $additional as $i => $img ) {
            if ( $i === 0 ) {
                continue; // index 0 mirrors the featured image
            }
            if ( empty( $img['url'] ) ) {
                continue;
            }
            $gallery[] = [
                'full'  => $img['url'],
                'thumb' => $img['sizes']['project-gallery-thumb'] ?? $img['sizes']['thumbnail'] ?? $img['url'],
                'alt'   => $img['alt'] ?: ( $img['title'] ?? $title ),
            ];
        }
    }
}

$has_gallery = ! empty( $gallery );

// ── ACF detail fields (configured in Settings > Single Project Content) ───────
$detail_rows = [];
$detail_fields_raw = LXPG_Settings::get( 'project_detail_fields', '' );
if ( $detail_fields_raw !== '' && function_exists( 'get_field' ) ) {
    foreach ( array_filter( array_map( 'trim', explode( ',', $detail_fields_raw ) ) ) as $field_name ) {
        $field_name = sanitize_key( $field_name );
        $value      = get_field( $field_name, $post_id );
        if ( ! is_scalar( $value ) || $value === '' || $value === false ) {
            continue;
        }
        $field_obj      = get_field_object( $field_name, $post_id );
        $detail_rows[]  = [
            'label' => ( $field_obj && ! empty( $field_obj['label'] ) )
                ? $field_obj['label']
                : ucwords( str_replace( [ '_', '-' ], ' ', $field_name ) ),
            'value' => (string) $value,
        ];
    }
}

// ── Settings ──────────────────────────────────────────────────────────────────
$cta_page_id    = (int) LXPG_Settings::get( 'cta_page_id', 0 );
$cta_url        = $cta_page_id ? get_permalink( $cta_page_id ) : '';
$cta_heading    = LXPG_Settings::get( 'cta_heading',     'Love This Project? Get Started on Yours Today?' );
$cta_btn        = LXPG_Settings::get( 'cta_button_text', 'Contact Us' );

// ── Inline content CTA ────────────────────────────────────────────────────────
$content_cta_url    = '';
$content_cta_target = '_blank';
$content_cta_rel    = 'noopener noreferrer';
if ( LXPG_Settings::get( 'content_cta_enabled', '' ) === '1' ) {
    $link_type = LXPG_Settings::get( 'content_cta_link_type', 'acf' );
    if ( $link_type === 'url' ) {
        $content_cta_url = LXPG_Settings::get( 'content_cta_static_url', '' );
    } elseif ( $link_type === 'phone' ) {
        $phone_raw = LXPG_Settings::get( 'content_cta_phone', '' );
        if ( $phone_raw !== '' ) {
            $content_cta_url    = 'tel:' . preg_replace( '/[^\d+]/', '', $phone_raw );
            $content_cta_target = '';
            $content_cta_rel    = '';
        }
    } elseif ( function_exists( 'get_field' ) ) {
        $content_cta_field = LXPG_Settings::get( 'content_cta_acf_field', '' );
        if ( $content_cta_field !== '' ) {
            $raw_url = get_field( sanitize_key( $content_cta_field ), $post_id );
            if ( is_string( $raw_url ) && $raw_url !== '' ) {
                $content_cta_url = $raw_url;
            }
        }
    }
}
$content_cta_text = LXPG_Settings::get( 'content_cta_text', 'Visit This Website' ) ?: 'Visit This Website';

// ── Client testimonial (optional addon) ────────────────────────────────────────
$testimonial = null;
if ( LXPG_Settings::get( 'testimonial_enabled', '' ) === '1' && function_exists( 'get_field' ) && post_type_exists( 'wpm-testimonial' ) ) {
    $testimonial_id = get_field( 'project_testimonial', $post_id );

    if ( $testimonial_id ) {
        $testimonial_content = get_post_field( 'post_content', $testimonial_id );
        $testimonial_title   = get_the_title( $testimonial_id );

        if ( $testimonial_content || $testimonial_title ) {
            // Strong Testimonials stores its data in post meta on the testimonial post.
            $testimonial = [
                'content' => $testimonial_content,
                'rating'  => (int) get_post_meta( $testimonial_id, 'star_rating', true ),
                'client'  => get_post_meta( $testimonial_id, 'client_name', true ),
                'company' => get_post_meta( $testimonial_id, 'company_name', true ),
            ];
        }
    }
}
?>

<main class="lxpg-single" role="main">
    <div class="lxpg-single-inner">

        <?php if ( $has_gallery ) : ?>
        <div class="lxpg-single-layout">

            <!-- Left: details -->
            <div class="lxpg-single-details">
                <h1 class="lxpg-single-title"><?php echo esc_html( $title ); ?></h1>

                <!-- Social sharing -->
                <div class="lxpg-share" aria-label="<?php esc_attr_e( 'Share this project', 'lifex-project-gallery' ); ?>">
                    <span class="lxpg-share-label"><?php esc_html_e( 'Share This Project:', 'lifex-project-gallery' ); ?></span>

                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( $permalink ); ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="lxpg-share-btn lxpg-share-btn--facebook"
                       aria-label="<?php esc_attr_e( 'Share on Facebook', 'lifex-project-gallery' ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                        </svg>
                    </a>

                    <a href="https://x.com/intent/tweet?text=<?php echo rawurlencode( $title ); ?>&url=<?php echo rawurlencode( $permalink ); ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="lxpg-share-btn lxpg-share-btn--x"
                       aria-label="<?php esc_attr_e( 'Share on X', 'lifex-project-gallery' ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>

                    <?php
                    $pinterest_img = ! empty( $gallery[0]['full'] ) ? $gallery[0]['full'] : '';
                    ?>
                    <a href="https://pinterest.com/pin/create/button/?url=<?php echo rawurlencode( $permalink ); ?>&media=<?php echo rawurlencode( $pinterest_img ); ?>&description=<?php echo rawurlencode( $title ); ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="lxpg-share-btn lxpg-share-btn--pinterest"
                       aria-label="<?php esc_attr_e( 'Save to Pinterest', 'lifex-project-gallery' ); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 0 1 .083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.632-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/>
                        </svg>
                    </a>
                </div>

                <?php if ( $subtitle ) : ?>
                    <h2 class="lxpg-single-subtitle"><?php echo esc_html( $subtitle ); ?></h2>
                <?php endif; ?>

                <div class="lxpg-single-content">
                    <?php the_content(); ?>
                </div>

                <?php if ( ! empty( $detail_rows ) ) : ?>
                <ul class="lxpg-single-acf-fields">
                    <?php foreach ( $detail_rows as $row ) : ?>
                        <li><strong><?php echo esc_html( $row['label'] ); ?>:</strong> <?php echo esc_html( $row['value'] ); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <ul class="lxpg-single-meta">
                    <?php if ( $project_id ) : ?>
                        <li><strong><?php esc_html_e( 'ID:', 'lifex-project-gallery' ); ?></strong> <?php echo $project_id; ?></li>
                    <?php endif; ?>
                    <?php if ( $sqft ) : ?>
                        <li><strong><?php esc_html_e( 'Sq Ft:', 'lifex-project-gallery' ); ?></strong> <?php echo $sqft; ?></li>
                    <?php endif; ?>
                    <?php if ( $manufacturer ) : ?>
                        <li><strong><?php esc_html_e( 'Type:', 'lifex-project-gallery' ); ?></strong> <?php echo $manufacturer; ?></li>
                    <?php endif; ?>
                    <?php if ( $color ) : ?>
                        <li><strong><?php esc_html_e( 'Color:', 'lifex-project-gallery' ); ?></strong> <?php echo $color; ?></li>
                    <?php endif; ?>
                    <?php if ( $price_min && $price_max ) : ?>
                        <li><strong><?php esc_html_e( 'Price Range:', 'lifex-project-gallery' ); ?></strong> $<?php echo $price_min; ?> &ndash; $<?php echo $price_max; ?></li>
                    <?php endif; ?>
                </ul>

                <?php if ( $testimonial ) : ?>
                    <?php include LXPG_DIR . 'templates/partials/testimonial.php'; ?>
                <?php endif; ?>

                <?php if ( $content_cta_url ) : ?>
                    <div class="lxpg-content-cta">
                        <a href="<?php echo esc_url( $content_cta_url ); ?>" class="lxpg-content-cta-link"<?php if ( $content_cta_target ) echo ' target="' . esc_attr( $content_cta_target ) . '" rel="' . esc_attr( $content_cta_rel ) . '"'; ?>>
                            <?php echo esc_html( $content_cta_text ); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: gallery -->
            <div class="lxpg-single-gallery" id="lxpg-gallery" data-lightbox="project">

                <?php if ( ! empty( $gallery[0] ) ) : ?>
                    <a href="<?php echo esc_url( $gallery[0]['full'] ); ?>"
                       class="lxpg-gallery-main-link"
                       data-lxpg-index="0"
                       aria-label="<?php esc_attr_e( 'View full-size image', 'lifex-project-gallery' ); ?>">
                        <img
                            src="<?php echo esc_url( $gallery[0]['thumb'] ); ?>"
                            alt="<?php echo esc_attr( $gallery[0]['alt'] ); ?>"
                            class="lxpg-gallery-main-img"
                            loading="eager"
                            decoding="async"
                        >
                    </a>
                <?php endif; ?>

                <?php if ( count( $gallery ) > 1 ) : ?>
                    <div class="lxpg-gallery-thumbs" role="list" aria-label="<?php esc_attr_e( 'Additional images', 'lifex-project-gallery' ); ?>">
                        <?php foreach ( $gallery as $i => $img ) : ?>
                            <a href="<?php echo esc_url( $img['full'] ); ?>"
                               class="lxpg-gallery-thumb<?php echo $i === 0 ? ' is-active' : ''; ?>"
                               data-lxpg-index="<?php echo (int) $i; ?>"
                               role="listitem"
                               aria-label="<?php
                                    /* translators: 1: current image number, 2: total images */
                                    printf( esc_attr__( 'Image %1$d of %2$d', 'lifex-project-gallery' ), $i + 1, count( $gallery ) );
                               ?>">
                                <img
                                    src="<?php echo esc_url( $img['thumb'] ); ?>"
                                    alt="<?php echo esc_attr( $img['alt'] ); ?>"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php
                // Pass gallery data to JS via a data attribute on a hidden element.
                $gallery_json = wp_json_encode( array_values( $gallery ) );
                ?>
                <script id="lxpg-gallery-data" type="application/json"><?php echo $gallery_json; ?></script>

            </div>

        </div><!-- .lxpg-single-layout -->

        <?php else : /* No gallery images — full-width layout */ ?>

        <div class="lxpg-single-full">
            <h1 class="lxpg-single-title"><?php echo esc_html( $title ); ?></h1>
            <?php if ( $subtitle ) : ?>
                <h2 class="lxpg-single-subtitle"><?php echo esc_html( $subtitle ); ?></h2>
            <?php endif; ?>
            <div class="lxpg-single-content"><?php the_content(); ?></div>
            <?php if ( ! empty( $detail_rows ) ) : ?>
            <ul class="lxpg-single-acf-fields">
                <?php foreach ( $detail_rows as $row ) : ?>
                    <li><strong><?php echo esc_html( $row['label'] ); ?>:</strong> <?php echo esc_html( $row['value'] ); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <ul class="lxpg-single-meta">
                <?php if ( $project_id ) : ?>
                    <li><strong><?php esc_html_e( 'ID:', 'lifex-project-gallery' ); ?></strong> <?php echo $project_id; ?></li>
                <?php endif; ?>
                <?php if ( $sqft ) : ?>
                    <li><strong><?php esc_html_e( 'Sq Ft:', 'lifex-project-gallery' ); ?></strong> <?php echo $sqft; ?></li>
                <?php endif; ?>
                <?php if ( $price_min && $price_max ) : ?>
                    <li><strong><?php esc_html_e( 'Price Range:', 'lifex-project-gallery' ); ?></strong> $<?php echo $price_min; ?> &ndash; $<?php echo $price_max; ?></li>
                <?php endif; ?>
            </ul>
            <?php if ( $testimonial ) : ?>
                <?php include LXPG_DIR . 'templates/partials/testimonial.php'; ?>
            <?php endif; ?>
            <?php if ( $content_cta_url ) : ?>
                <div class="lxpg-content-cta">
                    <a href="<?php echo esc_url( $content_cta_url ); ?>" class="lxpg-content-cta-link"<?php if ( $content_cta_target ) echo ' target="' . esc_attr( $content_cta_target ) . '" rel="' . esc_attr( $content_cta_rel ) . '"'; ?>>
                        <?php echo esc_html( $content_cta_text ); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php endif; ?>

    </div><!-- .lxpg-single-inner -->

    <?php
    if ( LXPG_Settings::get( 'pagination_enabled', '' ) === '1' ) :
        $prev_post = get_next_post();     // intentionally swapped: newest-first listing
        $next_post = get_previous_post();
        if ( $prev_post || $next_post ) :
    ?>
    <div class="lxpg-pagination">
        <div class="lxpg-pagination-prev">
            <?php if ( $prev_post ) : ?>
                <a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>">
                    &lsaquo; <?php echo esc_html( $prev_post->post_title ); ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="lxpg-pagination-next">
            <?php if ( $next_post ) : ?>
                <a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>">
                    <?php echo esc_html( $next_post->post_title ); ?> &rsaquo;
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <?php if ( $cta_url ) : ?>
    <div class="lxpg-cta">
        <div class="lxpg-cta-inner">
            <?php if ( $cta_heading ) : ?>
                <div class="lxpg-cta-text">
                    <span class="lxpg-cta-heading"><?php echo esc_html( $cta_heading ); ?></span>
                </div>
            <?php endif; ?>
            <a href="<?php echo esc_url( $cta_url ); ?>" class="lxpg-cta-link">
                <?php echo esc_html( $cta_btn ); ?>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <?php if ( $city || $state ) : ?>
    <div class="lxpg-map-wrap">
        <div class="lxpg-map-inner">
            <?php if ( $city || $state ) : ?>
                <h3 class="lxpg-map-location">
                    <?php echo trim( $city . ( $city && $state ? ', ' : '' ) . $state . ( $zip ? ' ' . $zip : '' ) ); ?>
                </h3>
            <?php endif; ?>
            <iframe
                class="lxpg-map"
                src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( trim( "$city, $state" ) ) . '&output=embed' ); ?>"
                title="<?php
                    /* translators: %s is city and state */
                    printf( esc_attr__( 'Map showing %s', 'lifex-project-gallery' ), esc_attr( "$city, $state" ) );
                ?>"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </div>
    </div>
    <?php endif; ?>

</main>

<?php endwhile; ?>

<?php get_footer(); ?>
