<?php
defined( 'ABSPATH' ) || exit;

class LXPG_Assets {

    public function __construct() {
        add_action( 'init',               [ $this, 'register' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
    }

    public function register(): void {
        wp_register_style(
            'lxpg-css',
            LXPG_URL . 'assets/css/lifex-project-gallery.css',
            [],
            LXPG_VERSION
        );

        wp_register_script(
            'lxpg-gallery-js',
            LXPG_URL . 'assets/js/gallery.js',
            [],
            LXPG_VERSION,
            [ 'strategy' => 'defer', 'in_footer' => true ]
        );

        wp_register_script(
            'lxpg-lightbox-js',
            LXPG_URL . 'assets/js/lightbox.js',
            [],
            LXPG_VERSION,
            [ 'strategy' => 'defer', 'in_footer' => true ]
        );
    }

    public function enqueue(): void {
        // Single project pages always get CSS + lightbox.
        if ( is_singular( 'project' ) ) {
            wp_enqueue_style( 'lxpg-css' );
            wp_enqueue_script( 'lxpg-lightbox-js' );
            return;
        }

        // Pages containing the shortcode get CSS + gallery JS.
        // has_shortcode() covers classic editor and most page builders that
        // store content in post_content. Page builders that post-process
        // content (Elementor, some BB layouts) will fall back to the
        // enqueue call inside LXPG_Shortcode::render().
        global $post;
        if (
            is_a( $post, 'WP_Post' ) &&
            has_shortcode( $post->post_content, 'project-gallery' )
        ) {
            wp_enqueue_style( 'lxpg-css' );
            wp_enqueue_script( 'lxpg-gallery-js' );
        }
    }

    /**
     * Called by the shortcode as a late-enqueue fallback for page builders.
     * WordPress will move styles to wp_head on next page load via caching,
     * and scripts to wp_footer for this load.
     */
    public static function enqueue_gallery(): void {
        wp_enqueue_style( 'lxpg-css' );
        wp_enqueue_script( 'lxpg-gallery-js' );
    }
}
