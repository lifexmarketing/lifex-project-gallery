<?php  

class ProjectGalleryModule extends FLBuilderModule {
    public function __construct()
    {
        parent::__construct(array(
            'name'            => __( 'Project Gallery Module', 'fl-builder' ),
            'description'     => __( 'Custom BB Project Gallery Module', 'fl-builder' ),
            'group'           => __( 'Project Gallery Plugin', 'fl-builder' ),
            'category'        => __( 'Project Gallery Plugin base', 'fl-builder' ),
            'dir'             => MY_MODULES_DIR . 'bsp-project-gallery-module/',
            'url'             => MY_MODULES_URL . 'bsp-project-gallery-module/',
            'icon'            => 'button.svg',
            'editor_export'   => true, // Defaults to true and can be omitted.
            'enabled'         => true, // Defaults to true and can be omitted.
            'partial_refresh' => false, // Defaults to false and can be omitted.
        ));
    }
    public function example_method(){
        return "Hello World!";
    }
}

FLBuilder::register_module( 'ProjectGalleryModule', array(
    'my-tab-1'      => array(
        'title'         => __( 'Basic', 'fl-builder' ),
        'sections'      => array(
            'my-section-1'  => array(
                'title'         => __( 'Button', 'fl-builder' ),
                'fields'        => array(
                    'my_button_text' => array(
                        'type'          => 'text',
                        'label'         => __( 'Button Text', 'fl-builder' ),
                        'default'       => 'Filter',
                        'maxlength'     => '50',
                        'size'          => '50',
                        'placeholder'   => __( 'Filter', 'fl-builder' ),
                        'class'         => 'my-button-typography',
                        'description'   => __( 'Enter text to display for button', 'fl-builder' ),
                        //'help'          => __( 'Text displayed in the help tooltip', 'fl-builder' )
                    ),
                    'my_button_typography' => array(
                        'type'       => 'typography',
                        'label'      => 'Button Text Typography',
                        'responsive' => true,
                        'preview'    => array(
                        'type'      => 'css',
                        'selector'  => 'my-button-typography',
                        ),
                    ),
                    'my_button_text_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Button Text Color', 'fl-builder' ),
                        'default'       => 'e7ebef',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    ),
                    'my_button_text_hover_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Button Text Hover Color', 'fl-builder' ),
                        'default'       => 'e7ebef',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    ),
                    'my_button_border' => array(
                        'type'       => 'border',
                        'label'      => 'Button Border',
                        'responsive' => true,
                        'preview'    => array(
                            'type'     => 'css',
                            'selector' => 'my-button-typography',
                        ),
                    ),
                    'my_button_padding' => array(
                        'type'        => 'dimension',
                        'label'       => 'Button Padding',
                        'description' => 'px',
                    ),
                    'my_button_margin' => array(
                        'type'        => 'dimension',
                        'label'       => 'Button Margins',
                        'description' => 'px',
                    ),
                    'my_button_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Button Color', 'fl-builder' ),
                        'default'       => 'e7ebef',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    ),
                    'my_button_hover_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Button Hover Color', 'fl-builder' ),
                        'default'       => 'e7ebef',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    )                    
                )
            ),
            'my-section-2'  => array(
                'title'         => __( 'Columns', 'fl-builder' ),
                'fields'        => array(
                    'my_num_columns' => array(
                        'type'          => 'select',
                        'label'         => __( 'Number of columns', 'fl-builder' ),
                        'default'       => '4',
                        'options'       => array(
                            '1'      => __( '1', 'fl-builder' ),
                            '2'      => __( '2', 'fl-builder' ),
                            '3'      => __( '3', 'fl-builder' ),
                            '4'      => __( '4', 'fl-builder' )
                        )
                    ),
                    'my_columns_padding' => array(
                        'type'        => 'dimension',
                        'label'       => 'Column Padding',
                        'description' => 'px',
                    ),
                    'my_columns_margin' => array(
                        'type'        => 'dimension',
                        'label'       => 'Column Margins',
                        'description' => 'px',
                    ),
                    'my_columns_border' => array(
                        'type'       => 'border',
                        'label'      => 'Column Border',
                        'responsive' => true,
                        'preview'    => array(
                            'type'     => 'css',
                            'selector' => 'project__gallery .grid-default',
                        ),
                    ),
                    'my_columns_caption_typography' => array(
                        'type'       => 'typography',
                        'label'      => 'Caption Typography',
                        'responsive' => true,
                        'preview'    => array(
                        'type'      => 'css',
                        'selector'  => 'project__gallery .grid-default a',
                        ),
                    ),
                    'my_columns_caption_background_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Caption Background Color', 'fl-builder' ),
                        'default'       => '2e3234',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    ),
                     'my_columns_caption_text_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Caption Text Color', 'fl-builder' ),
                        'default'       => 'ffffff',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    ),
                     'my_columns_caption_text_hover_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Caption Text Hover Color', 'fl-builder' ),
                        'default'       => 'ff0000',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    ),
                    'my_columns_overlay_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Column Overlay Color', 'fl-builder' ),
                        'default'       => 'ffffff',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    ),
                    'my_columns_hover_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Column Hover Color', 'fl-builder' ),
                        'default'       => 'cccccc',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    ) 
                )
            ),
            'my-section-3'  => array(
                'title'         => __( 'Drop Downs', 'fl-builder' ),
                'fields'        => array(
                    'my_dd_padding' => array(
                        'type'        => 'dimension',
                        'label'       => 'Padding',
                        'description' => 'px',
                    ),
                    'my_dd_margin' => array(
                        'type'        => 'dimension',
                        'label'       => 'Margins',
                        'description' => 'px',
                    ),
                    'my_dd_background_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Background Color', 'fl-builder' ),
                        'default'       => 'ffffff',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    ),
                    'my_dd_border' => array(
                        'type'       => 'border',
                        'label'      => 'Border',
                        'responsive' => true,
                        'preview'    => array(
                            'type'     => 'css',
                            'selector' => 'my-button-typography',
                        ),
                    ),
                    'my_dd_typography' => array(
                        'type'       => 'typography',
                        'label'      => 'Typography',
                        'responsive' => true,
                        'preview'    => array(
                        'type'      => 'css',
                        'selector'  => '.my-dd-typography',
                        )
                    )
                )
            )            ,
            'my-section-4'  => array(
                'title'         => __( 'Lightbox', 'fl-builder' ),
                'fields'        => array(
                    'my_lightbox_show_hide' => array(
                        'type'    => 'button-group',
                        'label'   => 'Show Lightbox',
                        'default' => '0',
                        'options' => array(
                            '1'    => 'Yes',
                            '0'    => 'No',
                        ),
                    ),
                    'my_lightbox_icon' => array(
                        'type'     => 'icon',
                        'label'    => 'Lightbox Icon',
                    ),
                   'my_lightbox_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Lightbox Icon Color', 'fl-builder' ),
                        'default'       => 'ff0000',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    )
                )
            )
        )
    )
) );

/*
Image stylizing:
padding/margins, border/radius, typography (for caption), background (for caption), overlay color, hover color



Button Stylizing (filter/search):
padding/margins, border/radius, text, typography, background/overlay color, hover color,



Drop-down selectors:
padding/margins, border/radius, typography, background color

*/

 ?>