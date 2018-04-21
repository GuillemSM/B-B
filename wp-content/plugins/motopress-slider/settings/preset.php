<?php

// TODO: Cache ALL settings

$fonts = array();
if (!(defined('DOING_AJAX') and DOING_AJAX) and is_admin()) {
	$fonts = wp_cache_get('mpsl_gfonts');
	if (false === $fonts) {
		$fonts = MPSLLayerPresetOptions::getFontList(true);
		array_walk($fonts, function(&$item) {
			$item = array(
				'label' => $item['family'],
				'value' => $item['family'],
				'attrs' => array(
					'data-variants' => $item['variants'] // Type: json
//					'data-variants' => array_map(function($el) { return $el['value']; }, $item['variants']) // Type: split
				)
			);
		});
		$fonts['']['label'] = '-- ' . __('SELECT', 'motopress-slider') . ' --';
		wp_cache_set('mpsl_gfonts', $fonts);
	}
}

return array(
    'font-typography' => array(
        'title' => __('Font and typography', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'col' => '12',
        'options' => array(
	        'allow_style' => array(
		        'type' => 'checkbox',
		        'label2' => __('Enable mouse over styles', 'motopress-slider'),
		        'default' => true
	        ),

            'background-color' => array(
                'type' => 'color_picker',
                'label' => __('Background color:', 'motopress-slider'),
                'default' => '',
	            'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
            'color' => array(
                'type' => 'color_picker',
                'label' => __('Text color:', 'motopress-slider'),
                'default' => '',
	            'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
            'font-size' => array(
                'type' => 'number',
                'label' => __('Font size:', 'motopress-slider'),
                'default' => '',
	            'min' => 0,
				'unit' => 'px',
	            'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
			'font-family' => array(
                'type' => 'font_picker',
                'label' => __('Font:', 'motopress-slider'),
                'default' => '',
				/**
				 * Data format:
	             *  - split: [index] => value  // Value uses both for value and label. Label = ucfirst(Value)
	             *  - json: [index] => { value => 'value', label => 'label' }
				 */
				// NOTE: regular -> normal | (?) skip italic & [number]italic | default - regular, :first or empty
	            'list' => $fonts,
				/** Element key - where dynamic data is taken */
	            'listAttrSettings' => array(
		            /**
		             * Type: json | split
		             * Delimiter: Used only with type `split`
		             */
		            'data-variants' => array(
			            'type' => 'json',
//			            'type' => 'split',
//						'delimiter' => ',',
		            )
	            ),
				'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
			'font-weight' => array(
                'type' => 'select',
                'label' => __('Weight:', 'motopress-slider'),
                'default' => '',
				'helpers' => array('font-family'),
				'dynamicList' => array(
					'parameter' => 'font-family',
					'attr' => 'data-variants',
					/*
					regexp instanceof RegExp
					regexp.test('str')
					str.replace(regexp, replacement)
					*/
//					'filter' => '/^((?!italic).)*$/i', // string|regexp
//					'replace' => array(
//						'value' => array(
//							'pattern' => 'regular', // string|regexp
//							'replacement' => 'normal'
//						)
//					),
				),
				'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
	        'font-style' => array(
		        'type' => 'select',
		        'label' => __('Font style:', 'motopress-slider'),
		        'default' => '',
		        'list' => array(
			        '' => 'Inherit',
			        'italic' => 'Italic'
		        ),
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
	        ),
//	        'font-style' => array(
//		        'type' => 'checkbox',
//		        'label' => __('Italic:', 'motopress-slider'),
//		        'default' => false
//	        ),
//			'white-space' => array(
//                'type' => 'checkbox',
//                'label' => __('Wordwrap:', 'motopress-slider'),
//                'default' => false
//            ),
            'letter-spacing' => array(
                'type' => 'number',
                'label' => __('Letter spacing:', 'motopress-slider'),
                'default' => '',
				'unit' => 'px',
	            'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
	        'line-height' => array(
                'type' => 'number',
                'label' => __('Line height:', 'motopress-slider'),
                'default' => '', // normal
		        'min' => 0,
				'unit' => 'px',
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
	        'text-align' => array(
                'type' => 'select',
                'label' => __('Text align:', 'motopress-slider'),
                'default' => '',
		        'list' => array(
			        '' => 'Inherit',
			        'left' => 'Left',
			        'center' => 'Center',
			        'right' => 'Right',
			        'justify' => 'Justify'
		        ),
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
        )
    ),
    'text-shadow' => array(
        'title' => __('Text Shadow', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'col' => '12',
        'options' => array(
			'text-shadow' => array(
                'type' => 'text_shadow',
                'default' => '',
				'options' => array(
					'text_shadow_color' => array(
						'type' => 'color_picker',
						'label' => __('Color:', 'motopress-slider'),
						'default' => ''
					),
					'text_shadow_hor_len' => array(
						'type' => 'number',
						'label' => __('Horizontal Length:', 'motopress-slider'),
						'default' => '',
						'unit' => 'px'
					),
					'text_shadow_vert_len' => array(
						'type' => 'number',
						'label' => __('Vertical Length:', 'motopress-slider'),
						'default' => '',
						'unit' => 'px'
					),
					'text_shadow_radius' => array(
						'type' => 'number',
						'label' => __('Radius:', 'motopress-slider'),
						'default' => '',
						'min' => 0,
						'unit' => 'px'
					)
				),
	            'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
        )
    ),
    'border' => array(
        'title' => __('Border', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'col' => '12',
        'options' => array(
            'border-style' => array(
                'type' => 'select',
                'label' => __('Border Style:', 'motopress-slider'),
                'default' => '',
	            'list' => array(
		            'none' => 'None',
		            '' => 'Inherit',
		            'hidden' => 'Hidden',
		            'solid' => 'Solid',
		            'dotted' => 'Dotted',
		            'dashed' => 'Dashed',
		            'double' => 'Double',
		            'groove' => 'Groove',
		            'ridge' => 'Ridge',
		            'inset' => 'Inset',
		            'outset' => 'Outset'
	            ),
	            'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
            /*'border-width' => array(
                'type' => 'number',
                'label' => __('Border Width:', 'motopress-slider'),
                'default' => '',
	            'min' => 0,
				'unit' => 'px'
//	            'dependency' => array(
//                    'parameter' => 'border-style',
//                    'except' => array('none', 'hidden', 'initial', 'inherit')
//                )
            ),*/
	        'border-top-width' => array(
                'type' => 'number',
                'label' => __('Top:', 'motopress-slider'),
                'default' => '',
	            'min' => 0,
				'unit' => 'px',
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
            'border-right-width' => array(
                'type' => 'number',
                'label' => __('Right:', 'motopress-slider'),
                'default' => '',
	            'min' => 0,
				'unit' => 'px',
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
            'border-bottom-width' => array(
                'type' => 'number',
                'label' => __('Bottom:', 'motopress-slider'),
                'default' => '',
	            'min' => 0,
				'unit' => 'px',
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
            'border-left-width' => array(
                'type' => 'number',
                'label' => __('Left:', 'motopress-slider'),
                'default' => '',
	            'min' => 0,
				'unit' => 'px',
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
            'border-color' => array(
                'type' => 'color_picker',
                'label' => __('Border Color:', 'motopress-slider'),
                'default' => '',
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
            'border-radius' => array(
                'type' => 'number',
                'label' => __('Border Radius:', 'motopress-slider'),
                'default' => '',
	            'min' => 0,
				'unit' => 'px',
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
        )
    ),
    'padding' => array(
        'title' => __('Padding', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'col' => '12',
        'options' => array(
            'padding-top' => array(
                'type' => 'number',
                'label' => __('Top:', 'motopress-slider'),
                'default' => '',
	            'min' => 0,
				'unit' => 'px',
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
            'padding-right' => array(
                'type' => 'number',
                'label' => __('Right:', 'motopress-slider'),
                'default' => '',
	            'min' => 0,
				'unit' => 'px',
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
            'padding-bottom' => array(
                'type' => 'number',
                'label' => __('Bottom:', 'motopress-slider'),
                'default' => '',
	            'min' => 0,
				'unit' => 'px',
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
            'padding-left' => array(
                'type' => 'number',
                'label' => __('Left:', 'motopress-slider'),
                'default' => '',
	            'min' => 0,
				'unit' => 'px',
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
        )
    ),
    'advanced-editor' => array(
        'title' => __('Advanced Editor', 'motopress-slider'),
        'icon' => null,
        'description' => '',
        'col' => '12',
        'options' => array(
			'custom_styles' => array(
                'type' => 'codemirror',
                'mode' => 'css',
                'label2' => __('Custom styles', 'motopress-slider'),
                'default' => '',
		        'disabled_dependency' => array(
	                'parameter' => 'allow_style',
                    'value' => true
                )
            ),
        )
    ),
);
