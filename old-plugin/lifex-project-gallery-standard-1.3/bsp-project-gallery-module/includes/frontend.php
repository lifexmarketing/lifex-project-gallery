<?php 

$button_text = htmlspecialchars($settings->my_button_text);

$lightbox_icon = htmlspecialchars($settings->my_lightbox_icon);

//var_dump($settings->my_button_typography); 
?>
 
[project_gallery buttontext="<?php echo $button_text; ?>" lightboxicon="<?php echo $lightbox_icon; ?>"]