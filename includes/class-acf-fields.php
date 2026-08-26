<?php
defined( 'ABSPATH' ) || exit;

/**
 * Registers the "Linked Testimonial" ACF field in code, so it ships with
 * the plugin instead of requiring manual setup in every site's ACF UI.
 * Uses its own field group (rather than merging into a site's manually
 * configured groups) since a group registered here becomes read-only in
 * the ACF admin UI.
 */
class LXPG_ACF_Fields {

    public function __construct() {
        // Priority 20: ACF itself registers on 'init' at priority 5, and
        // Strong Testimonials registers its post type at the default
        // priority (10) — both need to have already run before the
        // post_type_exists() check below is reliable.
        add_action( 'init', [ $this, 'register_fields' ], 20 );
    }

    public function register_fields(): void {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

        // Only offer the field when Strong Testimonials (post type
        // wpm-testimonial) is active — otherwise there's nothing to link to.
        if ( ! post_type_exists( 'wpm-testimonial' ) ) {
            return;
        }

        acf_add_local_field_group( [
            'key'      => 'group_lxpg_testimonial',
            'title'    => 'Project Gallery — Testimonial',
            'fields'   => [
                [
                    'key'           => 'field_lxpg_project_testimonial',
                    'label'         => 'Linked Testimonial',
                    'name'          => 'project-testimonial',
                    'type'          => 'post_object',
                    'instructions'  => 'Optional. Select a Strong Testimonials entry to display as a client review on this project.',
                    'required'      => 0,
                    'post_type'     => [ 'wpm-testimonial' ],
                    'post_status'   => [ 'publish' ],
                    'return_format' => 'id',
                    'multiple'      => 0,
                    'allow_null'    => 1,
                    'ui'            => 1,
                ],
            ],
            'location' => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'project',
                    ],
                ],
            ],
            'menu_order'            => 0,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'active'                => true,
        ] );
    }
}
