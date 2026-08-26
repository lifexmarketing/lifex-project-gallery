<?php
/**
 * Client testimonial block for a single project.
 * $testimonial is set by the parent template — an array with
 * content/rating/client/company, only included when populated.
 */
defined( 'ABSPATH' ) || exit;

$heading = LXPG_Settings::get( 'testimonial_heading', 'Client Testimonial' );
$rating  = max( 0, min( 5, $testimonial['rating'] ) );
?>
<div class="lxpg-testimonial">
    <?php if ( $heading ) : ?>
        <h3 class="lxpg-testimonial-heading"><?php echo esc_html( $heading ); ?></h3>
    <?php endif; ?>

    <?php if ( $rating ) : ?>
        <div class="lxpg-testimonial-rating" aria-label="<?php
            /* translators: %d is the star rating out of 5 */
            printf( esc_attr__( '%d out of 5 stars', 'lifex-project-gallery' ), $rating );
        ?>">
            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                <span class="lxpg-testimonial-star<?php echo $i <= $rating ? ' is-filled' : ''; ?>" aria-hidden="true">&#9733;</span>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

    <?php if ( $testimonial['content'] ) : ?>
        <div class="lxpg-testimonial-content">
            <?php echo wpautop( esc_html( $testimonial['content'] ) ); ?>
        </div>
    <?php endif; ?>

    <?php if ( $testimonial['client'] || $testimonial['company'] ) : ?>
        <p class="lxpg-testimonial-attribution">
            <?php if ( $testimonial['client'] ) : ?>
                <span class="lxpg-testimonial-client"><?php echo esc_html( $testimonial['client'] ); ?></span>
            <?php endif; ?>
            <?php if ( $testimonial['client'] && $testimonial['company'] ) : ?>
                <span>, </span>
            <?php endif; ?>
            <?php if ( $testimonial['company'] ) : ?>
                <span class="lxpg-testimonial-company"><?php echo esc_html( $testimonial['company'] ); ?></span>
            <?php endif; ?>
        </p>
    <?php endif; ?>
</div>
