<?php
/**
 * Add an element to fusion-builder.
 *
 * @package fusion-builder
 * @since 1.0
 */

if ( fusion_is_element_enabled( 'avada_views_addon' ) ) {

	if ( ! class_exists( 'MyHelloWorld' ) && class_exists( 'Fusion_Element' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @since 1.0
		 */
		class MyHelloWorld extends Fusion_Element {

			/**
			 * An array of the shortcode arguments.
			 *
			 * @access protected
			 * @since 1.0
			 * @var array
			 */
			protected $args;

			/**
			 * Constructor.
			 *
			 * @access public
			 * @since 1.0
			 */
			public function __construct() {
				parent::__construct();

				add_filter( 'fusion_attr_views-addon-wrapper', [ $this, 'attr' ] );
				add_filter( 'fusion_attr_separator-wrapper', [ $this, 'separator_wrapper_attr' ] );
				add_filter( 'fusion_attr_content', [ $this, 'content_attr' ] );

				add_shortcode( 'avada_views_addon', [ $this, 'render' ] );
			}

			/**
			 * Gets the default values.
			 *
			 * @static
			 * @access public
			 * @since 2.0.0
			 * @return array
			 */
			public static function get_element_defaults() {
				$fusion_settings = fusion_get_fusion_settings();

				return [
					'font_size'   => '',
					'color'      => $fusion_settings->get( 'fusion_addon_views__color' ),
					'background' => $fusion_settings->get( 'fusion_addon_views_background_color' ),
					'content_align' => 'auto',
					'style_type' => 'none',
					'padding_bottom'           => '',
					'padding_left'             => '',
					'padding_right'            => '',
					'padding_top'              => '',
					'separator_color'              => '',
				];
			}

			/**
			 * Maps settings to param variables.
			 *
			 * @static
			 * @access public
			 * @since 2.0.0
			 * @return array
			 */
			public static function settings_to_params() {
				return [
					'fusion_addon_views_content' => 'element_content',
					'fusion_addon_views_color'      => 'color',
					'fusion_addon_views_background_color' => 'background',
				];
			}

			/**
			 * Used to set any other variables for use on front-end editor template.
			 *
			 * @static
			 * @access public
			 * @since 2.0.0
			 * @return array
			 */
			public static function get_element_extras() {
				return [];
			}

			/**
			 * Maps settings to extra variables.
			 *
			 * @static
			 * @access public
			 * @since 2.0.0
			 * @return array
			 */
			public static function settings_to_extras() {
				return [];
			}

			/**
			 * Render the shortcode.
			 *
			 * @access public
			 * @since 1.0
			 * @param  array  $args    Shortcode parameters.
			 * @param  string $content Content between shortcode.
			 * @return string          HTML output.
			 */
			public function render( $args, $content = '' ) {
				$defaults   = FusionBuilder::set_shortcode_defaults( self::get_element_defaults(), $args, 'avada_views_addon' );
				$this->args = $defaults;

				$content = preg_replace( '/%total_views%/', '32342', $content);
				$content = preg_replace( '/%today_views%/', '32', $content);

				$html = '';

				if ( false !== strpos( $this->args['style_type'], 'double' ) || false !== strpos( $this->args['style_type'], 'single' ) ) {
					if(!empty($this->args['separator_color'])) {
 						$html .= '<style>.avada-views-addon-decoration::before,.avada-views-addon-decoration::after{border-color:' . $this->args['separator_color'] . ';</style>';
					}
				} elseif(false !== strpos( $this->args['style_type'], 'underline' )) {
					$styles = explode( ' ', $this->args['style_type'] );
					$border_bottom_color = $this->args['separator_color'];

					if ( isset( $styles[1] ) && in_array( $styles[1], array( 'dashed', 'dotted', 'solid' ) ) ) {
						$html .= '<style>.avada-views-addon-wrapper{border-bottom: 1px ' . $styles[1] . $border_bottom_color . ';}</style>';
					}
				}

				$html .= '<div ' . FusionBuilder::attributes( 'views-addon-wrapper' ) . '>';
				$html .= '<div ' . FusionBuilder::attributes( 'separator-wrapper' ) . '>';
				$html .= '<div ' . FusionBuilder::attributes( 'content' ) . '>';
				$html .= wpautop( $content, false );
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';

				return $html;
			}

			/**
			 * Builds the attributes array.
			 *
			 * @access public
			 * @since 1.0
			 * @return array
			 */
			public function attr() {
				$attr = [
					'class' => 'avada-views-addon-wrapper',
					'style' => 'color: ' . $this->args['color'] . '; background-color:' . $this->args['background'] . ';',
				];

				if( $this->args['content_align'] && $this->args['content_align'] !== 'auto' ) {
					$attr['style'] .= 'text-align:' . $this->args['content_align'] . ';';
				}

				if( $this->args['font_size'] && $this->args['font_size'] !== 'auto' ) {
					$attr['style'] .= 'font-size:' . $this->args['font_size'] . ';';
				}

				if( $this->args['padding_top'] ) {
					$attr['style'] .= 'padding-top:' . $this->args['padding_top'] . ';';
				}

				if( $this->args['padding_bottom'] ) {
					$attr['style'] .= 'padding-bottom:' . $this->args['padding_bottom'] . ';';
				}

				if( $this->args['padding_left'] ) {
					$attr['style'] .= 'padding-left:' . $this->args['padding_left'] . ';';
				}

				if( $this->args['padding_right'] ) {
					$attr['style'] .= 'padding-right:' . $this->args['padding_right'] . ';';
				}

				return $attr;
			}

			/**
			 * Builds the attributes for the separator wrapper.
			 *
			 * @return array
			 */
			public function separator_wrapper_attr() {
				$attr = [];

				if ( false !== strpos( $this->args['style_type'], 'double' ) || false !== strpos( $this->args['style_type'], 'single' ) ) {
					$styles = explode( ' ', $this->args['style_type'] );
					$styles = implode( '-', $styles );

					$class_name = 'avada-views-addon-decoration avada-views-addon-decoration--' . $styles;
					$attr['class'] .= $class_name;
				}

				return $attr;
			}

			/**
			 * Builds the attributes for the separator wrapper.
			 *
			 * @return array
			 */
			public function content_attr() {
				$attr = [];

				if ( false !== strpos( $this->args['style_type'], 'double' ) || false !== strpos( $this->args['style_type'], 'single' ) ) {
					$attr['style'] = 'display: inline-flex;flex-wrap: wrap;flex-direction: column;';
				}

				$attr['class'] = 'avada-views-addon-content';

				return $attr;
			}

			/**
			 * Adds settings to element options panel.
			 *
			 * @access public
			 * @since 1.1.6
			 * @return array $sections Blog settings.
			 */
			public function add_options() {
				return [
					'avada_views_addon_shortcode_section' => [
						'label'       => esc_attr__( 'Views Counter', 'avada-views-addon' ),
						'description' => '',
						'id'          => 'avada_views_addon_shortcode_section',
						'default'     => '',
						'icon'        => 'fusiona-eye',
						'type'        => 'accordion',
						'fields'      => [
							'fusion_addon_views_content' => [
								'label'       => esc_attr__( 'Content', 'avada-views-addon' ),
								'description' => esc_attr__( 'Text to display among the views. Use %total_views% or %today_views% to show the total/daily views.', 'avada-views-addon' ),
								'id'          => 'fusion_addon_views_content',
								'default'     => esc_attr__( 'Total views: %total_views%. Daily views: %today_views%', 'avada-views-addon' ),
								'type'        => 'text',
								'transport'   => 'postMessage',
							],
							'fusion_addon_views_color' => [
								'label'       => esc_attr__( 'Text Color', 'avada-views-addon' ),
								'description' => esc_attr__( 'Set the global text color.', 'avada-views-addon' ),
								'id'          => 'fusion_addon_views_color',
								'default'     => '',
								'type'        => 'color-alpha',
								'transport'   => 'postMessage',
							],
							'fusion_addon_views_background_color' => [
								'label'       => esc_attr__( 'Background Color', 'hello-world' ),
								'description' => esc_attr__( 'Set the global background color.', 'hello-world' ),
								'id'          => 'fusion_addon_views_background_color',
								'default'     => '',
								'type'        => 'color-alpha',
								'transport'   => 'postMessage',
							],
						],
					],
				];
			}

			/**
			 * Sets the necessary scripts.
			 *
			 * @access public
			 * @since 1.1
			 * @return void
			 */
			public function add_scripts() {

				/* For example.
				Fusion_Dynamic_JS::enqueue_script(
					'fusion-date-picker',
					FUSION_BUILDER_PLUGIN_URL . 'assets/js/library/flatpickr.js',
					FUSION_BUILDER_PLUGIN_URL . 'assets/js/library/flatpickr.js',
					[ 'jquery' ],
					'1',
					true
				);
				*/
			}

			/**
			 * Load element base CSS.
			 *
			 * @access public
			 * @since 3.0
			 * @return void
			 */
			public function add_css_files() {
				FusionBuilder()->add_element_css( SAMPLE_ADDON_PLUGIN_DIR . 'css/my-elements.css' );
			}
		}
	}

	new MyHelloWorld();
}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function avada_views_addon_map() {

	$fusion_settings = fusion_get_fusion_settings();

	$is_builder = ( function_exists( 'fusion_is_preview_frame' ) && fusion_is_preview_frame() ) || ( function_exists( 'fusion_is_builder_frame' ) && fusion_is_builder_frame() );
	$to_link    = '';

	if ( $is_builder ) {
		$to_link = '<span class="fusion-panel-shortcut" data-fusion-option="headers_typography_important_note_info">' . esc_html__( 'Global Options Heading Settings', 'fusion-builder' ) . '</span>';
	} else {
		$to_link = '<a href="' . esc_url( $fusion_settings->get_setting_link( 'headers_typography_important_note_info' ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Global Options Heading Settings', 'fusion-builder' ) . '</a>';
	}

	fusion_builder_map(
		fusion_builder_frontend_data(

			// Class reference.
			'MyHelloWorld',
			[
				'name'                     => esc_attr__( 'Views Counter', 'avada-views-addon' ),
				'shortcode'                => 'avada_views_addon',
				'icon'                     => 'fusiona-eye',

				// View used on front-end.
				'front_end_custom_settings_view_js' => SAMPLE_ADDON_PLUGIN_URL . 'elements/front-end/hello-world.js',

				// Template that is used on front-end.
				'front-end'                         => SAMPLE_ADDON_PLUGIN_DIR . '/elements/front-end/hello-world.php',

				'allow_generator'          => false,

				// Allows inline editor.
				'inline_editor'            => true,
				'inline_editor_shortcodes' => true,

				'params'                   => [
					[
						'type'        => 'tinymce',
						'heading'     => esc_attr__( 'Content', 'avada-views-addon' ),
						'description' => esc_attr__( 'Enter the text to display among the views. Use %total_views% or %today_views% to show the total/daily views.', 'avada-views-addon' ),
						'param_name'  => 'element_content',
						'dynamic_data' => true,
						'value'       => $fusion_settings->get( 'fusion_addon_views_content' ) ? $fusion_settings->get( 'fusion_addon_views_content' ) : esc_attr__( 'Total views: %total_views%. Daily views: %today_views%', 'avada-views-addon' ),
					],
					[
						'type'        => 'textfield',
						'heading'     => esc_attr__( 'Font Size', 'fusion-builder' ),
						/* translators: URL for the link. */
						'description' => sprintf( esc_html__( 'Controls the font size of the text. Enter value including any valid CSS unit, ex: 20px. Leave empty if the global font size for the corresponding heading size (h1-h6) should be used: %s.', 'fusion-builder' ), $to_link ),
						'param_name'  => 'font_size',
						'value'       => '',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Text Color', 'avada-views-addon' ),
						'description' => esc_attr__( 'Set the text color for the hello.', 'avada-views-addon' ),
						'param_name'  => 'color',
						'default'     => $fusion_settings->get( 'fusion_addon_views_color' ) ? $fusion_settings->get( 'fusion_addon_views_color' ) : '',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Background Color', 'avada-views-addon' ),
						'description' => esc_attr__( 'Set the background color.', 'avada-views-addon' ),
						'param_name'  => 'background',
						'default'     => $fusion_settings->get( 'fusion_addon_views_background_color' ) ? $fusion_settings->get( 'fusion_addon_views_background_color' ) : '',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
					],
					[
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Alignment', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose to align the heading left, right or center.', 'fusion-builder' ),
						'param_name'  => 'content_align',
						'value'       => [
							'auto'   => esc_attr__( 'Language Default', 'fusion-builder' ),
							'left'   => esc_attr__( 'Left', 'fusion-builder' ),
							'center' => esc_attr__( 'Center', 'fusion-builder' ),
							'right'  => esc_attr__( 'Right', 'fusion-builder' ),
						],
						'default'     => 'auto',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
					],
					[
						'type'             => 'dimension',
						'remove_from_atts' => true,
						'heading'          => esc_attr__( 'Padding', 'fusion-builder' ),
						'description'      => esc_attr__( 'In pixels or percentage, ex: 10px or 10%.', 'fusion-builder' ),
						'param_name'       => 'padding',
						'value'            => [
							'padding_top'    => '',
							'padding_right'  => '',
							'padding_bottom' => '',
							'padding_left'   => '',
						],
						'group'            => esc_attr__( 'Design', 'fusion-builder' ),
					],
					[
						'type'        => 'select',
						'heading'     => esc_attr__( 'Separator', 'fusion-builder' ),
						'description' => esc_attr__( 'Choose the kind of the title separator you want to use.', 'fusion-builder' ),
						'param_name'  => 'style_type',
						'value'       => [
							'none'          => esc_attr__( 'None', 'fusion-builder' ),
							'single solid'     => esc_attr__( 'Single Solid', 'fusion-builder' ),
							'single dashed'    => esc_attr__( 'Single Dashed', 'fusion-builder' ),
							'single dotted'    => esc_attr__( 'Single Dotted', 'fusion-builder' ),
							'double solid'     => esc_attr__( 'Double Solid', 'fusion-builder' ),
							'double dashed'    => esc_attr__( 'Double Dashed', 'fusion-builder' ),
							'double dotted'    => esc_attr__( 'Double Dotted', 'fusion-builder' ),
							'underline solid'  => esc_attr__( 'Underline Solid', 'fusion-builder' ),
							'underline dashed' => esc_attr__( 'Underline Dashed', 'fusion-builder' ),
							'underline dotted' => esc_attr__( 'Underline Dotted', 'fusion-builder' ),
						],
						'default'     => 'none',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
					],
					[
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Separator Color', 'avada-views-addon' ),
						'description' => esc_attr__( 'Defaults to text color.', 'fusion-builder' ),
						'param_name'  => 'separator_color',
						'default'     => '',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
					],
				],
			]
		)
	);
}
add_action( 'fusion_builder_before_init', 'avada_views_addon_map' );
