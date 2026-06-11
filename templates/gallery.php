<?php
/**
 * Gallery shortcode output.
 *
 * Available variables (set in LXPG_Shortcode::render()):
 *   $show_filters  bool
 *   $filter_fields array   – taxonomy slugs + reserved keywords
 *   $active        array   – current active filter values
 *   $posts         WP_Post[]
 *   $atts          array   – raw shortcode atts
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="lxpg-gallery">

    <?php if ( $show_filters ) : ?>
        <?php include __DIR__ . '/partials/filter-bar.php'; ?>
    <?php endif; ?>

    <div class="lxpg-grid" role="list">
        <?php global $post; foreach ( $posts as $post ) : setup_postdata( $post ); ?>
            <?php include __DIR__ . '/partials/project-card.php'; ?>
        <?php endforeach; wp_reset_postdata(); ?>
    </div>

</div>
