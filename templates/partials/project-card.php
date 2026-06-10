<?php
/**
 * Single project card for the gallery grid.
 * $post is set via setup_postdata() in the parent loop.
 */
defined( 'ABSPATH' ) || exit;

$project_id  = esc_html( get_post_meta( get_the_ID(), 'project_id', true ) );
$permalink   = get_the_permalink();
$title       = get_the_title();
$thumb_src   = get_the_post_thumbnail_url( null, 'project-gallery' );
$thumb_alt   = $title;

// Fall back to first ACF additional image if no featured image.
if ( ! $thumb_src && function_exists( 'get_field' ) ) {
    $additional = get_field( 'additional_images' );
    if ( is_array( $additional ) && ! empty( $additional[0]['url'] ) ) {
        $thumb_src = $additional[0]['sizes']['project-gallery'] ?? $additional[0]['url'];
        $thumb_alt = $additional[0]['alt'] ?: $title;
    }
}

$has_image = (bool) $thumb_src;
?>
<article class="lxpg-card" role="listitem">
    <a href="<?php echo esc_url( $permalink ); ?>"
       class="lxpg-card-link"
       aria-label="<?php echo esc_attr( $title ); ?>">

        <div class="lxpg-card-image-wrap">
            <?php if ( $has_image ) : ?>
                <img
                    src="<?php echo esc_url( $thumb_src ); ?>"
                    alt="<?php echo esc_attr( $thumb_alt ); ?>"
                    class="lxpg-card-image"
                    loading="lazy"
                    decoding="async"
                    width="735"
                    height="489"
                >
            <?php else : ?>
                <div class="lxpg-card-no-image" aria-hidden="true"></div>
            <?php endif; ?>

            <div class="lxpg-card-overlay" aria-hidden="true">
                <div class="lxpg-card-meta">
                    <p class="lxpg-card-title"><?php echo esc_html( $title ); ?></p>
                    <?php if ( $project_id ) : ?>
                        <p class="lxpg-card-id">
                            <?php
                            /* translators: %s is the numeric project ID */
                            printf( esc_html__( 'Project #%s', 'lifex-project-gallery' ), $project_id );
                            ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </a>
</article>
