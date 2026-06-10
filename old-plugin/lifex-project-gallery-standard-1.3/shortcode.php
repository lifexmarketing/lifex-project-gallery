<?php

add_shortcode('project_gallery', function($atts, $content = "") {


    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);

    // override default attributes with user attributes
    $project_gallery_atts = shortcode_atts([
        'buttontext' => 'Filter',
        'lightboxicon' => 'lb test',
    ], $atts);

    if (isset($_REQUEST['location'])) {
        $selected_location = $_REQUEST['location'];
        $selected_location_exploded = explode(', ', $selected_location);
        $selected_city = isset($selected_location_exploded[0]) ? $selected_location_exploded[0] : '';
        $selected_state = isset($selected_location_exploded[1]) ? $selected_location_exploded[1] : '';
    }

    if (isset($_REQUEST['sqft'])) {
        $selected_sqft = $_REQUEST['sqft'] ? $_REQUEST['sqft'] : '';
    }

    ob_start();

    ?>
    <script type="text/javascript">
        jQuery(document).ready(function( $ ) {
            $( ".group.project__filters" ).on( "submit", function( event ) {
                var oldAction = window.location.href.replace(window.location.search,'');
                console.log('old action inline', oldAction);
                var params = $( this ).serialize();
                console.log('params inline', params);
                $(this).attr('action', oldAction + '?' + params);
            });
        });
    </script>

    <div class="project__gallery">
    <form action="" method="post" class="group project__filters">

        <div class="grid-25">
            <select name="category" id="category" class="my-dd-typography">
                <?php $categories = get_terms('project_category', array('orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false)); ?>
                <option value="">Any Category</option>
                <?php foreach ($categories as $category): ?>
					<option value="<?php echo $category->term_id; ?>" <?php if(isset($_REQUEST['category']) && $_REQUEST['category'] == $category->term_id): ?>selected="selected"<?php endif; ?>><?php echo $category->name; ?></option>
				<?php endforeach; ?>
            </select>
        </div>

        <div class="grid-25">
            <?php

            // for testing values
            //echo '<h1>' . $project_gallery_atts['lightboxicon'] . '</h1>';

            ?>

            <select name="location" id="location" class='my-dd-typography'>
                <?php $args = array('post_type' => 'project', 'posts_per_page' => -1); ?>
                <?php query_posts($args); ?>
                <?php if (have_posts()) : ?>
                    <?php $locations = array(); ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php $locations[] = get_post_meta(get_the_ID(), 'project_city', true).', '.get_post_meta(get_the_ID(), 'project_state', true); ?>
                    <?php endwhile; ?>
                <?php endif; wp_reset_query(); ?>
                <?php sort($locations); ?>
                <?php $locations = array_unique($locations); ?>
                <option value="">Any Location</option>
				<?php foreach ($locations as $location): ?>
					<option value="<?php echo $location; ?>" <?php if(isset($selected_location) && $location == $selected_location): ?>selected="selected"<?php endif; ?>><?php echo $location; ?></option>
				<?php endforeach; ?>
            </select>
        </div>

        <div class="grid-25">
            <select name="sqft" id="sqft" class='my-dd-typography'>
                <option value="">Any Sq Ft</option>
                <option value="0, 150" <?php if(isset($selected_sqft) && $selected_sqft == '0, 150'): ?>selected="selected"<?php endif; ?>>Up to 150 sq ft</option>
                <option value="150, 300" <?php if(isset($selected_sqft) && $selected_sqft == '150, 300'): ?>selected="selected"<?php endif; ?>>150 - 300 sq ft</option>
                <option value="300, 500" <?php if(isset($selected_sqft) && $selected_sqft == '300, 500'): ?>selected="selected"<?php endif; ?>>300 - 500 sq ft</option>
                <option value="500, 1000000" <?php if(isset($selected_sqft) && $selected_sqft == '500, 1000000'): ?>selected="selected"<?php endif; ?>>500 sq ft+</option>
            </select>
        </div>

        <div class="grid-25 filter-buttons"><input class="filter-button my-button-typography" type="submit" value="<?php echo esc_html__($project_gallery_atts['buttontext'], 'project_gallery') ?>"></div>

    </form>

    <?php

    $args = array(
        'post_type' => 'project',
        //'orderby' => 'publish_date',
        'orderby' => array(
                'menu_order' => 'DSC'), 
        'posts_per_page' => -1,
        'tax_query' => array(),
        'meta_query' => array(),
    );

    if (isset($_REQUEST['category']) && !empty($_REQUEST['category'])) {
        array_push($args['tax_query'], array(
            'taxonomy' => 'project_category',
            'terms' => $_REQUEST['category'],
            'field' => 'term_id',
        ));
    }

    if (isset($_REQUEST['location']) && !empty($_REQUEST['location'])) {
        array_push($args['meta_query'], array(
            'key' => 'project_city',
            'value' => $selected_city,
            'compare' => 'LIKE'
        ), array(
            'key' => 'project_state',
            'value' => $selected_state,
            'compare' => 'LIKE'
        ));
    }

    if (isset($_REQUEST['sqft']) && !empty($_REQUEST['sqft'])) {
        array_push($args['meta_query'], array(
            'key' => 'project_sqft',
            'value' => $selected_sqft,
            'type' => 'numeric',
            'compare' => 'BETWEEN'
        ));
    }

    $project_gallery = new WP_Query($args);
    //echo 'Last SQL-Query: '.$project_gallery->request;

    if ($project_gallery->have_posts()) { ?>
        <div class="group project__list">
            <?php

            $count = $project_gallery->found_posts;
            $columns = 1;
            ?>

            <?php while ($project_gallery->have_posts()) : $project_gallery->the_post(); ?>

                <?php $featured = get_field('featured_project'); ?>

                <?php if ($featured) : ?>


                    <div class="grid-25 grid-default <?php echo 'pg-col-' . $columns ?>">
                        <div class="overlay">
                            <?php
                            $images = get_field('additional_images');
                            $img_count = 0;

                            $featured = get_field('featured_project');
                            $featured_color = get_field('featured_color');
                            ?>

                            <?php if ($images) : ?>
                                <div class="bsp-gallery">
                                    <?php foreach( $images as $image ): ?>
                                        <?php if( $img_count == 0 ): ?>
                                            <a class="openlb" href="<?php echo $image['url']; ?>"><span class="lb-icon <?php echo $project_gallery_atts['lightboxicon']; ?>"></span></a>
                                        <?php else: ?>
                                            <a href="<?php echo $image['url']; ?>"></a>
                                        <?php endif;

                                        $img_count++; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (has_post_thumbnail()): ?>
                                <a class="mainlink" href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('project-gallery', ['title' => get_the_title(), 'alt' => get_the_title()]); ?>
                                </a>
                            <?php elseif( get_field('additional_images') ): ?>
                                <a class="mainlink" href="<?php the_permalink(); ?>"><img src="<?php echo get_field('additional_images')[0]['sizes']['cta']; ?>" alt="<?php the_title(); ?>" /> </a>
                            <?php else: ?>
								<a class="mainlink" href="<?php the_permalink(); ?>">
									<img src="<?php echo plugin_dir_url(__FILE__) . 'images/no-image.png'; ?>" alt="No Image" />
								</a>
                            <?php endif; ?>

                            <span class="projectid"><a <?php if ($featured) {echo 'style="background: ' . $featured_color . '" ';} ?>href="<?php the_permalink(); ?>">Project ID: #<?php echo get_post_meta(get_the_ID(), 'project_id', true) ?></a> </span>
                        </div>
                    </div>
                    <?php
                    $columns++;
                    if ($columns == 5) {
                        $columns = 1;
                    }
                    $count--;
                    ?>
                <?php endif; ?>
            <?php endwhile; wp_reset_postdata(); ?>



            <?php while ($project_gallery->have_posts()) : $project_gallery->the_post(); ?>

                <?php $featured = get_field('featured_project'); ?>


                <?php if (!$featured) : ?>


                    <div class="grid-25 grid-default <?php echo 'pg-col-' . $columns ?>">
                        <div class="overlay">
                            <?php
                            $images = get_field('additional_images');
                            $img_count = 0;

                            $featured = get_field('featured_project');
                            $featured_color = get_field('featured_color');
                            ?>

                            <?php if ($images) : ?>
                                <div class="bsp-gallery">
                                    <?php foreach( $images as $image ): ?>
                                        <?php if( $img_count == 0 ): ?>
                                            <a class="openlb" href="<?php echo $image['url']; ?>"><span class="lb-icon <?php echo $project_gallery_atts['lightboxicon']; ?>"></span></a>
                                        <?php else: ?>
                                            <a href="<?php echo $image['url']; ?>"></a>
                                        <?php endif;

                                        $img_count++; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (has_post_thumbnail()): ?>
                                <a class="mainlink" href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('project-gallery', ['title' => get_the_title(), 'alt' => get_the_title()]); ?>
                                </a>
                            <?php elseif( get_field('additional_images') ): ?>
                                <a class="mainlink proj-gal-addtl" href="<?php the_permalink(); ?>"><img src="<?php echo get_field('additional_images')[0]['sizes']['cta']; ?>" alt="<?php the_title(); ?>" /> </a>
                            <?php else: ?>
								<a class="mainlink" href="<?php the_permalink(); ?>">
									<img src="<?php echo plugin_dir_url(__FILE__) . 'images/no-image.png'; ?>" alt="No Image" />
								</a>
                            <?php endif; ?>

                            <span class="projectid"><a <?php if ($featured) {echo 'style="background: ' . $featured_color . '" ';} ?>href="<?php the_permalink(); ?>">Project ID: #<?php echo get_post_meta(get_the_ID(), 'project_id', true) ?></a> </span>
                        </div>
                    </div>
                    <?php
                    $columns++;
                    if ($columns == 5) {
                        $columns = 1;
                    }
                    $count--;
                    ?>

                <?php endif; ?>
            <?php endwhile; wp_reset_postdata(); ?>






        </div>
        </div>
    <?php } else { ?>
        <p>No projects found matching the filters selected.</p>
    <?php }
    return ob_get_clean();

});
