<?php
defined( 'ABSPATH' ) || exit;

class LXPG_Shortcode {

    /** Reserved filter keywords that are not taxonomy slugs. */
    private const RESERVED = [ 'location', 'sqft' ];

    public function __construct() {
        add_shortcode( 'project-gallery', [ $this, 'render' ] );
    }

    public function render( array|string $atts ): string {
        // Late-enqueue fallback for page builders (no-op if already enqueued).
        LXPG_Assets::enqueue_gallery();

        $atts = shortcode_atts( [
            'filters'       => 'off',
            'filter_fields' => '',
            'count'         => -1,
            'category'      => '',
        ], array_change_key_case( (array) $atts, CASE_LOWER ), 'project-gallery' );

        $show_filters    = strtolower( $atts['filters'] ) === 'on';
        $count           = $atts['count'] == -1 ? -1 : absint( $atts['count'] );
        $preset_category = sanitize_title( $atts['category'] );

        // Parse filter_fields into taxonomy slugs and reserved keywords.
        $filter_fields = [];
        if ( $show_filters && $atts['filter_fields'] !== '' ) {
            $filter_fields = array_filter(
                array_map( 'trim', explode( ',', $atts['filter_fields'] ) )
            );
        }

        // Collect sanitized active filter values from GET.
        $active = $this->collect_active_filters( $filter_fields );

        // Build and run the query.
        $query = $this->build_query( $count, $preset_category, $active );

        if ( ! $query->have_posts() ) {
            wp_reset_postdata();
            ob_start();
            include LXPG_DIR . 'templates/partials/no-results.php';
            return ob_get_clean();
        }

        // Sort: featured projects first, preserve relative menu_order within each group.
        $posts = $this->sort_featured( $query->posts );
        wp_reset_postdata();

        ob_start();
        include LXPG_DIR . 'templates/gallery.php';
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------

    private function collect_active_filters( array $filter_fields ): array {
        $active = [];

        // Category is always present when filters are on.
        if ( isset( $_GET['pg_category'] ) ) {
            $active['pg_category'] = absint( $_GET['pg_category'] );
        }

        foreach ( $filter_fields as $field ) {
            if ( $field === 'location' ) {
                if ( isset( $_GET['pg_location'] ) && $_GET['pg_location'] !== '' ) {
                    $active['pg_location'] = sanitize_text_field( wp_unslash( $_GET['pg_location'] ) );
                }
            } elseif ( $field === 'sqft' ) {
                if ( isset( $_GET['pg_sqft'] ) && $_GET['pg_sqft'] !== '' ) {
                    // Expect "min,max" — only allow two comma-separated integers.
                    $raw   = sanitize_text_field( wp_unslash( $_GET['pg_sqft'] ) );
                    $parts = array_map( 'absint', explode( ',', $raw ) );
                    if ( count( $parts ) === 2 ) {
                        $active['pg_sqft'] = $parts;
                    }
                }
            } else {
                $param = 'pg_' . sanitize_key( $field );
                if ( isset( $_GET[ $param ] ) && $_GET[ $param ] !== '' ) {
                    if ( taxonomy_exists( $field ) ) {
                        $active[ $param ] = absint( $_GET[ $param ] );
                    } else {
                        // ACF / native post meta — store as sanitized string.
                        $active[ $param ] = sanitize_text_field( wp_unslash( $_GET[ $param ] ) );
                    }
                }
            }
        }

        return $active;
    }

    private function build_query( int $count, string $preset_category, array $active ): WP_Query {
        $args = [
            'post_type'      => 'project',
            'posts_per_page' => $count,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'post_status'    => 'publish',
            'tax_query'      => [],
            'meta_query'     => [],
        ];

        // Pre-filter by category attribute on the shortcode itself.
        if ( $preset_category !== '' ) {
            $args['tax_query'][] = [
                'taxonomy' => 'project_category',
                'field'    => 'slug',
                'terms'    => $preset_category,
            ];
        }

        // Active category filter from UI.
        if ( ! empty( $active['pg_category'] ) ) {
            $args['tax_query'][] = [
                'taxonomy' => 'project_category',
                'field'    => 'term_id',
                'terms'    => $active['pg_category'],
            ];
        }

        // Active location filter.
        if ( ! empty( $active['pg_location'] ) ) {
            $parts = explode( ', ', $active['pg_location'], 2 );
            $args['meta_query']['relation'] = 'AND';
            $args['meta_query'][] = [
                'key'     => 'project_city',
                'value'   => sanitize_text_field( $parts[0] ),
                'compare' => '=',
            ];
            if ( isset( $parts[1] ) ) {
                $args['meta_query'][] = [
                    'key'     => 'project_state',
                    'value'   => sanitize_text_field( $parts[1] ),
                    'compare' => '=',
                ];
            }
        }

        // Active sqft filter.
        if ( ! empty( $active['pg_sqft'] ) ) {
            $args['meta_query'][] = [
                'key'     => 'project_sqft',
                'value'   => $active['pg_sqft'],
                'type'    => 'NUMERIC',
                'compare' => 'BETWEEN',
            ];
        }

        // Active custom taxonomy and ACF/meta filters.
        foreach ( $active as $param => $filter_value ) {
            if ( ! str_starts_with( $param, 'pg_' ) ) {
                continue;
            }
            $slug = substr( $param, 3 ); // strip 'pg_'
            if ( in_array( $slug, [ 'category', 'location', 'sqft' ], true ) ) {
                continue; // already handled above
            }
            if ( taxonomy_exists( $slug ) ) {
                $args['tax_query'][] = [
                    'taxonomy' => $slug,
                    'field'    => 'term_id',
                    'terms'    => $filter_value,
                ];
            } else {
                // ACF / native post meta field.
                $args['meta_query'][] = [
                    'key'     => $slug,
                    'value'   => $filter_value,
                    'compare' => '=',
                ];
            }
        }

        return new WP_Query( $args );
    }

    /**
     * Returns the post list with ACF-featured projects first,
     * preserving relative ordering within each group.
     */
    private function sort_featured( array $posts ): array {
        if ( ! function_exists( 'get_field' ) ) {
            return $posts;
        }

        $featured = [];
        $regular  = [];

        foreach ( $posts as $post ) {
            if ( get_field( 'featured_project', $post->ID ) ) {
                $featured[] = $post;
            } else {
                $regular[] = $post;
            }
        }

        return array_merge( $featured, $regular );
    }
}
