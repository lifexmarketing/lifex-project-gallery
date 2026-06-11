<?php
/**
 * Filter bar partial.
 *
 * Variables inherited from gallery.php:
 *   $filter_fields array
 *   $active        array
 */
defined( 'ABSPATH' ) || exit;

$reserved = [ 'location', 'sqft' ];
?>
<form method="get" action="" class="lxpg-filters" role="search" aria-label="<?php esc_attr_e( 'Filter projects', 'lifex-project-gallery' ); ?>">

    <?php
    // Preserve non-filter GET params so page state isn't lost.
    foreach ( $_GET as $key => $val ) {
        if ( ! str_starts_with( (string) $key, 'pg_' ) ) {
            printf(
                '<input type="hidden" name="%s" value="%s">',
                esc_attr( $key ),
                esc_attr( sanitize_text_field( wp_unslash( (string) $val ) ) )
            );
        }
    }
    ?>

    <?php /* Always-present category filter */ ?>
    <div class="lxpg-filter-item">
        <label for="lxpg-filter-category" class="lxpg-sr-only">
            <?php esc_html_e( 'Category', 'lifex-project-gallery' ); ?>
        </label>
        <select name="pg_category" id="lxpg-filter-category" class="lxpg-filter-select">
            <option value=""><?php esc_html_e( 'Any Category', 'lifex-project-gallery' ); ?></option>
            <?php
            $categories = get_terms( [
                'taxonomy'   => 'project_category',
                'orderby'    => 'name',
                'order'      => 'ASC',
                'hide_empty' => true,
            ] );

            if ( ! is_wp_error( $categories ) ) {
                foreach ( $categories as $cat ) {
                    printf(
                        '<option value="%s"%s>%s</option>',
                        esc_attr( $cat->term_id ),
                        selected( $active['pg_category'] ?? 0, $cat->term_id, false ),
                        esc_html( $cat->name )
                    );
                }
            }
            ?>
        </select>
    </div>

    <?php foreach ( $filter_fields as $field ) : ?>

        <?php if ( $field === 'location' ) : ?>
            <?php
            global $wpdb;
            $locations = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT DISTINCT CONCAT(c.meta_value, ', ', s.meta_value)
                     FROM {$wpdb->postmeta} c
                     JOIN {$wpdb->postmeta} s ON c.post_id = s.post_id
                     JOIN {$wpdb->posts}    p ON c.post_id = p.ID
                     WHERE c.meta_key = %s
                       AND s.meta_key = %s
                       AND p.post_type = %s
                       AND p.post_status = %s
                       AND c.meta_value != ''
                       AND s.meta_value != ''
                     ORDER BY 1 ASC",
                    'project_city',
                    'project_state',
                    'project',
                    'publish'
                )
            );
            if ( ! empty( $locations ) ) :
            ?>
            <div class="lxpg-filter-item">
                <label for="lxpg-filter-location" class="lxpg-sr-only">
                    <?php esc_html_e( 'Location', 'lifex-project-gallery' ); ?>
                </label>
                <select name="pg_location" id="lxpg-filter-location" class="lxpg-filter-select">
                    <option value=""><?php esc_html_e( 'Any Location', 'lifex-project-gallery' ); ?></option>
                    <?php foreach ( $locations as $loc ) : ?>
                        <option value="<?php echo esc_attr( $loc ); ?>"
                            <?php selected( $active['pg_location'] ?? '', $loc ); ?>>
                            <?php echo esc_html( $loc ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

        <?php elseif ( $field === 'sqft' ) : ?>
            <?php
            $sqft_ranges = [
                ''          => __( 'Any Size', 'lifex-project-gallery' ),
                '0,150'     => __( 'Up to 150 sq ft', 'lifex-project-gallery' ),
                '150,300'   => __( '150 - 300 sq ft', 'lifex-project-gallery' ),
                '300,500'   => __( '300 - 500 sq ft', 'lifex-project-gallery' ),
                '500,99999' => __( '500 sq ft+', 'lifex-project-gallery' ),
            ];
            $active_sqft = isset( $active['pg_sqft'] ) ? implode( ',', $active['pg_sqft'] ) : '';
            ?>
            <div class="lxpg-filter-item">
                <label for="lxpg-filter-sqft" class="lxpg-sr-only">
                    <?php esc_html_e( 'Square Footage', 'lifex-project-gallery' ); ?>
                </label>
                <select name="pg_sqft" id="lxpg-filter-sqft" class="lxpg-filter-select">
                    <?php foreach ( $sqft_ranges as $val => $label ) : ?>
                        <option value="<?php echo esc_attr( $val ); ?>"
                            <?php selected( $active_sqft, $val ); ?>>
                            <?php echo esc_html( $label ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

        <?php elseif ( taxonomy_exists( $field ) ) : ?>
            <?php
            $tax_obj   = get_taxonomy( $field );
            $tax_label = $tax_obj ? $tax_obj->labels->singular_name : ucwords( str_replace( [ '_', '-' ], ' ', $field ) );
            $terms     = get_terms( [ 'taxonomy' => $field, 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => true ] );

            if ( is_wp_error( $terms ) || empty( $terms ) ) {
                continue;
            }

            $param    = 'pg_' . $field;
            $input_id = 'lxpg-filter-' . $field;
            ?>
            <div class="lxpg-filter-item">
                <label for="<?php echo esc_attr( $input_id ); ?>" class="lxpg-sr-only">
                    <?php echo esc_html( $tax_label ); ?>
                </label>
                <select name="<?php echo esc_attr( $param ); ?>"
                        id="<?php echo esc_attr( $input_id ); ?>"
                        class="lxpg-filter-select">
                    <option value="">
                        <?php
                        /* translators: %s is the taxonomy label e.g. "Category" */
                        printf( esc_html__( 'Any %s', 'lifex-project-gallery' ), esc_html( $tax_label ) );
                        ?>
                    </option>
                    <?php foreach ( $terms as $term ) : ?>
                        <option value="<?php echo esc_attr( $term->term_id ); ?>"
                            <?php selected( $active[ $param ] ?? 0, $term->term_id ); ?>>
                            <?php echo esc_html( $term->name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

        <?php else : ?>
            <?php
            // ACF / native post meta field — build dropdown from distinct published values.
            global $wpdb;
            $meta_key   = sanitize_key( $field );
            $meta_values = $wpdb->get_col( $wpdb->prepare(
                "SELECT DISTINCT pm.meta_value
                 FROM {$wpdb->postmeta} pm
                 JOIN {$wpdb->posts} p ON pm.post_id = p.ID
                 WHERE pm.meta_key = %s
                   AND p.post_type = %s
                   AND p.post_status = %s
                   AND pm.meta_value != ''
                 ORDER BY pm.meta_value ASC",
                $meta_key,
                'project',
                'publish'
            ) );

            if ( empty( $meta_values ) ) {
                continue;
            }

            $param    = 'pg_' . $meta_key;
            $input_id = 'lxpg-filter-' . $meta_key;
            $label    = ucwords( str_replace( [ '_', '-' ], ' ', $field ) );
            ?>
            <div class="lxpg-filter-item">
                <label for="<?php echo esc_attr( $input_id ); ?>" class="lxpg-sr-only">
                    <?php echo esc_html( $label ); ?>
                </label>
                <select name="<?php echo esc_attr( $param ); ?>"
                        id="<?php echo esc_attr( $input_id ); ?>"
                        class="lxpg-filter-select">
                    <option value="">
                        <?php
                        /* translators: %s is the field label e.g. "Color" */
                        printf( esc_html__( 'Any %s', 'lifex-project-gallery' ), esc_html( $label ) );
                        ?>
                    </option>
                    <?php foreach ( $meta_values as $val ) : ?>
                        <option value="<?php echo esc_attr( $val ); ?>"
                            <?php selected( $active[ $param ] ?? '', $val ); ?>>
                            <?php echo esc_html( $val ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

        <?php endif; ?>
    <?php endforeach; ?>

    <div class="lxpg-filter-item">
        <button type="submit" class="lxpg-filter-btn">
            <?php esc_html_e( 'Filter', 'lifex-project-gallery' ); ?>
        </button>
    </div>

</form>
