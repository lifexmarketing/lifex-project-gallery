<?php
defined( 'ABSPATH' ) || exit;

class LXPG_Settings {

    private const OPTION = 'lxpg_settings';

    public function __construct() {
        add_action( 'admin_menu',    [ $this, 'add_settings_page' ] );
        add_action( 'admin_init',    [ $this, 'register_settings' ] );
        add_filter( 'plugin_action_links_' . plugin_basename( LXPG_FILE ), [ $this, 'add_action_link' ] );
    }

    public static function get( string $key, mixed $default = '' ): mixed {
        $options = get_option( self::OPTION, [] );
        return $options[ $key ] ?? $default;
    }

    public function add_settings_page(): void {
        add_options_page(
            __( 'Project Gallery Settings', 'lifex-project-gallery' ),
            __( 'Project Gallery',           'lifex-project-gallery' ),
            'manage_options',
            'lifex-project-gallery',
            [ $this, 'render_settings_page' ]
        );
    }

    public function register_settings(): void {
        register_setting( 'lxpg_settings_group', self::OPTION, [
            'sanitize_callback' => [ $this, 'sanitize_options' ],
        ] );

        add_settings_section(
            'lxpg_cta_section',
            __( 'Single Project CTA', 'lifex-project-gallery' ),
            null,
            'lifex-project-gallery'
        );

        $fields = [
            'cta_page_id'      => __( 'Contact Page',       'lifex-project-gallery' ),
            'cta_heading'      => __( 'CTA Heading',        'lifex-project-gallery' ),
            'cta_subheading'   => __( 'CTA Subheading',     'lifex-project-gallery' ),
            'cta_button_text'  => __( 'CTA Button Text',    'lifex-project-gallery' ),
        ];

        foreach ( $fields as $id => $label ) {
            add_settings_field(
                'lxpg_' . $id,
                $label,
                [ $this, 'render_field' ],
                'lifex-project-gallery',
                'lxpg_cta_section',
                [ 'key' => $id ]
            );
        }
    }

    public function render_field( array $args ): void {
        $key   = $args['key'];
        $value = self::get( $key );
        $name  = self::OPTION . '[' . $key . ']';

        if ( $key === 'cta_page_id' ) {
            wp_dropdown_pages( [
                'name'              => $name,
                'id'                => 'lxpg_' . $key,
                'selected'          => (int) $value,
                'show_option_none'  => __( '-- None (hide CTA) --', 'lifex-project-gallery' ),
                'option_none_value' => '0',
            ] );
            return;
        }

        printf(
            '<input type="text" name="%s" id="lxpg_%s" value="%s" class="regular-text">',
            esc_attr( $name ),
            esc_attr( $key ),
            esc_attr( $value )
        );
    }

    public function sanitize_options( mixed $input ): array {
        if ( ! is_array( $input ) ) {
            return [];
        }

        $clean = [];
        $clean['cta_page_id']     = absint( $input['cta_page_id'] ?? 0 );
        $clean['cta_heading']     = sanitize_text_field( $input['cta_heading']    ?? 'Love This Project?' );
        $clean['cta_subheading']  = sanitize_text_field( $input['cta_subheading'] ?? 'Get Started on Yours Today!' );
        $clean['cta_button_text'] = sanitize_text_field( $input['cta_button_text'] ?? 'Contact Us' );

        return $clean;
    }

    public function render_settings_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Project Gallery Settings', 'lifex-project-gallery' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'lxpg_settings_group' );
                do_settings_sections( 'lifex-project-gallery' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function add_action_link( array $links ): array {
        $url = admin_url( 'options-general.php?page=lifex-project-gallery' );
        array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'lifex-project-gallery' ) . '</a>' );
        return $links;
    }
}
