<?php
defined( 'ABSPATH' ) || exit;

class LXPG_Settings {

    private const OPTION = 'lxpg_settings';

    /**
     * All configurable fields, grouped into sections.
     *
     * Types:
     *   color  — WordPress color picker (hex/rgb)
     *   rgba   — plain text input, expects rgba(...) syntax
     *   select — <select> with options key
     *   text   — plain text input
     *   page   — wp_dropdown_pages
     *
     * 'property' maps to a CSS custom property. Fields without 'property'
     * are content settings (strings, page IDs) not output as CSS.
     */
    private const SECTIONS = [
        'global' => [
            'label'  => 'Global',
            'fields' => [
                'css_radius' => [
                    'label'    => 'Border Radius',
                    'type'     => 'text',
                    'default'  => '4px',
                    'property' => '--lxpg-radius',
                    'hint'     => 'Applied to cards, buttons, and selects. e.g. 4px or 0.5rem',
                ],
            ],
        ],
        'grid' => [
            'label'  => 'Gallery Grid',
            'fields' => [
                'css_grid_gap' => [
                    'label'    => 'Card Gap',
                    'type'     => 'text',
                    'default'  => '1rem',
                    'property' => '--lxpg-grid-gap',
                    'hint'     => 'Space between cards. e.g. 1rem or 16px',
                ],
                'css_card_ratio' => [
                    'label'    => 'Image Aspect Ratio',
                    'type'     => 'select',
                    'default'  => '3 / 2',
                    'property' => '--lxpg-card-ratio',
                    'options'  => [
                        '3 / 2'   => 'Landscape 3:2 (default)',
                        '4 / 3'   => 'Landscape 4:3',
                        '16 / 9'  => 'Widescreen 16:9',
                        '1 / 1'   => 'Square',
                    ],
                ],
                'css_card_overlay_bg' => [
                    'label'    => 'Hover Overlay Color',
                    'type'     => 'rgba',
                    'default'  => 'rgba(0,0,0,0.62)',
                    'property' => '--lxpg-card-overlay-bg',
                    'hint'     => 'Use rgba() to control opacity. e.g. rgba(0,0,0,0.6)',
                ],
                'css_card_text' => [
                    'label'    => 'Overlay Text Color',
                    'type'     => 'color',
                    'default'  => '#ffffff',
                    'property' => '--lxpg-card-text',
                ],
            ],
        ],
        'filters' => [
            'label'  => 'Filter Bar',
            'fields' => [
                'css_filter_border' => [
                    'label'    => 'Select Border Color',
                    'type'     => 'color',
                    'default'  => '#d1d5db',
                    'property' => '--lxpg-filter-border',
                ],
                'css_filter_bg' => [
                    'label'    => 'Select Background',
                    'type'     => 'color',
                    'default'  => '#ffffff',
                    'property' => '--lxpg-filter-bg',
                ],
                'css_filter_text' => [
                    'label'    => 'Select Text Color',
                    'type'     => 'color',
                    'default'  => '#1f2937',
                    'property' => '--lxpg-filter-text',
                ],
                'css_filter_focus' => [
                    'label'    => 'Focus Ring Color',
                    'type'     => 'color',
                    'default'  => '#111827',
                    'property' => '--lxpg-filter-focus',
                    'hint'     => 'Keyboard focus outline on selects, buttons, and cards.',
                ],
                'css_btn_bg' => [
                    'label'    => 'Filter Button Background',
                    'type'     => 'color',
                    'default'  => '#1f2937',
                    'property' => '--lxpg-btn-bg',
                ],
                'css_btn_text' => [
                    'label'    => 'Filter Button Text',
                    'type'     => 'color',
                    'default'  => '#ffffff',
                    'property' => '--lxpg-btn-text',
                ],
                'css_btn_hover_bg' => [
                    'label'    => 'Filter Button Hover Background',
                    'type'     => 'color',
                    'default'  => '#374151',
                    'property' => '--lxpg-btn-hover-bg',
                ],
            ],
        ],
        'share' => [
            'label'  => 'Share Buttons',
            'fields' => [
                'css_share_bg' => [
                    'label'    => 'Share Button Background',
                    'type'     => 'color',
                    'default'  => '#4b5563',
                    'property' => '--lxpg-share-bg',
                ],
                'css_share_hover_bg' => [
                    'label'    => 'Share Button Hover Background',
                    'type'     => 'color',
                    'default'  => '#1f2937',
                    'property' => '--lxpg-share-hover-bg',
                ],
            ],
        ],
        'cta_style' => [
            'label'  => 'CTA Band Colors',
            'fields' => [
                'css_cta_bg' => [
                    'label'    => 'CTA Background',
                    'type'     => 'color',
                    'default'  => '#1f2937',
                    'property' => '--lxpg-cta-bg',
                ],
                'css_cta_text' => [
                    'label'    => 'CTA Text Color',
                    'type'     => 'color',
                    'default'  => '#ffffff',
                    'property' => '--lxpg-cta-text',
                ],
                'css_cta_btn_bg' => [
                    'label'    => 'CTA Button Background',
                    'type'     => 'color',
                    'default'  => '#ffffff',
                    'property' => '--lxpg-cta-btn-bg',
                ],
                'css_cta_btn_text' => [
                    'label'    => 'CTA Button Text',
                    'type'     => 'color',
                    'default'  => '#1f2937',
                    'property' => '--lxpg-cta-btn-text',
                ],
            ],
        ],
        'lightbox' => [
            'label'  => 'Lightbox',
            'fields' => [
                'css_lb_bg' => [
                    'label'    => 'Backdrop Color',
                    'type'     => 'rgba',
                    'default'  => 'rgba(0,0,0,0.92)',
                    'property' => '--lxpg-lb-bg',
                    'hint'     => 'Use rgba() to control opacity. e.g. rgba(0,0,0,0.9)',
                ],
                'css_lb_text' => [
                    'label'    => 'Caption Text Color',
                    'type'     => 'color',
                    'default'  => '#ffffff',
                    'property' => '--lxpg-lb-text',
                ],
            ],
        ],
        'cta_content' => [
            'label'  => 'CTA Band Content',
            'fields' => [
                'cta_page_id' => [
                    'label'   => 'Contact Page',
                    'type'    => 'page',
                    'default' => '0',
                    'hint'    => 'Leave unset to hide the CTA band entirely.',
                ],
                'cta_heading' => [
                    'label'   => 'Heading',
                    'type'    => 'text',
                    'default' => 'Love This Project?',
                ],
                'cta_subheading' => [
                    'label'   => 'Subheading',
                    'type'    => 'text',
                    'default' => 'Get Started on Yours Today!',
                ],
                'cta_button_text' => [
                    'label'   => 'Button Text',
                    'type'    => 'text',
                    'default' => 'Contact Us',
                ],
            ],
        ],
    ];

    // -------------------------------------------------------------------------

    public function __construct() {
        add_action( 'admin_menu',             [ $this, 'add_settings_page' ] );
        add_action( 'admin_init',             [ $this, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts',  [ $this, 'enqueue_admin_assets' ] );
        add_action( 'wp_head',                [ $this, 'output_custom_css' ] );
        add_filter( 'plugin_action_links_' . plugin_basename( LXPG_FILE ), [ $this, 'add_action_link' ] );
    }

    public static function get( string $key, mixed $default = '' ): mixed {
        $options = get_option( self::OPTION, [] );
        return $options[ $key ] ?? $default;
    }

    // ── Admin assets ──────────────────────────────────────────────────────────

    public function enqueue_admin_assets( string $hook ): void {
        if ( $hook !== 'settings_page_lifex-project-gallery' ) {
            return;
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script(
            'lxpg-admin',
            LXPG_URL . 'assets/js/admin.js',
            [ 'wp-color-picker' ],
            LXPG_VERSION,
            true
        );
    }

    // ── CSS output ────────────────────────────────────────────────────────────

    public function output_custom_css(): void {
        $lines = [];

        foreach ( self::SECTIONS as $section ) {
            foreach ( $section['fields'] as $key => $field ) {
                if ( empty( $field['property'] ) ) {
                    continue;
                }

                $value = self::get( $key );

                if ( $value === '' || $value === null ) {
                    continue;
                }

                $lines[] = "\t" . esc_attr( $field['property'] ) . ': ' . esc_html( $value ) . ';';
            }
        }

        if ( empty( $lines ) ) {
            return;
        }

        echo "<style id=\"lxpg-custom-props\">\n:root {\n" . implode( "\n", $lines ) . "\n}\n</style>\n";
    }

    // ── Settings registration ─────────────────────────────────────────────────

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

        foreach ( self::SECTIONS as $section_id => $section ) {
            add_settings_section(
                'lxpg_' . $section_id,
                $section['label'],
                null,
                'lifex-project-gallery'
            );

            foreach ( $section['fields'] as $key => $field ) {
                add_settings_field(
                    'lxpg_' . $key,
                    $field['label'],
                    [ $this, 'render_field' ],
                    'lifex-project-gallery',
                    'lxpg_' . $section_id,
                    [ 'key' => $key, 'field' => $field ]
                );
            }
        }
    }

    // ── Field renderer ────────────────────────────────────────────────────────

    public function render_field( array $args ): void {
        $key   = $args['key'];
        $field = $args['field'];
        $value = self::get( $key );
        $name  = self::OPTION . '[' . $key . ']';
        $id    = 'lxpg_' . $key;
        $hint  = $field['hint'] ?? '';

        switch ( $field['type'] ) {

            case 'color':
                printf(
                    '<input type="text" name="%s" id="%s" value="%s" class="lxpg-color-field" data-default-color="%s">',
                    esc_attr( $name ),
                    esc_attr( $id ),
                    esc_attr( $value ),
                    esc_attr( $field['default'] )
                );
                break;

            case 'rgba':
                printf(
                    '<input type="text" name="%s" id="%s" value="%s" class="regular-text" placeholder="%s">',
                    esc_attr( $name ),
                    esc_attr( $id ),
                    esc_attr( $value ),
                    esc_attr( $field['default'] )
                );
                break;

            case 'select':
                echo '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '">';
                foreach ( $field['options'] as $val => $label ) {
                    printf(
                        '<option value="%s"%s>%s</option>',
                        esc_attr( $val ),
                        selected( $value, $val, false ),
                        esc_html( $label )
                    );
                }
                echo '</select>';
                break;

            case 'page':
                wp_dropdown_pages( [
                    'name'              => $name,
                    'id'                => $id,
                    'selected'          => (int) $value,
                    'show_option_none'  => __( '-- None (hide CTA) --', 'lifex-project-gallery' ),
                    'option_none_value' => '0',
                ] );
                break;

            case 'text':
            default:
                printf(
                    '<input type="text" name="%s" id="%s" value="%s" class="regular-text" placeholder="%s">',
                    esc_attr( $name ),
                    esc_attr( $id ),
                    esc_attr( $value ),
                    esc_attr( $field['default'] )
                );
                break;
        }

        if ( $hint ) {
            echo '<p class="description">' . esc_html( $hint ) . '</p>';
        }
    }

    // ── Sanitization ──────────────────────────────────────────────────────────

    public function sanitize_options( mixed $input ): array {
        if ( ! is_array( $input ) ) {
            return [];
        }

        $clean = [];

        foreach ( self::SECTIONS as $section ) {
            foreach ( $section['fields'] as $key => $field ) {
                $raw = $input[ $key ] ?? '';

                $clean[ $key ] = match ( $field['type'] ) {
                    'color'  => $this->sanitize_hex_color( $raw ),
                    'rgba'   => $this->sanitize_rgba( $raw ),
                    'select' => array_key_exists( $raw, $field['options'] ?? [] ) ? $raw : '',
                    'page'   => absint( $raw ),
                    default  => sanitize_text_field( $raw ),
                };
            }
        }

        return $clean;
    }

    // ── Sanitization helpers ──────────────────────────────────────────────────

    private function sanitize_hex_color( string $color ): string {
        if ( $color === '' ) {
            return '';
        }
        // Allow 3- or 6-digit hex.
        if ( preg_match( '/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/', $color ) ) {
            return $color;
        }
        return '';
    }

    private function sanitize_rgba( string $value ): string {
        if ( $value === '' ) {
            return '';
        }
        // Allow only rgba(...) or rgb(...).
        if ( preg_match( '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}(\s*,\s*[0-9.]+)?\s*\)$/', $value ) ) {
            return $value;
        }
        return '';
    }

    // ── Page render ───────────────────────────────────────────────────────────

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
