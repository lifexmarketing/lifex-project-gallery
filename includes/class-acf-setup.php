<?php
defined( 'ABSPATH' ) || exit;

/**
 * One-click setup for the ACF fields a typical project gallery site needs.
 * Persists a real, ACF-admin-editable field group (unlike a local field
 * group registered via acf_add_local_field_group()) so site admins can
 * adjust it afterward like any field group they built by hand.
 *
 * The standard set is adapted from a reference site's "Project Information"
 * group (see acf-export-2026-08-26.json): Project ID, City, State, ZIP,
 * and Additional Images, in that order, plus the plugin's own Linked
 * Testimonial field appended last.
 */
class LXPG_ACF_Setup {

    private const STANDARD_GROUP_KEY = 'group_6491db25653cb';

    public function __construct() {
        add_action( 'admin_post_lxpg_generate_fields', [ $this, 'handle_generate' ] );
        add_action( 'admin_post_lxpg_import_fields',   [ $this, 'handle_import' ] );
    }

    // ── Standard field definitions ───────────────────────────────────────────

    private function get_base_fields(): array {
        return [
            [
                'key'               => 'field_lxpg_project_id',
                'label'             => 'Project ID',
                'name'              => 'project_id',
                'type'              => 'text',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => [ 'width' => '', 'class' => '', 'id' => '' ],
                'default_value'     => '',
                'maxlength'         => '',
                'placeholder'       => '',
                'prepend'           => '',
                'append'            => '',
            ],
            [
                'key'               => 'field_649c5546cf1cf',
                'label'             => 'City',
                'name'              => 'project_city',
                'type'              => 'text',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => [ 'width' => '', 'class' => '', 'id' => '' ],
                'default_value'     => '',
                'maxlength'         => '',
                'placeholder'       => '',
                'prepend'           => '',
                'append'            => '',
            ],
            [
                'key'               => 'field_6491db7c90d69',
                'label'             => 'State',
                'name'              => 'project_state',
                'type'              => 'text',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => [ 'width' => '', 'class' => '', 'id' => '' ],
                'default_value'     => '',
                'maxlength'         => '',
                'placeholder'       => '',
                'prepend'           => '',
                'append'            => '',
            ],
            [
                'key'               => 'field_lxpg_project_zip',
                'label'             => 'ZIP',
                'name'              => 'project_zip',
                'type'              => 'text',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => [ 'width' => '', 'class' => '', 'id' => '' ],
                'default_value'     => '',
                'maxlength'         => '',
                'placeholder'       => '',
                'prepend'           => '',
                'append'            => '',
            ],
            [
                'key'               => 'field_6491dc28b8d0b',
                'label'             => 'Additional Images',
                'name'              => 'additional_images',
                'type'              => 'gallery',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => [ 'width' => '', 'class' => '', 'id' => '' ],
                'return_format'     => 'array',
                'library'           => 'all',
                'min'               => '',
                'max'               => '',
                'min_width'         => '',
                'min_height'        => '',
                'min_size'          => '',
                'max_width'         => '',
                'max_height'        => '',
                'max_size'          => '',
                'mime_types'        => '',
                'insert'            => 'append',
                'preview_size'      => 'medium',
            ],
        ];
    }

    private function get_testimonial_field(): array {
        return [
            'key'           => 'field_lxpg_project_testimonial',
            'label'         => 'Linked Testimonial',
            'name'          => 'project_testimonial',
            'type'          => 'post_object',
            'instructions'  => 'Optional. Select a Strong Testimonials entry to display as a client review on this project.',
            'required'      => 0,
            'post_type'     => [ 'wpm-testimonial' ],
            'post_status'   => [ 'publish' ],
            'return_format' => 'id',
            'multiple'      => 0,
            'allow_null'    => 1,
            'ui'            => 1,
        ];
    }

    /**
     * @return string[] Field names this site is expected to have. Includes
     *                   the testimonial field only when Strong Testimonials
     *                   is active, since it has nothing to link to otherwise.
     */
    public function get_standard_field_names(): array {
        $names = array_column( $this->get_base_fields(), 'name' );
        if ( post_type_exists( 'wpm-testimonial' ) ) {
            $names[] = 'project_testimonial';
        }
        return $names;
    }

    private function get_standard_field_group(): array {
        $fields = $this->get_base_fields();
        if ( post_type_exists( 'wpm-testimonial' ) ) {
            $fields[] = $this->get_testimonial_field();
        }

        return [
            'key'                   => self::STANDARD_GROUP_KEY,
            'title'                 => 'Project Information',
            'fields'                => $fields,
            'location'              => [
                [
                    [ 'param' => 'post_type', 'operator' => '==', 'value' => 'project' ],
                ],
            ],
            'menu_order'            => 0,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen'        => '',
            'active'                => true,
            'description'           => '',
        ];
    }

    // ── Status check ──────────────────────────────────────────────────────────

    private function field_exists_on_project( string $name ): bool {
        foreach ( acf_get_field_groups( [ 'post_type' => 'project' ] ) as $group ) {
            foreach ( acf_get_fields( $group ) as $field ) {
                if ( $field['name'] === $name ) {
                    return true;
                }
            }
        }
        return false;
    }

    /** @return array<string,bool> Field name => whether it already exists. */
    public function get_status(): array {
        $status = [];
        foreach ( $this->get_standard_field_names() as $name ) {
            $status[ $name ] = $this->field_exists_on_project( $name );
        }
        return $status;
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function handle_generate(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to do this.', 'lifex-project-gallery' ) );
        }
        check_admin_referer( 'lxpg_generate_fields' );

        $redirect = admin_url( 'edit.php?post_type=project&page=lifex-project-gallery' );

        if ( ! function_exists( 'acf_import_field_group' ) ) {
            wp_safe_redirect( add_query_arg( 'lxpg_acf_notice', 'no_acf', $redirect ) );
            exit;
        }

        acf_import_field_group( $this->get_standard_field_group() );

        wp_safe_redirect( add_query_arg( 'lxpg_acf_notice', 'generated', $redirect ) );
        exit;
    }

    public function handle_import(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to do this.', 'lifex-project-gallery' ) );
        }
        check_admin_referer( 'lxpg_import_fields' );

        $redirect = admin_url( 'edit.php?post_type=project&page=lifex-project-gallery' );

        if ( ! function_exists( 'acf_import_field_group' ) ) {
            wp_safe_redirect( add_query_arg( 'lxpg_acf_notice', 'no_acf', $redirect ) );
            exit;
        }

        $file = $_FILES['lxpg_acf_json'] ?? null;
        if ( empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) || $file['size'] > 2 * MB_IN_BYTES ) {
            wp_safe_redirect( add_query_arg( 'lxpg_acf_notice', 'import_error', $redirect ) );
            exit;
        }

        $data = json_decode( (string) file_get_contents( $file['tmp_name'] ), true );
        if ( isset( $data['key'] ) ) {
            $data = [ $data ]; // A single exported group, not an array of groups.
        }

        $imported = 0;
        if ( is_array( $data ) ) {
            foreach ( $data as $group ) {
                if ( ! is_array( $group ) || empty( $group['key'] ) || empty( $group['fields'] ) || empty( $group['location'] ) ) {
                    continue;
                }
                acf_import_field_group( $group );
                $imported++;
            }
        }

        if ( $imported === 0 ) {
            wp_safe_redirect( add_query_arg( 'lxpg_acf_notice', 'import_error', $redirect ) );
            exit;
        }

        wp_safe_redirect( add_query_arg( [ 'lxpg_acf_notice' => 'imported', 'lxpg_acf_count' => $imported ], $redirect ) );
        exit;
    }

    // ── Admin UI ─────────────────────────────────────────────────────────────

    public function render_panel(): void {
        echo '<h2>' . esc_html__( 'ACF Field Setup', 'lifex-project-gallery' ) . '</h2>';

        if ( ! function_exists( 'acf_get_field_groups' ) ) {
            echo '<div class="notice notice-warning inline"><p>'
                . esc_html__( 'Advanced Custom Fields is not active. Install and activate ACF to use these tools.', 'lifex-project-gallery' )
                . '</p></div>';
            return;
        }

        $this->render_notice();

        $status  = $this->get_status();
        $missing = array_keys( array_filter( $status, static fn( $exists ) => ! $exists ) );

        echo '<table class="widefat striped" style="max-width:520px;margin-bottom:16px;"><thead><tr><th>'
            . esc_html__( 'Field', 'lifex-project-gallery' ) . '</th><th>'
            . esc_html__( 'Status', 'lifex-project-gallery' ) . '</th></tr></thead><tbody>';

        foreach ( $status as $name => $exists ) {
            printf(
                '<tr><td><code>%s</code></td><td>%s</td></tr>',
                esc_html( $name ),
                $exists
                    ? '&#10003; ' . esc_html__( 'Exists', 'lifex-project-gallery' )
                    : '&mdash; ' . esc_html__( 'Missing', 'lifex-project-gallery' )
            );
        }
        echo '</tbody></table>';

        if ( empty( $missing ) ) {
            echo '<p>' . esc_html__( 'All standard fields are already set up on this site.', 'lifex-project-gallery' ) . '</p>';
        } elseif ( count( $missing ) === count( $status ) ) {
            ?>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'lxpg_generate_fields' ); ?>
                <input type="hidden" name="action" value="lxpg_generate_fields">
                <?php submit_button( __( 'Generate Standard Fields', 'lifex-project-gallery' ), 'primary', 'submit', false ); ?>
            </form>
            <?php
        } else {
            echo '<p>' . esc_html__( 'Some standard fields already exist on this site. To avoid creating duplicate field definitions, add the missing ones manually in ACF, or use Import Custom JSON below with a matching export.', 'lifex-project-gallery' ) . '</p>';
        }
        ?>
        <h3><?php esc_html_e( 'Import Custom Fields', 'lifex-project-gallery' ); ?></h3>
        <p><?php esc_html_e( 'Upload an ACF JSON export (Custom Fields → Tools → Export) to set up a different field structure for this site instead.', 'lifex-project-gallery' ); ?></p>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
            <?php wp_nonce_field( 'lxpg_import_fields' ); ?>
            <input type="hidden" name="action" value="lxpg_import_fields">
            <input type="file" name="lxpg_acf_json" accept="application/json,.json" required>
            <?php submit_button( __( 'Import ACF JSON', 'lifex-project-gallery' ), 'secondary', 'submit', false ); ?>
        </form>
        <?php
    }

    private function render_notice(): void {
        if ( ! isset( $_GET['lxpg_acf_notice'] ) ) {
            return;
        }

        $notice   = sanitize_key( wp_unslash( $_GET['lxpg_acf_notice'] ) );
        $count    = isset( $_GET['lxpg_acf_count'] ) ? (int) $_GET['lxpg_acf_count'] : 0;
        $messages = [
            'generated'    => [ 'success', __( 'Standard fields generated.', 'lifex-project-gallery' ) ],
            /* translators: %d is the number of field groups imported */
            'imported'     => [ 'success', sprintf( __( 'Imported %d field group(s).', 'lifex-project-gallery' ), $count ) ],
            'import_error' => [ 'error',   __( 'That file could not be imported. Make sure it is a valid ACF JSON export.', 'lifex-project-gallery' ) ],
            'no_acf'       => [ 'error',   __( 'Advanced Custom Fields is not active.', 'lifex-project-gallery' ) ],
        ];

        if ( isset( $messages[ $notice ] ) ) {
            [ $type, $text ] = $messages[ $notice ];
            printf( '<div class="notice notice-%s inline"><p>%s</p></div>', esc_attr( $type ), esc_html( $text ) );
        }
    }
}
