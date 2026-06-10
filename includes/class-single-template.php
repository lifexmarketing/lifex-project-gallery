<?php
defined( 'ABSPATH' ) || exit;

class LXPG_Single_Template {

    public function __construct() {
        add_filter( 'single_template', [ $this, 'override' ] );
    }

    public function override( string $template ): string {
        global $post;

        if ( $post instanceof WP_Post && $post->post_type === 'project' ) {
            $plugin_template = LXPG_DIR . 'templates/single-project.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }

        return $template;
    }
}
