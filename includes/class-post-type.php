<?php
defined( 'ABSPATH' ) || exit;

class LXPG_Post_Type {

    public function __construct() {
        add_action( 'init',               [ $this, 'register' ] );
        add_action( 'after_setup_theme',  [ $this, 'register_image_sizes' ] );
    }

    public function register(): void {
        register_post_type( 'project', [
            'labels' => [
                'name'               => _x( 'Project Gallery',       'post type plural name', 'lifex-project-gallery' ),
                'singular_name'      => _x( 'Project',               'post type singular name', 'lifex-project-gallery' ),
                'add_new'            => __( 'Add Project',            'lifex-project-gallery' ),
                'add_new_item'       => __( 'Add New Project',        'lifex-project-gallery' ),
                'edit_item'          => __( 'Edit Project',           'lifex-project-gallery' ),
                'new_item'           => __( 'New Project',            'lifex-project-gallery' ),
                'view_item'          => __( 'View Project',           'lifex-project-gallery' ),
                'search_items'       => __( 'Search Projects',        'lifex-project-gallery' ),
                'not_found'          => __( 'No projects found.',     'lifex-project-gallery' ),
                'not_found_in_trash' => __( 'Nothing in Trash.',      'lifex-project-gallery' ),
            ],
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'project', 'with_front' => false ],
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-format-gallery',
            'supports'           => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
        ] );

        register_taxonomy( 'project_category', 'project', [
            'labels' => [
                'name'          => __( 'Project Categories',        'lifex-project-gallery' ),
                'singular_name' => __( 'Project Category',          'lifex-project-gallery' ),
                'add_new_item'  => __( 'Add New Project Category',  'lifex-project-gallery' ),
                'new_item_name' => __( 'New Project Category',      'lifex-project-gallery' ),
            ],
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_tagcloud'     => false,
            'hierarchical'      => true,
            'rewrite'           => [ 'slug' => 'project-gallery/category', 'with_front' => false ],
        ] );
    }

    public function register_image_sizes(): void {
        add_image_size( 'project-gallery',       735, 489, true );
        add_image_size( 'project-gallery-thumb', 300, 200, true );
    }
}
