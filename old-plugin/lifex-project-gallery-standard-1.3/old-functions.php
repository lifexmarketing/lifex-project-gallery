<?php

add_action( 'after_setup_theme', 'project_gallery_size_setup' );

function project_gallery_size_setup() {
    add_image_size('project-gallery', 735, 489, true);
}

/*
MODIFIED GALLERY SHORTCODE STRUCTURE
*/
add_filter('post_gallery', 'ct_post_gallery', 10, 2);
function ct_post_gallery($output, $attr) {
    global $post;
 
    if (isset($attr['orderby'])) {
        $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
        if (!$attr['orderby'])
            unset($attr['orderby']);
    }
 
    extract(shortcode_atts(array(
        'order' => 'ASC',
        'orderby' => 'menu_order ID',
        'id' => $post->ID,
        'itemtag' => 'dl',
        'icontag' => 'dt',
        'captiontag' => 'dd',
        'columns' => 4,
        'size' => 'thumbnail',
        'include' => '',
        'exclude' => ''
    ), $attr));
 
    $id = intval($id);
    if ('RAND' == $order) $orderby = 'none';
 
    if (!empty($include)) {
        $include = preg_replace('/[^0-9,]+/', '', $include);
        $_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
 
        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    }
 
    if (empty($attachments)) return '';
 
    // Here's your actual output, you may customize it to your need
    $output = "<div class=\"gallery masonry__gallery\">\n";
 
    // Now you loop through each attachment
    foreach ($attachments as $id => $attachment) {
        $img = wp_get_attachment_image_src($id, 'medium');
				$imgsrc = wp_get_attachment_image_url($id, 'large');
				$imgalt = get_post_field('post_title', $attachment->ID);
				$imgcap = get_post_field('post_excerpt', $attachment->ID);
		
        $output .= "<dl class=\"gallery-item\">\n";
			 	$output .= "<dt class=\"gallery-icon landscape\">\n";
				if($imgcap):
					$output .= "<a href=\"$imgsrc\" data-fancybox=\"gallery\" data-caption=\"$imgcap\"><img src=\"{$img[0]}\" width=\"{$img[1]}\" height=\"{$img[2]}\" alt=\"$imgalt\" /></a>\n";
				else:
					$output .= "<a href=\"$imgsrc\" data-fancybox=\"gallery\"><img src=\"{$img[0]}\" width=\"{$img[1]}\" height=\"{$img[2]}\" alt=\"$imgalt\" /></a>\n";
				endif;
				$output .= "</dt>\n";
        $output .= "</dl>\n";
    }
 
    $output .= "</div>\n";
 
    return $output;
}

/*MODIFY PROJECT CATEGORY ARGS*/
function custom_ppp( $query ) {
	if ( $query->is_tax('project_category') && $query->is_main_query()) {
		$query->set( 'posts_per_page', '-1' );
		$orderby[] = array('menu_order' => 'ASC', 'ID' => 'ASC');
		$query->set('orderby', array( $orderby ) );		
		if(isset($_POST['location'])):
			$selected_location = $_POST['location'];
			$selected_location_exploded = explode(', ', $selected_location);
			$selected_city = $selected_location_exploded[0];
			$selected_state = $selected_location_exploded[1];
		endif;
		if(isset($_POST['sqft'])):
			$selected_sqft = $_POST['sqft'];
		endif;
		$meta_query[] = array(
				array(
					'key' => 'project_city',
					'value' => (!$selected_city ? '' : $selected_city),
					'compare' => 'LIKE'
				),
				array(
					'key' => 'project_state',
					'value' => (!$selected_state ? '' : $selected_state),
					'compare' => 'LIKE'
				),
				array(
					'key' => 'project_sqft',
					'value' => (!$selected_sqft ? '' : $selected_sqft),
					'type' => 'numeric',
					'compare' => 'BETWEEN'
				)
			);
		$query->set('meta_query', array( $meta_query ) );
	}
}

add_action( 'pre_get_posts', 'custom_ppp', 9999 );
