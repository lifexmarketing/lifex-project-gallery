<?php
	/* Render the CSS for each individual instance of a module */
?>


<?php
	FLBuilderCSS::typography_field_rule( array(
		'settings'	=> $settings,
		'setting_name' 	=> 'my_button_typography',
		'selector' 	=> ".fl-node-$id .my-button-typography",
	));

	FLBuilderCSS::typography_field_rule( array(
		'settings'	=> $settings,
		'setting_name' 	=> 'my_dd_typography',
		'selector' 	=> ".fl-node-$id .my-dd-typography",
	) );

	FLBuilderCSS::border_field_rule( array(
		'settings' 	=> $settings,
		'setting_name' 	=> 'my_button_border',
		'selector' 	=> ".fl-node-$id .my-button-typography",
	) );

	FLBuilderCSS::border_field_rule( array(
		'settings' 	=> $settings,
		'setting_name' 	=> 'my_dd_border',
		'selector' 	=> ".fl-node-$id .my-dd-typography",
	) );

	FLBuilderCSS::border_field_rule( array(
		'settings' 	=> $settings,
		'setting_name' 	=> 'my_columns_border',
		'selector' 	=> ".fl-node-$id .project__gallery .grid-default",
	) );

	FLBuilderCSS::typography_field_rule( array(
		'settings'	=> $settings,
		'setting_name' 	=> 'my_columns_caption_typography',
		'selector' 	=> ".fl-node-$id .project__gallery .grid-default a",
	) );
?>


<?php 	/* Grid ****************************************************************************************************************************/ ?>

.fl-node-<?php echo $id; ?> .grid-default {
   
   <?php switch ($settings->my_num_columns) {
	    case "1":
	        echo "width: 100%;";
	        break;
	    case "2":
	        echo "width: 50%;";
	        break;
	    case "3":
	        echo "width: 33%;";
	        break;
	    case "4":
	        echo "width: 25%;";
	        break;
	    default:
	         echo "width: 25%;";
	} 
			
	?>

	padding-top: <?php echo $settings->my_columns_padding_top;?>px; 
	padding-right: <?php echo $settings->my_columns_padding_right;?>px;
	padding-bottom: <?php echo $settings->my_columns_padding_bottom;?>px; 
	padding-left: <?php echo $settings->my_columns_padding_left; ?>px;
	margin-top: <?php echo $settings->my_columns_margin_top;?>px; 
	margin-right: <?php echo $settings->my_columns_margin_right;?>px;
	margin-bottom: <?php echo $settings->my_columns_margin_bottom;?>px; 
	margin-left: <?php echo $settings->my_columns_margin_left; ?>px;
}

.fl-node-<?php echo $id; ?> .grid-default div.overlay {

	position:relative;
}

.fl-node-<?php echo $id; ?> .grid-default div.overlay a.openlb {

<?php

		$lightbox_show_hide = $settings->my_lightbox_show_hide;

		if ($lightbox_show_hide) { 
	?>
		 	
			display: inline-block; 
			right: 15px;
		    z-index: 999;
		    position: absolute;
		    top: 10px;
	<?php
		}
		else {
	?>
			display: none; 
	<?php
		}
	?>


<?php

		$my_lightbox_color = $settings->my_lightbox_color;

		if ($my_lightbox_color[0] !== 'r') {
		 	
		 	$my_lightbox_color = '#' . $my_lightbox_color;
		}
	?>
  
	color: <?php echo $my_lightbox_color;?>; 

	

}

.fl-node-<?php echo $id; ?> .grid-default span.projectid a {

	<?php

		$columns_caption_background_color = $settings->my_columns_caption_background_color;

		if ($columns_caption_background_color[0] !== 'r') {
		 	
		 	$columns_caption_background_color = '#' . $columns_caption_background_color;
		}
	?>
  
	background-color: <?php echo $columns_caption_background_color;?>; 
}

.fl-node-<?php echo $id; ?> .grid-default a.mainlink:before {

	<?php

		$columns_overlay_color = $settings->my_columns_overlay_color;

		if ($columns_overlay_color[0] !== 'r') {
		 	
		 	$columns_overlay_color = '#' . $columns_overlay_color;
		}
	?>
  
	background-color: <?php echo $columns_overlay_color;?>; 
}

.fl-node-<?php echo $id; ?> .grid-default a.mainlink:hover:before, .fl-node-<?php echo $id; ?> .grid-default a.mainlink:focus:before {

	<?php

		$columns_hover_color = $settings->my_columns_hover_color;

		if ($columns_hover_color[0] !== 'r') {
		 	
		 	$dcolumns_hover_color = '#' . $columns_hover_color;
		}
	?>
  
	background-color: <?php echo $columns_hover_color;?>; 
}
                                            

.fl-node-<?php echo $id; ?> .grid-default span.projectid a {

	<?php

		$columns_caption_text_color = $settings->my_columns_caption_text_color;

		if ($columns_caption_text_color[0] !== 'r') {
		 	
		 	$columns_caption_text_color = '#' . $columns_caption_text_color;
		}
	?>
  
	color: <?php echo $columns_caption_text_color;?>; 
}


.fl-node-<?php echo $id; ?> .grid-default span.projectid a:hover {

	<?php

		$columns_caption_text_hover_color = $settings->my_columns_caption_text_hover_color;

		if ($columns_caption_text_hover_color[0] !== 'r') {
		 	
		 	$columns_caption_text_hover_color = '#' . $columns_caption_text_hover_color;
		}
	?>
  
	color: <?php echo $columns_caption_text_hover_color;?>; 
}


.fl-node-<?php echo $id; ?> .project__list .grid-default img  {

		margin-bottom:0; 


		 <?php switch ($settings->my_num_columns) {
	    case "1":
	        echo "max-height: none;";
	        break;
	    case "2":
	        echo "max-height: none;";
	        break;
	    case "3":
	        echo "max-height: none;";
	        break;
	    case "4":
	        echo "max-height: none;";
	        break;
	} 
?>

}



<?php 	/* Drop-down selectors ****************************************************************************************************************************/ ?>


.fl-node-<?php echo $id;?> .my-dd-typography {

	<?php

		$dd_color = $settings->my_dd_background_color;

		if ($dd_color[0] !== 'r') {
		 	
		 	$dd_color = '#' . $dd_color;
		}
	?>

	padding-top: <?php echo $settings->my_dd_padding_top;?>px; 
	padding-right: <?php echo $settings->my_dd_padding_right;?>px; 
	padding-bottom: <?php echo $settings->my_dd_padding_bottom;?>px; 
	padding-left: <?php echo $settings->my_dd_padding_left; ?>px;
	margin-top: <?php echo $settings->my_dd_margin_top;?>px; 
	margin-right: <?php echo $settings->my_dd_margin_right;?>px ;
	margin-bottom: <?php echo $settings->my_dd_margin_bottom;?>px; 
	margin-left: <?php echo $settings->my_dd_margin_left; ?>px;
	background-color: <?php echo $dd_color; ?>;
}


<?php 	/* Button ****************************************************************************************************************************/ ?>

.fl-node-<?php echo $id; ?> .my-button-typography {
	<?php

		$button_color = $settings->my_button_color;
		$button_text_color = $settings->my_button_text_color;

		if ($button_color[0] !== 'r')
		 {
		 	$button_color = '#' . $button_color;
		 }

		if ($button_text_color[0] !== 'r')
		 {
		 	$button_text_color = '#' . $button_text_color;
		 }
	?>

    color: <?php echo $button_text_color; ?>;
    background-color: <?php echo $button_color; ?>;
    padding-top: <?php echo $settings->my_button_padding_top;?>px; 
    padding-right: <?php echo $settings->my_button_padding_right;?>px; 
    padding-bottom: <?php echo $settings->my_button_padding_bottom;?>px; 
    padding-left:  <?php echo $settings->my_button_padding_left; ?>px;
    margin-top: <?php echo $settings->my_button_margin_top;?>px; 
    margin-right: <?php echo $settings->my_button_margin_right;?>px; 
    margin-bottom: <?php echo $settings->my_button_margin_bottom;?>px; 
    margin-left: <?php echo $settings->my_button_margin_left; ?>px;
}


.fl-node-<?php echo $id; ?> .my-button-typography:hover {
	<?php

	$button_hover_color = $settings->my_button_hover_color;
	$button_text_hover_color = $settings->my_button_text_hover_color;

	if ($button_hover_color[0] !== 'r')
		{
			$button_hover_color = '#' . $button_hover_color;
		}

	if ($button_text_hover_color[0] !== 'r')
		{
			$button_text_hover_color = '#' . $button_text_hover_color;
		}

	 ?>
    color: <?php echo $button_text_hover_color; ?>; 
    background-color: <?php echo $button_hover_color; ?>;
}



<?php 	/* Lightbox ****************************************************************************************************************************/ ?>

.mfp-iframe-holder .mfp-close, .mfp-image-holder .mfp-close {
    color: #fff;
    right: -6px;
    text-align: right;
    padding-right: 12px!important;
    width: 100%;
    font-size: 80px;
}

.admin-bar .mfp-wrap .mfp-close, .admin-bar .mfp-wrap .mfp-close:active, .admin-bar .mfp-wrap .mfp-close:hover, .admin-bar .mfp-wrap .mfp-close:focus {
    top: 12px!important;
}

.mfp-zoom-out-cur, .mfp-zoom-out-cur .mfp-image-holder .mfp-close {
    cursor: pointer!important;
}


<?php 	/* Responsive ****************************************************************************************************************************/ ?>

@media (max-width: 767px) {
	.fl-node-<?php echo $id; ?> .project__gallery .project__list *[class^="grid"] { width: 100%; float: none; box-sizing: border-box; padding: 0; margin-bottom:20px; }
}
