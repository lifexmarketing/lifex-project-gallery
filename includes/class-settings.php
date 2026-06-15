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
        'grid' => [
            'label'  => 'Gallery Grid',
            'fields' => [
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
                'css_card_caption_bg' => [
                    'label'    => 'Caption Background',
                    'type'     => 'color',
                    'default'  => '#1f2937',
                    'property' => '--lxpg-card-caption-bg',
                ],
                'css_caption_hover_bg' => [
                    'label'    => 'Caption Hover Background',
                    'type'     => 'color',
                    'default'  => '#374151',
                    'property' => '--lxpg-caption-hover-bg',
                ],
                'css_card_text' => [
                    'label'    => 'Caption Text Color',
                    'type'     => 'color',
                    'default'  => '#ffffff',
                    'property' => '--lxpg-card-text',
                ],
                'css_caption_hover_text' => [
                    'label'    => 'Caption Hover Text Color',
                    'type'     => 'color',
                    'default'  => '#ffffff',
                    'property' => '--lxpg-caption-hover-text',
                ],
                'css_caption_underline' => [
                    'label'    => 'Caption Hover Underline Color',
                    'type'     => 'text',
                    'default'  => '',
                    'property' => '--lxpg-caption-underline',
                    'hint'     => 'Enter a CSS color (e.g. #ffffff) to show an underline on hover. Leave empty for none.',
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
        'single_content' => [
            'label'  => 'Single Project Content',
            'fields' => [
                'subtitle_template' => [
                    'label'   => 'Subtitle Template',
                    'type'    => 'text',
                    'default' => '',
                    'hint'    => 'Build the subtitle using [bracket] tokens. e.g. [category] Project in [project_city]. Tokens resolve in order: [category] = primary category, any taxonomy slug, then any ACF field name. Unresolved tokens are removed; if none resolve the subtitle is hidden.',
                ],
                'project_detail_fields' => [
                    'label'   => 'ACF Detail Fields',
                    'type'    => 'text',
                    'default' => '',
                    'hint'    => 'Comma-separated ACF field names to display after project content, in order. e.g. project_sqft, project_manufacturer. Labels are pulled from the ACF field definition.',
                ],
            ],
        ],
        'content_cta' => [
            'label'  => 'Inline CTA — Single Project Pages',
            'fields' => [
                'content_cta_enabled' => [
                    'label'   => 'Enable Inline CTA',
                    'type'    => 'checkbox',
                    'default' => '',
                ],
                'content_cta_text' => [
                    'label'   => 'Button Text',
                    'type'    => 'text',
                    'default' => 'Visit This Website',
                ],
                'content_cta_acf_field' => [
                    'label'   => 'ACF Field for URL',
                    'type'    => 'text',
                    'default' => '',
                    'hint'    => 'ACF field name on the project post that holds the destination URL (e.g. project_website).',
                ],
                'content_cta_bg' => [
                    'label'    => 'Button Background',
                    'type'     => 'color',
                    'default'  => '#1f2937',
                    'property' => '--lxpg-content-cta-bg',
                ],
                'content_cta_color' => [
                    'label'    => 'Button Text Color',
                    'type'     => 'color',
                    'default'  => '#ffffff',
                    'property' => '--lxpg-content-cta-color',
                ],
                'content_cta_hover_bg' => [
                    'label'    => 'Hover Background',
                    'type'     => 'color',
                    'default'  => '#374151',
                    'property' => '--lxpg-content-cta-hover-bg',
                ],
                'content_cta_hover_color' => [
                    'label'    => 'Hover Text Color',
                    'type'     => 'color',
                    'default'  => '#ffffff',
                    'property' => '--lxpg-content-cta-hover-color',
                ],
            ],
        ],
        'cta_content' => [
            'label'  => 'CTA Band Content — Single Project Pages',
            'fields' => [
                'cta_page_id' => [
                    'label'   => 'Contact Page',
                    'type'    => 'page',
                    'default' => '0',
                    'hint'    => 'The page the CTA button links to. Leave unset to hide the CTA band entirely.',
                ],
                'cta_heading' => [
                    'label'   => 'Heading',
                    'type'    => 'text',
                    'default' => 'Love This Project?',
                ],
                'cta_button_text' => [
                    'label'   => 'Button Text',
                    'type'    => 'text',
                    'default' => 'Contact Us',
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
                'css_cta_btn_hover_bg' => [
                    'label'    => 'CTA Button Hover Background',
                    'type'     => 'color',
                    'default'  => '#e5e7eb',
                    'property' => '--lxpg-cta-btn-hover-bg',
                ],
                'css_cta_btn_hover_text' => [
                    'label'    => 'CTA Button Hover Text',
                    'type'     => 'color',
                    'default'  => '#1f2937',
                    'property' => '--lxpg-cta-btn-hover-text',
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
        wp_enqueue_style(
            'lxpg-preview',
            LXPG_URL . 'assets/css/lifex-project-gallery.css',
            [],
            LXPG_VERSION
        );
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

        $prop_attr = ! empty( $field['property'] ) ? ' data-lxpg-prop="' . esc_attr( $field['property'] ) . '"' : '';

        switch ( $field['type'] ) {

            case 'checkbox':
                printf(
                    '<input type="checkbox" name="%1$s" id="%2$s" value="1"%3$s>',
                    esc_attr( $name ),
                    esc_attr( $id ),
                    checked( $value, '1', false )
                );
                break;

            case 'color':
                printf(
                    '<input type="text" name="%1$s" id="%2$s" value="%3$s" class="lxpg-color-field" data-default-color="%4$s"%5$s>',
                    esc_attr( $name ),
                    esc_attr( $id ),
                    esc_attr( $value ),
                    esc_attr( $field['default'] ),
                    $prop_attr
                );
                break;

            case 'rgba':
                printf(
                    '<input type="text" name="%1$s" id="%2$s" value="%3$s" class="regular-text" placeholder="%4$s"%5$s>',
                    esc_attr( $name ),
                    esc_attr( $id ),
                    esc_attr( $value ),
                    esc_attr( $field['default'] ),
                    $prop_attr
                );
                break;

            case 'select':
                echo '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '"' . $prop_attr . '>';
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
                    '<input type="text" name="%1$s" id="%2$s" value="%3$s" class="regular-text" placeholder="%4$s"%5$s>',
                    esc_attr( $name ),
                    esc_attr( $id ),
                    esc_attr( $value ),
                    esc_attr( $field['default'] ),
                    $prop_attr
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
                    'color'    => $this->sanitize_hex_color( $raw ),
                    'rgba'     => $this->sanitize_rgba( $raw ),
                    'select'   => array_key_exists( $raw, $field['options'] ?? [] ) ? $raw : '',
                    'page'     => absint( $raw ),
                    'checkbox' => ( $raw === '1' ) ? '1' : '',
                    default    => sanitize_text_field( $raw ),
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

        // Collect every saved CSS var override so both preview containers start
        // in sync with the currently saved state before any JS runs.
        $pv = '';
        foreach ( self::SECTIONS as $section ) {
            foreach ( $section['fields'] as $key => $field ) {
                if ( empty( $field['property'] ) ) {
                    continue;
                }
                $value = self::get( $key );
                if ( $value === '' ) {
                    continue;
                }
                $pv .= esc_attr( $field['property'] ) . ':' . esc_attr( $value ) . ';';
            }
        }

        // Sections that get a side preview and which renderer to use.
        $previews = [ 'grid' => 'card', 'filters' => 'filter', 'content_cta' => 'content_cta_btn', 'cta_style' => 'cta' ];
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Project Gallery Settings', 'lifex-project-gallery' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'lxpg_settings_group' ); ?>

                <?php foreach ( self::SECTIONS as $section_id => $section ) :
                    $preview_type = $previews[ $section_id ] ?? null;
                ?>
                <h2><?php echo esc_html( $section['label'] ); ?></h2>

                <?php if ( $preview_type ) : ?>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:start;">
                    <div style="min-width:0;">
                <?php endif; ?>

                        <table class="form-table" role="presentation"><tbody>
                            <?php do_settings_fields( 'lifex-project-gallery', 'lxpg_' . $section_id ); ?>
                        </tbody></table>

                <?php if ( $preview_type ) : ?>
                    </div>
                    <div>
                        <?php
                        if ( $preview_type === 'card' ) {
                            $this->render_card_preview( $pv );
                        } elseif ( $preview_type === 'filter' ) {
                            $this->render_filter_preview( $pv );
                        } elseif ( $preview_type === 'content_cta_btn' ) {
                            $this->render_content_cta_preview( $pv );
                        } elseif ( $preview_type === 'cta' ) {
                            $this->render_cta_preview( $pv );
                        }
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php endforeach; ?>

                <?php submit_button(); ?>
            </form>
        </div>
        <style>
            /* WP admin overrides <select> styles; force the plugin vars to win in the preview. */
            [data-lxpg-preview] .lxpg-filter-select {
                appearance: none !important;
                -webkit-appearance: none !important;
                background-color: var(--lxpg-filter-bg) !important;
                border: 1px solid var(--lxpg-filter-border) !important;
                color: var(--lxpg-filter-text) !important;
            }
        </style>
        <?php
    }

    private function render_card_preview( string $pv ): void {
        ?>
        <p style="font-size:12px;font-weight:600;margin:0 0 10px;text-transform:uppercase;letter-spacing:.06em;color:#50575e;">
            <?php esc_html_e( 'Card Preview', 'lifex-project-gallery' ); ?>
        </p>
        <div style="background:#f6f7f7;border:1px solid #c3c4c7;border-radius:3px;padding:16px;max-width: 200px;">
            <div data-lxpg-preview class="lxpg-gallery" style="<?php echo $pv; ?>">
                <article class="lxpg-card">
                    <a href="#" class="lxpg-card-link" onclick="return false;">
                        <div class="lxpg-card-image-wrap"></div>
                        <div class="lxpg-card-caption">
                            <p class="lxpg-card-label"><?php esc_html_e( 'Sample Project', 'lifex-project-gallery' ); ?></p>
                        </div>
                    </a>
                </article>
            </div>
        </div>
        <?php
    }

    private function render_filter_preview( string $pv ): void {
        ?>
        <p style="font-size:12px;font-weight:600;margin:0 0 10px;text-transform:uppercase;letter-spacing:.06em;color:#50575e;">
            <?php esc_html_e( 'Filter Preview', 'lifex-project-gallery' ); ?>
        </p>
        <div style="background:#f6f7f7;border:1px solid #c3c4c7;border-radius:3px;padding:16px;max-width: 400px;">
            <div data-lxpg-preview class="lxpg-gallery" style="<?php echo $pv; ?>">
                <div class="lxpg-filters-preview" style="flex-direction:column;">
                    <div class="lxpg-filter-item" style="min-width:0;">
                        <select class="lxpg-filter-select">
                            <option><?php esc_html_e( 'Any Category', 'lifex-project-gallery' ); ?></option>
                            <option><?php esc_html_e( 'Commercial', 'lifex-project-gallery' ); ?></option>
                            <option><?php esc_html_e( 'Residential', 'lifex-project-gallery' ); ?></option>
                        </select>
                    </div>
                    <div class="lxpg-filter-item" style="min-width:0;">
                        <button type="button" class="lxpg-filter-btn"><?php esc_html_e( 'Filter', 'lifex-project-gallery' ); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    private function render_content_cta_preview( string $pv ): void {
        $enabled = self::get( 'content_cta_enabled', '' ) === '1';
        ?>
        <div id="lxpg-content-cta-preview" style="display:<?php echo $enabled ? 'block' : 'none'; ?>;max-width: 400px;">
            <p style="font-size:12px;font-weight:600;margin:0 0 10px;text-transform:uppercase;letter-spacing:.06em;color:#50575e;">
                <?php esc_html_e( 'Inline CTA Preview', 'lifex-project-gallery' ); ?>
            </p>
            <div style="background:#f6f7f7;border:1px solid #c3c4c7;border-radius:3px;padding:16px;">
                <div data-lxpg-preview style="<?php echo $pv; ?>;max-width:380px;">
                    <div class="lxpg-content-cta">
                        <a href="#" class="lxpg-content-cta-link" onclick="return false;">
                            <?php echo esc_html( self::get( 'content_cta_text', 'Visit This Website' ) ?: 'Visit This Website' ); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    private function render_cta_preview( string $pv ): void {
        ?>
        <p style="font-size:12px;font-weight:600;margin:0 0 10px;text-transform:uppercase;letter-spacing:.06em;color:#50575e;">
            <?php esc_html_e( 'CTA Preview', 'lifex-project-gallery' ); ?>
        </p>
        <div style="background:#f6f7f7;border:1px solid #c3c4c7;border-radius:3px;padding:16px;max-width: 400px;">
            <div data-lxpg-preview style="<?php echo $pv; ?>;max-width:380px;">
                <div class="lxpg-cta">
                    <div class="lxpg-cta-inner">
                        <div class="lxpg-cta-text">
                            <span class="lxpg-cta-heading"><?php echo esc_html( self::get( 'cta_heading', 'Love This Project?' ) ?: 'Love This Project?' ); ?></span>
                        </div>
                        <a href="#" class="lxpg-cta-link" onclick="return false;">
                            <?php echo esc_html( self::get( 'cta_button_text', 'Contact Us' ) ?: 'Contact Us' ); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function add_action_link( array $links ): array {
        $url = admin_url( 'options-general.php?page=lifex-project-gallery' );
        array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'lifex-project-gallery' ) . '</a>' );
        return $links;
    }
}
