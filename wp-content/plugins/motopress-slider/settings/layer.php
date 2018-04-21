<?php
/** @var MPSLSlideOptions $this */
$sliderType = $this->getSliderType();

$result =  array(

	// --------------------- Content ---------------------
	'content' => array(
        'title' => __('Content', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'options' => array(
        	'type' => array(
                'type' => 'select',
                'layer_type' => 'all',
                'default' => 'html',
                'list' => array(
                    'html' => 'html',
                    'image' => 'image',
                    'button' => 'button',
                    'video' => 'video'
                ),
                'hidden' => true
            ),
        	'text' => array(
                'type' => 'tiny_mce',
	            'layer_type' => array('html'),
                'label' => __('Text/HTML', 'motopress-slider'),
                'default' => __('lorem ipsum', 'motopress-slider'),
                'plugins' => array(),
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'html'
                )
            ),
            'button_text' => array(
                'type' => 'text',
	            'layer_type' => array('button'),
                'label' => __('Button Text', 'motopress-slider'),
                'default' => __('Button', 'motopress-slider'),
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'button'
                )
            ),
	        'button_link' => array(
                'type' => 'text',
	            'layer_type' => array('button'),
                'label' => __('Link:', 'motopress-slider'),
                'default' => '#',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'button'
                )
            ),
            'button_autolink' => array(
                'type' => 'action_group',
	            'layer_type' => array('button'),
                'label' => __('To Post', 'motopress-slider'),
                'default' => '',
                'list' => array(
                    'permalink' => __('#link to post', 'motopress-slider')
                ),
	            'actions' => array(
                    'permalink' => array(
                        'button_link' => '%permalink%',
                    ),
                ),
                'classes' => 'button-link',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'button'
                )
            ),
            'button_target' => array(
                'type' => 'checkbox',
	            'layer_type' => array('button'),
                'label2' => __('Open in new window', 'motopress-slider'),
                'default' => 'false',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'button'
                )
            ),
            'image_id' => array(
                'type' => 'library_image',
	            'layer_type' => array('image'),
//                'label2' => __('Image', 'motopress-slider'),
                'default' => '',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                ),
                'helpers' => array('image_url'),
                'button_label' => __('Select Image', 'motopress-slider'),
                'select_label' => __('Select Image', 'motopress-slider')
            ),
            'image_url' => array(
                'type' => 'hidden',
	            'layer_type' => array('image'),
                'default' => '',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                ),
            ),
	        'image_link' => array(
                'type' => 'text',
		        'layer_type' => array('image'),
                'label' => __('Link:', 'motopress-slider'),
                'default' => '',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                )
            ),
            'image_target' => array(
                'type' => 'checkbox',
	            'layer_type' => array('image'),
                'label2' => __('Open in new window', 'motopress-slider'),
                'default' => 'false',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                )
            ),
            'image_autolink' => array(
                'type' => 'action_group',
	            'layer_type' => array('image'),
                'label' => __('To Post', 'motopress-slider'),
                'default' => '',
                'list' => array(
                    'permalink' => __('#link to post', 'motopress-slider')
                ),
                'actions' => array(
                    'permalink' => array(
                        'image_link' => '%permalink%',
                    ),
                ),
                'classes' => 'button-link',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                )
            ),
            'video_type' => array(
                'type' => 'button_group',
	            'layer_type' => array('video'),
                'default' => 'youtube',
                'list' => array(
                    'youtube' => __('Youtube', 'motopress-slider'),
                    'vimeo' => __('Vimeo', 'motopress-slider'),
                    'html' => __('Media Library', 'motopress-slider')
                ),
                'button_size' => 'large',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                )
            ),
//            'video_id' => array(
//                'type' => 'library_video',
//                'default' => '',
//                'dependency' => array(
//                    'parameter' => 'video_type',
//                    'value' => 'html'
//                ),
//                'button_label' => __('Select Video', 'motopress-slider')
//            ),
            'video_src_mp4' => array(
                'type' => 'text',
	            'layer_type' => array('video'),
                'default' => '',
                'label' => __('Source MP4: ', 'motopress-slider'),
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'html'
                )
            ),
            'video_src_webm' => array(
                'type' => 'text',
	            'layer_type' => array('video'),
                'default' => '',
                'label' => __('Source WEBM: ', 'motopress-slider'),
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'html'
                )
            ),
            'video_src_ogg' => array(
                'type' => 'text',
	            'layer_type' => array('video'),
                'default' => '',
                'label' => __('Source OGG: ', 'motopress-slider'),
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'html'
                )
            ),
            'youtube_src' => array(
                'type' => 'text',
	            'layer_type' => array('video'),
                'default' => '',
	            'label' => __('Link to YouTube video:', 'motopress-slider'),
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'youtube'
                )
            ),
            'vimeo_src' => array(
                'type' => 'text',
	            'layer_type' => array('video'),
                'default'=> '',
	            'label' => __('Link to Vimeo video:', 'motopress-slider'),
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'vimeo'
                )
            ),
            'video_preview_image' => array(
                'type' => 'text',
	            'layer_type' => array('video'),
                'default' => '',
                'label' => __('Preview Image URL:', 'motopress-slider'),
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                )
            ),
            'video_autoplay' => array(
                'type' => 'checkbox',
	            'layer_type' => array('video'),
                'label' => __('Autoplay', 'motopress-slider'),
                'default' => false,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                )
            ),
//            'video_loop' => array(
//                'type' => 'select',
//                'label' => __('Loop', 'motopress-slider'),
//                'default' => 'disabled',
//                'list' => array(
//                    'disabled' => __('disabled', 'motopress-slider'),
//                    'loop' => __('Loop', 'motopress-slider')
//                ),
//                'dependency' => array(
//                    'parameter' => 'type',
//                    'value' => 'video'
//                )
//            ),
            'video_loop' => array(
                'type' => 'checkbox',
	            'layer_type' => array('video'),
                'label' => __('Loop', 'motopress-slider'),
                'default' => false,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                )
            ),
            'video_html_hide_controls' => array(
                'type' => 'checkbox',
	            'layer_type' => array('video'),
                'label' => __('Hide Controls', 'motopress-slider'),
                'default' => false,
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'html'
                )
            ),
            'video_youtube_hide_controls' => array(
                'type' => 'checkbox',
	            'layer_type' => array('video'),
                'label' => __('Hide Controls', 'motopress-slider'),
                'default' => false,
                'dependency' => array(
                    'parameter' => 'video_type',
                    'value' => 'youtube'
                )
            ),
            'video_mute' => array(
                'type' => 'checkbox',
	            'layer_type' => array('video'),
                'label' => __('Mute', 'motopress-slider'),
                'default' => false,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                )
            ),
            'video_disable_mobile' => array(
                'type' => 'checkbox',
	            'layer_type' => array('video'),
                'label' => __('Disable/Hide on Mobile', 'motopress-slider'),
                'default' => false,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                )
            ),
        )
	),

	// --------------------- Position & Size ---------------------
	'position_size' => array(
        'title' => __('Position & Size', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'options' => array(
            'align' => array(
                'type' => 'align_table',
	            'layer_type' => 'all',
                'default' => array(
                    'vert' => 'middle',
                    'hor' => 'center'
                ),
	            'layout_dependent' => true,

                'options' => array(
                    'vert_align' => array(
                        'type' => 'hidden',
	                    'layer_type' => 'all',
                        'default' => 'middle',
		                'layout_dependent' => true
                    ),
                    'hor_align' => array(
                        'type' => 'hidden',
	                    'layer_type' => 'all',
                        'default' => 'center',
		                'layout_dependent' => true
                    ),
                    'offset_x' => array(
                        'type' => 'number',
	                    'layer_type' => 'all',
                        'default' => 0,
                        'label2' => __('X:', 'motopress-slider'),
		                'layout_dependent' => true
                    ),
                    'offset_y' => array(
                        'type' => 'number',
	                    'layer_type' => 'all',
                        'default' => 0,
                        'label2' => __('Y:', 'motopress-slider'),
		                'layout_dependent' => true
                    )
                )
            ),
	        'resizable' => array(
                'type' => 'checkbox',
	            'layer_type' => 'all',
                'label2' => __('Resize layer automatically when resizing browser', 'motopress-slider'),
                'default' => true,
            ),
            'dont_change_position' => array(
                'type' => 'checkbox',
	            'layer_type' => 'all',
                'label2' => __('Don\'t change layer position when resizing browser', 'motopress-slider'),
                'default' => false,
            ),
            'hide_width' => array(
                'type' => 'number',
	            'layer_type' => 'all',
                'label' => __('Hide layer after this width (px)', 'motopress-slider'),
//                'label2' => '',
                'default' => '',
                'min' => 0,
            ),
	        'width' => array(
                'type' => 'number',
	            'layer_type' => array('image'),
                'label2' => __('W:', 'motopress-slider'),
//                'default' => 300,
                'default' => '',
                'min' => 1,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                ),
                'layout_dependent' => true
            ),
            'html_width' => array(
                'type' => 'number',
	            'layer_type' => array('html'),
                'label2' => __('W:', 'motopress-slider'),
                'default' => '',
                'min' => 1,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'html'
                ),
                'layout_dependent' => true
            ),
	        'video_width' => array(
                'type' => 'number',
	            'layer_type' => array('video'),
                'label2' => 'W:',
                'default' => 427,
//                'min' => 1,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                ),
                'layout_dependent' => true
            ),
            'video_height' => array(
                'type' => 'number',
	            'layer_type' => array('video'),
                'label2' => 'H:',
                'default' => 240,
//                'min' => 1,
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'video'
                ),
                'layout_dependent' => true
            ),
        )
	),

	// --------------------- Animation ---------------------
	'animation' => array(
        'title' => __('Animation', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'options' => array(
        	'start_animation' => $this->getOptionsByType('start', 'animation', false),
            'start_timing_function' => $this->getOptionsByType('start', 'easings', false),
            'start_duration' => $this->getOptionsByType('start', 'duration', false),
            'end_animation' => $this->getOptionsByType('end', 'animation', false),
            'end_timing_function' => $this->getOptionsByType('end', 'easings', false),
            'end_duration' => $this->getOptionsByType('end', 'duration', false),

            'start_animation_group' => array(
                'type' => 'animation_control',
	            'layer_type' => 'all',
                'id' => 'start_animation_btn',
                'animation_type' => 'start',
                'text' => __('Edit', 'motopress-slider'),
                'skip' => true,
                'skipChild' => true,
                'options' => array(
                    'start_duration_clone' => $this->getOptionsByType('start', 'duration', true),
                    'start_timing_function_clone' => $this->getOptionsByType('start', 'easings',true),
                    'start_animation_clone' => $this->getOptionsByType('start', 'animation', true),
                ),
            ),
            'end_animation_group' => array(
                'type' => 'animation_control',
	            'layer_type' => 'all',
                'id' => 'end_animation_btn',
                'animation_type' => 'end',
                'text' => __('Edit', 'motopress-slider'),
                'skip' => true,
                'skipChild' => true,
                'options' => array(
                    'end_duration_clone' => $this->getOptionsByType('end','duration', true),
                    'end_timing_function_clone' => $this->getOptionsByType('end', 'easings', true),
                    'end_animation_clone' => $this->getOptionsByType('end', 'animation', true),
                ),
            ),
            'start' => array(
                'type' => 'number',
	            'layer_type' => 'all',
                'label2' => __('Display at (ms): ', 'motopress-slider'),
                'default' => 1000,
                'min' => 0,
//                'max' => 9000,
            ),
            'end' => array(
                'type' => 'number',
	            'layer_type' => 'all',
                'label2' => __('Hide at (ms): ', 'motopress-slider'),
                'default' => 0,
                'min' => 0
            ),
        )
	),

	// --------------------- Style ---------------------
	'style' => array(
        'title' => __('Style', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'options' => array(
			'preset' => array(
                'type' => 'style_editor',
	            'layer_type' => 'all',
                'label2' => __('Style: ', 'motopress-slider'),
                'edit_label' => __('Edit', 'motopress-slider'),
                'remove_label' => __('Clear', 'motopress-slider'),
	            'helpers' => array('private_styles'),
	            'default' => '',
            ),
            'private_preset_class' => array(
                'type' => 'hidden',
	            'layer_type' => 'all',
                'default' => ''
            ),
            'private_styles' => array(
                'type' => 'multiple',
	            'layer_type' => 'all',
                'default' => array() // JSON
            ),
//            'hover_styles' => array(
//                'type' => 'multiple',
//                'layer_type' => array('html', 'button'),
//                'default' => array(),
//	            /*'skip' => true,
//	            'hidden' => true,
//	            'dependency' => array(
//		            'parameter' => 'type',
//		            'value' => array('html', 'button'),
//	            ),*/
//            ),
	        'classes' => array(
                'type' => 'text',
		        'layer_type' => 'all',
                'label2' => __('CSS Classes: ', 'motopress-slider'),
                'default' => ''
            ),
	        'image_link_classes' => array(
                'type' => 'text',
		        'layer_type' => array('image'),
                'label2' => __('Link Custom Classes: ', 'motopress-slider'),
                'default' => '',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'image'
                )
            ),

	        // Deprecated
	        'html_style' => array(
                'type' => 'select',
		        'layer_type' => array('html'),
                'label' => __('Theme Styles (deprecated)', 'motopress-slider'),
                'default' => '',
                'list' => array(
                    '' => __('none', 'motopress-slider'),
                    'mpsl-header-dark' => __('Header Dark', 'motopress-slider'),
                    'mpsl-header-white' => __('Header White', 'motopress-slider'),
                    'mpsl-sub-header-dark' => __('Sub-Header Dark', 'motopress-slider'),
                    'mpsl-sub-header-white' => __('Sub-Header White', 'motopress-slider'),
                    'mpsl-text-dark' => __('Text Dark', 'motopress-slider'),
                    'mpsl-text-white' => __('Text White', 'motopress-slider'),
                ),
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'html'
                )
            ),
            'button_style' => array(
                'type' => 'select',
	            'layer_type' => array('button'),
                'label' => __('Theme Styles (deprecated)', 'motopress-slider'),
                'default' => '',
                'list' => array(
                    '' => __('none', 'motopress-slider'),
                    'mpsl-button-blue' => __('Button Blue', 'motopress-slider'),
                    'mpsl-button-green' => __('Button Green', 'motopress-slider'),
                    'mpsl-button-red' => __('Button Red', 'motopress-slider')
                ),
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'button'
                )
            ),

	        // It's important to name font layer settings as their equivalent in CSS
            'font-size' => array(
                'type' => 'number',
	            'layer_type' => array('html', 'button'),
                'label' => __('Font size', 'motopress-slider') . '*',
                'default' => '',
                'min' => 0,
                'unit' => 'px',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => array('html', 'button'),
                ),
                'layout_dependent' => true
            ),
            'line-height' => array(
                'type' => 'number',
	            'layer_type' => array('html', 'button'),
                'label' => __('Line height', 'motopress-slider') . '*',
                'default' => '',
                'min' => 0,
                'unit' => 'px',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => array('html', 'button'),
                ),
                'layout_dependent' => true
            ),
	        'text-align' => array(
                'type' => 'select',
		        'layer_type' => array('html'),
                'label' => __('Text align', 'motopress-slider') . '*',
                'default' => '',
		        'list' => array(
			        '' => __('Default', 'motopress-slider'),
			        'left' => __('Left', 'motopress-slider'),
			        'center' => __('Center', 'motopress-slider'),
			        'right' => __('Right', 'motopress-slider'),
			        'justify' => __('Justify', 'motopress-slider')
		        ),
		        'dependency' => array(
                    'parameter' => 'type',
                    'value' => array('html')
                ),
                'layout_dependent' => true
            ),
	        'white-space' => array(
                'type' => 'select',
	            'layer_type' => array('html'),
                'label' => __('Whitespace', 'motopress-slider') . '*',
                'default' => 'normal',
                'list' => array(
	                'normal' => __('Normal', 'motopress-slider'),
	                'nowrap' => __('No-wrap', 'motopress-slider')
                ),
                'dependency' => array(
                    'parameter' => 'type',
                    'value' => 'html'
                ),
	            'layout_dependent' => true
            ),
        )
	)

);

if ($sliderType === 'custom') {
    unset($result['content']['options']['button_autolink']);
    unset($result['content']['options']['image_autolink']);

} else { // post | woocommerce
	$result['content']['options']['text']['default'] = '%title%';
}

return $result;
