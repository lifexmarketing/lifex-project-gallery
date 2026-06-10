<?php
defined( 'ABSPATH' ) || exit;

class LXPG_Meta_Boxes {

    private const FIELDS = [
        'details' => [
            'project_id'          => 'Project ID',
            'project_sqft'        => 'Sq. Ft.',
            'project_color'       => 'Color',
            'project_price_range' => 'Price Range',
            'project_manufacturer'=> 'Manufacturer',
        ],
        'location' => [
            'project_city'  => 'City',
            'project_state' => 'State',
            'project_zip'   => 'ZIP',
        ],
    ];

    public function __construct() {
        add_action( 'admin_init',  [ $this, 'add_meta_boxes' ] );
        add_action( 'save_post',   [ $this, 'save_meta' ] );
    }

    public function add_meta_boxes(): void {
        add_meta_box(
            'lxpg_project_meta',
            __( 'Project Information', 'lifex-project-gallery' ),
            [ $this, 'render_meta_box' ],
            'project',
            'normal',
            'low'
        );
    }

    public function render_meta_box( WP_Post $post ): void {
        wp_nonce_field( 'lxpg_save_meta_' . $post->ID, 'lxpg_meta_nonce' );

        foreach ( self::FIELDS as $section => $fields ) {
            echo '<fieldset style="margin-bottom:1rem;">';
            echo '<legend><strong>' . esc_html( ucfirst( $section ) ) . '</strong></legend><hr/>';

            foreach ( $fields as $key => $label ) {
                $value = get_post_meta( $post->ID, $key, true );
                printf(
                    '<p><label for="%1$s"><strong>%2$s</strong></label><br>
                     <input type="text" name="%1$s" id="%1$s" value="%3$s" style="width:100%%"></p>',
                    esc_attr( $key ),
                    esc_html( $label ),
                    esc_attr( $value )
                );
            }

            echo '</fieldset>';
        }
    }

    public function save_meta( int $post_id ): void {
        $nonce = sanitize_text_field( wp_unslash( $_POST['lxpg_meta_nonce'] ?? '' ) );

        if ( ! wp_verify_nonce( $nonce, 'lxpg_save_meta_' . $post_id ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $all_fields = array_merge( ...array_values( self::FIELDS ) );

        foreach ( array_keys( $all_fields ) as $key ) {
            if ( isset( $_POST[ $key ] ) ) {
                update_post_meta(
                    $post_id,
                    $key,
                    sanitize_text_field( wp_unslash( $_POST[ $key ] ) )
                );
            }
        }
    }
}
