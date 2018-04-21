<?php

/** @var MPSLSlideOptions $this */
$sliderType = $this->getSliderType();

$slideSettings = array(
    'main' => array(
        'title' => __('General', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'col' => '12',
        'hidden' => false,
        'options' => array(
            'title' => array(
                'type' => 'text',
                'label' => __('Slide Title', 'motopress-slider'),
                'description' => __('The title of the slide that will be shown in the slides list.', 'motopress-slider'),
                'default' => 'Slide'
            ),
            'status' => array(
                'type' => 'button_group',
                'label' => __('Status', 'motopress-slider'),
                'description' => '',
                'default' => 'published',
                'button_size' => 'large',
                'list' => array(
                    'published' => __('Published', 'motopress-slider'),
                    'draft' => __('Draft', 'motopress-slider')
                )
            ),
            /*
            'bg_type' => array(
                'type' => 'radio_group',
                'label' => __('Background type:', 'motopress-slider'),
                'default' => 'color',
                'list' => array(
                    'color' => __('Color', 'motopress-slider'),
                    'image' => __('Image', 'motopress-slider'),
//                    'parallax' => __('Parallax', 'motopress-slider'),
//                    'video' => __('Video', 'motopress-slider'),
//                    'youtube' => __('YouTube', 'motopress-slider'),
//                    'gradient' => __('Gradient', 'motopress-slider')
                )
            ),
            'bg_color' => array(
                'type' => 'text',
                'label' => __('Background Color:', 'motopress-slider'),
                'default' => '#ffffff',
                'dependency' => array(
                    'parameter' => 'bg_type',
                    'value' => 'color'
                )
            ),
            */
//            'bg_types' => array(
//                'type' => 'checkbox',
//                'label' => __('Background type:', 'motopress-slider'),
//                'default' => array('color'),
//                'list' => array(
//                    'color' => __('Color', 'motopress-slider'),
//                    'image' => __('Image', 'motopress-slider'),
////                    'parallax' => __('Parallax', 'motopress-slider'),
//                    'video' => __('Video', 'motopress-slider'),
////                    'youtube' => __('YouTube', 'motopress-slider'),
////                    'gradient' => __('Gradient', 'motopress-slider')
//                )
//            ),
        )
    ),
    'color' => array(
        'title' => __('Color', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'col' => '12',
        'options' => array(
            'fonts' => array(
                'type' => 'multiple',
                'default' => array(),
                'hidden' => true
            ),
            'bg_color_type' => array(
                'type' => 'radio_group',
                'label' => __('Background Color Type:', 'motopress-slider'),
                'default' => 'color',
                'list' => array(
                    'color' => __('Color', 'motopress-slider'),
                    'gradient' => __('Gradient', 'motopress-slider')
                ),
//                'dependency' => array(
//                    'parameter' => 'bg_types',
//                    'value' => 'color'
//                )
            ),
            'bg_color' => array(
                'type' => 'color_picker',
                'label' => __('Background Color:', 'motopress-slider'),
                'default' => '#ffffff',
                'dependency' => array(
                    'parameter' => 'bg_color_type',
                    'value' => 'color'
                )
            ),
            'bg_grad_color_2' => array(
                'type' => 'color_picker',
                'label' => __('Gradient color 1:', 'motopress-slider'),
                'default' => 'black',
                'dependency' => array(
                    'parameter' => 'bg_color_type',
                    'value' => 'gradient'
                )
            ),
            'bg_grad_color_1' => array(
                'type' => 'color_picker',
                'label' => __('Gradient color 2:', 'motopress-slider'),
                'default' => 'white',
                'dependency' => array(
                    'parameter' => 'bg_color_type',
                    'value' => 'gradient'
                )
            ),
            'bg_grad_angle' => array(
                'type' => 'number',
                'label' => __('Gradient angle:', 'motopress-slider'),
                'default' => 0,
                'dependency' => array(
                    'parameter' => 'bg_color_type',
                    'value' => 'gradient'
                )
            ),
        )
    ),
    'image' => array(
        'title' => __('Image', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'col' => '12',
        'options' => array(
            'bg_image_type' => array(
                'type' => 'select',
                'label' => __('Background Image:', 'motopress-slider'),
                'description' => '',
                'default' => 'library',
                'disabled' => false,
                'list' => array(
                    'library' => __('Media Library', 'motopress-slider'),
                    'external' => __('External URL', 'motopress-slider'),

                ),
//                'dependency' => array(
//                    'parameter' => 'bg_types',
//                    'value' => 'image'
//                )
            ),
            'bg_image_id' => array(
                'type' => 'library_image',
//                'label' => __('Image background', 'motopress-slider'),
                'default' => '',
                'label' => '',
                'button_label' => __('Browse...', 'motopress-slider'),
                'select_label' => __('Insert image', 'motopress-slider'),
                'can_remove' => true,
                'dependency' => array(
                    'parameter' => 'bg_image_type',
                    'value' => 'library'
                ),
                'helpers' => array('bg_internal_image_url')
            ),
            'bg_internal_image_url' => array(
                'type' => 'hidden',
                'default' => '',
                'dependency' => array(
                    'parameter' => 'bg_image_type',
                    'value' => 'library'
                )
            ),
            'bg_image_url' => array(
                'type' => 'image_url',
//                'label' => __('Image url', 'motopress-slider'),
                'label' => '',
                'default' => '',
                'dependency' => array(
                    'parameter' => 'bg_image_type',
                    'value' => 'external'
                )
            ),
            'bg_fit' => array(
                'type' => 'select',
                'label' => __('Size:', 'motopress-slider'),
                'description' => '',
                'default' => 'cover',
                'disabled' => false,
                'list' => array(
                    'cover' => __('cover', 'motopress-slider'),
                    'contain' => __('contain', 'motopress-slider'),
                    'percentage' => __('(%, %)', 'motopress-slider'),
                    'normal' => __('normal', 'motopress-slider')
                ),
//                'dependency' => array(
//                    'parameter' => 'bg_types',
//                    'value' => 'image'
//                )
            ),
            'bg_fit_x' => array(
                'type' => 'number',
                'label' => __('Fit X:', 'motopress-slider'),
                'default' => 100,
//                'min' => 0,
//                'max' => 100,
                'dependency' => array(
                    'parameter' => 'bg_fit',
                    'value' => 'percentage'
                )
            ),
            'bg_fit_y' => array(
                'type' => 'number',
                'label' => __('Fit Y:', 'motopress-slider'),
                'default' => 100,
//                'min' => 0,
//                'max' => 100,
                'dependency' => array(
                    'parameter' => 'bg_fit',
                    'value' => 'percentage'
                )
            ),
            'bg_repeat' => array(
                'type' => 'select',
                'label' => __('Repeat:', 'motopress-slider'),
                'description' => '',
                'default' => 'no-repeat',
                'disabled' => false,
                'list' => array(
                    'no-repeat' => __('no-repeat', 'motopress-slider'),
                    'repeat' => __('repeat', 'motopress-slider'),
                    'repeat-x' => __('repeat-x', 'motopress-slider'),
                    'repeat-y' => __('repeat-y', 'motopress-slider')
                ),
//                'dependency' => array(
//                    'parameter' => 'bg_types',
//                    'value' => 'image'
//                )
            ),
            'bg_position' => array(
                'type' => 'select',
                'label' => __('Position:', 'motopress-slider'),
                'description' => '',
                'default' => 'center center',
                'disabled' => false,
                'list' => array(
                    'center top' => __('center top', 'motopress-slider'),
                    'center bottom' => __('center bottom', 'motopress-slider'),
                    'center center' => __('center center', 'motopress-slider'),
                    'left top' => __('left top', 'motopress-slider'),
                    'left center' => __('left center', 'motopress-slider'),
                    'left bottom' => __('left bottom', 'motopress-slider'),
                    'right top' => __('right top', 'motopress-slider'),
                    'right center' => __('right center', 'motopress-slider'),
                    'right bottom' => __('right bottom', 'motopress-slider'),
                    'percentage' => __('(x%, y%)', 'motopress-slider')
                ),
//                'dependency' => array(
//                    'parameter' => 'bg_types',
//                    'value' => 'image'
//                )
            ),
            'bg_position_x' => array(
                'type' => 'number',
                'label' => __('Position X:', 'motopress-slider'),
                'default' => 0,
//                'min' => 0,
//                'max' => 100,
                'dependency' => array(
                    'parameter' => 'bg_position',
                    'value' => 'percentage'
                )
            ),
            'bg_position_y' => array(
                'type' => 'number',
                'label' => __('Position Y:', 'motopress-slider'),
                'default' => 0,
//                'min' => 0,
//                'max' => 100,
                'dependency' => array(
                    'parameter' => 'bg_position',
                    'value' => 'percentage'
                )
            ),
        )
    ),
    'video' => array(
        'title' => __('Video', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'col' => '12',
        'options' => array(
            /* Video BG Start */
            'bg_video_src_mp4' => array(
                'type' => 'text',
                'default' => '',
                'label' => __('Video in MP4 format:', 'motopress-slider'),
//                'dependency' => array(
//                    'parameter' => 'bg_types',
//                    'value' => 'video'
//                )
            ),
            'bg_video_src_webm' => array(
                'type' => 'text',
                'default' => '',
                'label' => __('Video in WEBM format:', 'motopress-slider'),
//                'dependency' => array(
//                    'parameter' => 'bg_types',
//                    'value' => 'video'
//                )
            ),
            'bg_video_src_ogg' => array(
                'type' => 'text',
                'default' => '',
                'label' => __('Video in OGG format:', 'motopress-slider'),
//                'dependency' => array(
//                    'parameter' => 'bg_types',
//                    'value' => 'video'
//                )
            ),
            'bg_video_loop' => array(
                'type' => 'checkbox',
                'default' => false,
				'label' => '',
                'label2' => __('Loop', 'motopress-slider'),
//                'dependency' => array(
//                    'parameter' => 'bg_types',
//                    'value' => 'video'
//                )
            ),
            'bg_video_mute' => array(
                'type' => 'checkbox',
                'default' => false,
				'label' => '',
                'label2' => __('Mute', 'motopress-slider'),
//                'dependency' => array(
//                    'parameter' => 'bg_types',
//                    'value' => 'video'
//                )
            ),
            'bg_video_fillmode' => array(
                'type' => 'select',
                'default' => 'fill',
                'label' => __('Fill mode:', 'motopress-slider'),
                'list' => array(
                    'fill' => __('Fill', 'motopress-slider'),
                    'fit' => __('Fit', 'motopress-slider')
                ),
//                'dependency' => array(
//                    'parameter' => 'bg_types',
//                    'value' => 'video'
//                )
            ),
            'bg_video_cover' => array(
                'type' => 'checkbox',
                'default' => false,
				'label' => '',
                'label2' => __('Cover Video', 'motopress-slider'),
//                'dependency' => array(
//                    'parameter' => 'bg_types',
//                    'value' => 'video'
//                )
            ),
            'bg_video_cover_type' => array(
                'type' => 'select',
                'default' => '',
                'label' => __('Cover Type', 'motopress-slider'),
                'list' => array(
                    '' => __('None', 'motopress-slider'),
                    '2x2-black' => __('2 x 2 Black', 'motopress-slider'),
                    '2x2-white' => __('2 x 2 White', 'motopress-slider'),
                    '3x3-black' => __('3 x 3 Black', 'motopress-slider'),
                    '3x3-white' => __('3 x 3 White', 'motopress-slider')
                ),
                'dependency' => array(
                    'parameter' => 'bg_video_cover',
                    'value' => true
                )
            )
            /* Video BG End */
        )
    ),
    'link' => array(
        'title' => __('Link', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'col' => '12',
        'options' => array(
            'link' => array(
                'type' => 'text',
                'label' => __('Link this slide to:', 'motopress-slider'),
                'default' => ''
            ),
            'link_target' => array(
                'type' => 'checkbox',
                'label' => '',
                'label2' => __('Open in new window', 'motopress-slider'),
                'default' => false
            ),
            'link_id' => array(
                'type' => 'text',
                'label' => __('Link id:', 'motopress-slider'),
                'default' => ''
            ),
            'link_class' => array(
                'type' => 'text',
                'label' => __('Link class:', 'motopress-slider'),
                'default' => ''
            ),
            'link_rel' => array(
                'type' => 'text',
                'label' => __('Link rel:', 'motopress-slider'),
                'default' => ''
            ),
            'link_title' => array(
                'type' => 'text',
                'label' => __('Link title:', 'motopress-slider'),
                'default' => ''
            ),
        )
    ),
    'visibility' => array(
        'title' => __('Visibility', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'col' => '12',
        'options' => array(
            'need_logged_in' => array(
                'type' => 'checkbox',
                'label' => '',
                'label2' => __('Only logged-in users can view this slide', 'motopress-slider'),
                'default' => false
            ),
            'date_from' => array(
                'type' => 'datepicker',
                'label' => __('Visible from', 'motopress-slider'),
                'default' => '',
            ),
            'date_until' => array(
                'type' => 'datepicker',
                'label' => __('Visible until', 'motopress-slider'),
                'default' => '',
            ),
        )
    ),
    'misc' => array(
        'title' => __('Misc', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'col' => '12',
        'options' => array(
            'slide_classes' => array(
                'type' => 'text',
                'label' => __('HTML Class:', 'motopress-slider'),
                'default' => '',
            ),
            'slide_id' => array(
                'type' => 'text',
                'label' => __('HTML ID:', 'motopress-slider'),
                'default' => '',
            )
        )
    ),
);

if (in_array($sliderType, array('post', 'woocommerce'))) {
    $newImageOptions = array(
        'auto' => __('Auto Image', 'motopress-slider'),
        'featured' => __('Featured Image', 'motopress-slider'),
        'first' => __('First Image in Post', 'motopress-slider')
    );

    $slideSettings['image']['options']['bg_image_type']['list'] = array_merge($slideSettings['image']['options']['bg_image_type']['list'], $newImageOptions);

    $slideSettings['main']['hidden'] = true;
    unset($slideSettings['visibility']);
    unset($slideSettings['misc']['options']['slide_id']);
}

return $slideSettings;