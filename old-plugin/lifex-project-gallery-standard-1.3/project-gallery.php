<?php
/*
Plugin Name: LifeX Project Gallery
Version: 1.3
Author: LifeX Marketing
*/

add_action('init', 'project_gallery_css');

function project_gallery_css() {
	wp_register_style('project_gallery_css', plugins_url('/css/project-gallery.css', __FILE__), false, '1.0.1', 'all');
	wp_register_style('project-gallery-fancybox-css', plugins_url('/css/jquery.fancybox.min.css', __FILE__), false, '3.5.7', 'all');
}

add_action('init', 'project_gallery_js');

function project_gallery_js() {
	wp_register_script('project-gallery-js', plugins_url('/js/keystone-custom.js', __FILE__), array('jquery-magnificpopup'), '1.0.2', true );
	wp_register_script('project-gallery-fancybox-js', plugins_url('/js/jquery.fancybox.min.js', __FILE__), array('jquery'), '3.5.7', true );

}

add_action('init', 'enqueue_project_gallery');

function enqueue_project_gallery() {
	wp_enqueue_style('project_gallery_css');
	wp_enqueue_style('project-gallery-fancybox-css');
	wp_enqueue_script('project-gallery-fancybox-js');
	wp_enqueue_script('project-gallery-js');
}

add_action('init', 'project_gallery_register');

function project_gallery_register() {
	$labels = array(
		'name' => _x('Project Gallery', 'post type plural name'),
		'singular_name' => _x('Project', 'post type singular name'),
		'add_new' => __('Add Project'),
		'add_new_item' => __('Add New Project'),
		'edit_item' => __('Edit Project'),
		'new_item' => __('New Project'),
		'view_item' => __('View Project'),
		'search_items' => __('Search Project Gallery'),
		'not_found' =>  __('You have not yet added any projects.'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'_builtin' => false,
		'query_var' => true,
		'rewrite' => array('slug' => 'project', 'with_front' => false),
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-format-gallery',
		//'taxonomies' => array('category'),
		'supports' => array('title', 'editor', 'thumbnail','page-attributes')
	  );

	register_post_type( 'project' , $args );
}

add_action( 'init', 'create_project_category', 0 );

function create_project_category() {
	register_taxonomy(
		'project_category',
		'project',
		array(
			'labels' => array(
				'name' => 'Categories',
				'add_new_item' => 'Add New Project Category',
				'new_item_name' => "New Project Category"
			),
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'show_tagcloud' => false,
			'hierarchical' => true,
			'rewrite' => array('slug' => 'project-gallery/category', 'with_front' => false)
		)
	);
}

add_filter("manage_edit-project_gallery_columns", "project_gallery_edit_columns");

function project_gallery_edit_columns($columns){
  $columns = array(
  	"cb" => "<input type=\"checkbox\" />",
		"title" => "Title"  );
  return $columns;
}

add_action("admin_init", "project_admin_init");

function project_admin_init(){
	add_meta_box("project_meta", "Project Information", "project_meta", "project", "normal", "low");
}

function project_meta() {
	global $post;
	$projectID = get_post_meta($post->ID, 'project_id', true );
    $project_sqft = get_post_meta($post->ID, 'project_sqft', true );
    $project_color = get_post_meta($post->ID, 'project_color', true );
	$project_price_range = get_post_meta($post->ID, 'project_price_range', true );
    $project_manufacturer = get_post_meta($post->ID, 'project_manufacturer', true );
    $project_city = get_post_meta($post->ID, 'project_city', true );
    $project_state = get_post_meta($post->ID, 'project_state', true );
    $project_zip = get_post_meta($post->ID, 'project_zip', true );
?>
	<fieldset>
  
  	<legend><big><strong>Details</strong></big></legend>
    
    <hr/>

    <p><label for="project_id"><strong>Project ID:</strong></label><br />
    <input type="text" name="project_id" id="project_id" value="<?php echo $projectID; ?>" style="width: 100%;"></p>

    <p><label for="sqft"><strong>Sq. Ft.:</strong></label><br />
    <input type="text" name="project_sqft" id="project_sqft" value="<?php echo $project_sqft; ?>" style="width: 100%;" /></p>
    
    <p><label for="color"><strong>Color:</strong></label><br />
    <input type="text" name="project_color" id="project_color" value="<?php echo $project_color; ?>" style="width: 100%;" /></p>
  
    <p><label for="price-range"><strong>Price Range:</strong></label><br />
    <input type="text" name="project_price_range" id="project_price_range" value="<?php echo $project_price_range; ?>" style="width: 100%;" /></p>
    
    <p><label for="manufacturer"><strong>Manufacturer:</strong></label><br />
    <input type="text" name="project_manufacturer" id="project_manufacturer" value="<?php echo $project_manufacturer; ?>" style="width: 100%;" /></p>
    
  </fieldset>
  
  <fieldset>
  
  	<legend><big><strong>Location</strong></big></legend>
    
    <hr/>
	
    <p><label for="city"><strong>City:</strong></label><br />
    <input type="text" name="project_city" id="project_city" value="<?php echo $project_city; ?>" style="width: 100%;" /></p>
    
    <p><label for="state"><strong>State:</strong></label><br />
    <input type="text" name="project_state" id="project_state" value="<?php echo $project_state; ?>" style="width: 100%;" /></p>
  
    <p><label for="zip"><strong>ZIP:</strong></label><br />
    <input type="text" name="project_zip" id="project_zip" value="<?php echo $project_zip; ?>" style="width: 100%;" /></p>
    
  </fieldset>
  
  <?php
}

add_action('save_post', 'project_save_details');

function project_save_details($post_id) {
  global $post;
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return;
	}

	if(isset($post->post_type) && $post->post_type == "project") {
		if( isset($_POST) ) {
			update_post_meta($post_id, 'project_id', $_POST['project_id']);
			update_post_meta($post_id, 'project_sqft', $_POST['project_sqft']);
			update_post_meta($post_id, 'project_color', $_POST['project_color']);
			update_post_meta($post_id, 'project_price_range', $_POST['project_price_range']);
			update_post_meta($post_id, 'project_manufacturer', $_POST['project_manufacturer']);
			update_post_meta($post_id, 'project_city', $_POST['project_city']);
			update_post_meta($post_id, 'project_state', $_POST['project_state']);
			update_post_meta($post_id, 'project_zip', $_POST['project_zip']);
		}
	}
}

add_filter('single_template', 'project_gallery_single_template');

function project_gallery_single_template($single) {
    global $post;

    if ( $post->post_type == 'project' ) {
        if ( file_exists( __DIR__ . '/project-gallery-single.php' ) ) {
            return __DIR__ . '/project-gallery-single.php';
        }
    }

    return $single;
}

define( 'MY_MODULES_DIR', plugin_dir_path( __FILE__ ) );
define( 'MY_MODULES_URL', plugins_url( '/', __FILE__ ) );

add_action( 'init', 'load_custom_bb_modules' );

function load_custom_bb_modules() {
    if ( class_exists( 'FLBuilder' ) ) {

        require_once 'bsp-project-gallery-module/bsp-project-gallery-module.php';
    }
}

require_once __DIR__ .'/old-functions.php';
require_once __DIR__ .'/class-featured-post.php';
require_once __DIR__ .'/class-featured-post-widget.php';
require_once __DIR__ .'/shortcode.php';
