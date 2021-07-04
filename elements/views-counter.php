<?php
/**
 * Add an element to fusion-builder.
 *
 * @package Fusion_Views_Addon
 * @since 1.0
 */

use Fusion_Views_Addon\Views_Counter;
use function Fusion_Views_Addon\get_fusion_dynamic_data_total_post_views_num;
use function Fusion_Views_Addon\get_fusion_dynamic_data_today_post_views_num;

if ( fusion_is_element_enabled( 'fusion_views_addon' ) ) {

	if ( ! class_exists( 'Fusion_Views_Addon' ) && class_exists( 'Fusion_Element' ) ) {
		/**
		 * Shortcode class.
		 *
		 * @since 1.0
		 */
		class Fusion_Views_Addon extends Fusion_Element {

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

				add_filter( 'fusion_attr_views-addon-wrapper', array( $this, 'attr' ) );
				add_filter( 'fusion_attr_separator-wrapper', array( $this, 'separator_wrapper_attr' ) );
				add_filter( 'fusion_attr_content', array( $this, 'content_attr' ) );

				add_shortcode( 'fusion_views_addon', array( $this, 'render' ) );
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

				return array(
					'element_content' => $fusion_settings->get( 'fusion_addon_views_content' ),
					'hide_on_mobile'  => fusion_builder_default_visibility( 'string' ),
					'font_size'       => '',
					'color'           => $fusion_settings->get( 'fusion_addon_views_color' ),
					'background'      => $fusion_settings->get( 'fusion_addon_views_background_color' ),
					'content_align'   => 'auto',
					'style_type'      => 'none',
					'padding_bottom'  => '',
					'padding_left'    => '',
					'padding_right'   => '',
					'padding_top'     => '',
					'separator_color' => '',
					'class'           => '',
				);
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
				return array(
					'fusion_addon_views_content'          => 'element_content',
					'fusion_addon_views_color'            => 'color',
					'fusion_addon_views_background_color' => 'background',
				);
			}

			/**
			 * Check if component should render
			 *
			 * @access public
			 * @since 2.4
			 * @return boolean
			 */
			public function should_render() {
				return is_singular();
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
				return array(
					'total_views_addon' => get_fusion_dynamic_data_total_post_views_num(),
					'today_views_addon' => get_fusion_dynamic_data_today_post_views_num(),
				);
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
				return array();
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
				$defaults   = FusionBuilder::set_shortcode_defaults( self::get_element_defaults(), $args, 'fusion_views_addon' );
				$this->args = $defaults;

				$id = 'avada-views-addon-wrapper--' . uniqid();

				global $post;
				$views_counter = null;
				try {
					$views_counter = new Views_Counter( $post->ID );
					$content       = preg_replace( '/%total_views%/', $views_counter->get_total_views_format_i18n(), $content );
					$content       = preg_replace( '/%today_views%/', $views_counter->get_today_views_format_i18n(), $content );
				} catch ( RuntimeException $e ) {
					$content = preg_replace( '/%total_views%/', '1', $content );
					$content = preg_replace( '/%today_views%/', '1', $content );
				}

				$html = '';

				if ( false !== strpos( $this->args['style_type'], 'double' ) || false !== strpos( $this->args['style_type'], 'single' ) ) {
					if ( ! empty( $this->args['separator_color'] ) ) {
						$html .= '<style>#' . $id . ' .avada-views-addon-decoration::before, #' . $id . ' .avada-views-addon-decoration::after{border-color:' . $this->args['separator_color'] . ';</style>';
					}
				} elseif ( false !== strpos( $this->args['style_type'], 'underline' ) ) {
					$styles              = explode( ' ', $this->args['style_type'] );
					$border_bottom_color = $this->args['separator_color'];

					if ( isset( $styles[1] ) && in_array( $styles[1], array( 'dashed', 'dotted', 'solid' ), true ) ) {
						$html .= '<style>#' . $id . '.avada-views-addon-wrapper{border-bottom: 1px ' . $styles[1] . $border_bottom_color . ';}</style>';
					}
				}

				$html .= '<div ' . FusionBuilder::attributes( 'views-addon-wrapper', array( 'id' => $id ) ) . '>';
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
			 * @param array $attr The initial attributes.
			 * @return array
			 */
			public function attr( $attr ) {
				$attr = fusion_builder_visibility_atts(
					$this->args['hide_on_mobile'],
					array(
						'id'    => $attr['id'],
						'class' => 'avada-views-addon-wrapper',
						'style' => '',
					)
				);

				if ( $this->args['color'] ) {
					$attr['style'] .= 'color:' . $this->args['color'] . ';';
				}

				if ( $this->args['background'] ) {
					$attr['style'] .= 'background-color:' . $this->args['background'] . ';';
				}

				if ( $this->args['content_align'] && 'auto' !== $this->args['content_align'] ) {
					$attr['style'] .= 'text-align:' . $this->args['content_align'] . ';';
				}

				if ( $this->args['font_size'] && 'auto' !== $this->args['font_size'] ) {
					$attr['style'] .= 'font-size:' . $this->args['font_size'] . ';';
				}

				if ( $this->args['padding_top'] ) {
					$attr['style'] .= 'padding-top:' . $this->args['padding_top'] . ';';
				}

				if ( $this->args['padding_bottom'] ) {
					$attr['style'] .= 'padding-bottom:' . $this->args['padding_bottom'] . ';';
				}

				if ( $this->args['padding_left'] ) {
					$attr['style'] .= 'padding-left:' . $this->args['padding_left'] . ';';
				}

				if ( $this->args['padding_right'] ) {
					$attr['style'] .= 'padding-right:' . $this->args['padding_right'] . ';';
				}

				if ( $this->args['margin_top'] ) {
					$attr['style'] .= 'margin-top:' . $this->args['margin_top'] . ';';
				}

				if ( $this->args['margin_bottom'] ) {
					$attr['style'] .= 'margin-bottom:' . $this->args['margin_bottom'] . ';';
				}

				if ( $this->args['margin_left'] ) {
					$attr['style'] .= 'margin-left:' . $this->args['margin_left'] . ';';
				}

				if ( $this->args['margin_right'] ) {
					$attr['style'] .= 'margin-right:' . $this->args['margin_right'] . ';';
				}

				if ( $this->args['class'] ) {
					$attr['class'] .= ' ' . $this->args['class'];
				}

				return $attr;
			}

			/**
			 * Builds the attributes for the separator wrapper.
			 *
			 * @return array
			 */
			public function separator_wrapper_attr() {
				$attr = array();

				if ( false !== strpos( $this->args['style_type'], 'double' ) || false !== strpos( $this->args['style_type'], 'single' ) ) {
					$styles = explode( ' ', $this->args['style_type'] );
					$styles = implode( '-', $styles );

					$class_name     = 'avada-views-addon-decoration avada-views-addon-decoration--' . $styles;
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
				$attr = array();

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
				return array(
					'fusion_views_addon_shortcode_section' => array(
						'label'       => esc_attr__( 'Views Counter', 'fusion-views-addon' ),
						'description' => '',
						'id'          => 'fusion_views_addon_shortcode_section',
						'default'     => '',
						'icon'        => 'fusiona-eye',
						'type'        => 'accordion',
						'fields'      => array(
							'fusion_addon_views_content' => array(
								'label'       => esc_attr__( 'Content', 'fusion-views-addon' ),
								'description' => esc_attr__( 'Text to display among the views. Use %total_views% or %today_views% to show the total/daily views.', 'fusion-views-addon' ),
								'id'          => 'fusion_addon_views_content',
								'default'     => esc_attr__( 'Total views: %total_views%. Daily views: %today_views%', 'fusion-views-addon' ),
								'type'        => 'text',
								'transport'   => 'postMessage',
							),
							'fusion_addon_views_color'   => array(
								'label'       => esc_attr__( 'Text Color', 'fusion-views-addon' ),
								'description' => esc_attr__( 'Set the global text color.', 'fusion-views-addon' ),
								'id'          => 'fusion_addon_views_color',
								'default'     => '',
								'type'        => 'color-alpha',
								'transport'   => 'postMessage',
							),
							'fusion_addon_views_background_color' => array(
								'label'       => esc_attr__( 'Background Color', 'fusion-views-addon' ),
								'description' => esc_attr__( 'Set the global background color.', 'fusion-views-addon' ),
								'id'          => 'fusion_addon_views_background_color',
								'default'     => '',
								'type'        => 'color-alpha',
								'transport'   => 'postMessage',
							),
						),
					),
				);
			}

			/**
			 * Load element base CSS.
			 *
			 * @access public
			 * @since 3.0
			 * @return void
			 */
			public function add_css_files() {
				FusionBuilder()->add_element_css( FUSION_VIEWS_ADDON_PLUGIN_DIR . 'css/my-elements.css' );
			}
		}
	}

	new Fusion_Views_Addon();
}

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_views_addon_map() {

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
			'Fusion_Views_Addon',
			array(
				'name'                              => esc_attr__( 'Views Counter', 'fusion-views-addon' ),
				'shortcode'                         => 'fusion_views_addon',
				'icon'                              => 'fusiona-eye',

				// View used on front-end.
				'front_end_custom_settings_view_js' => FUSION_VIEWS_ADDON_PLUGIN_URL . 'elements/front-end/views-counter.js',

				// Template that is used on front-end.
				'front-end'                         => FUSION_VIEWS_ADDON_PLUGIN_DIR . '/elements/front-end/views-counter.php',

				'allow_generator'                   => false,

				// Allows inline editor.
				'inline_editor'                     => true,
				'inline_editor_shortcodes'          => true,

				'params'                            => array(
					array(
						'type'         => 'tinymce',
						'heading'      => esc_attr__( 'Content', 'fusion-views-addon' ),
						'description'  => esc_attr__( 'Enter the text to display among the views. Use %total_views% or %today_views% to show the total/daily views.', 'fusion-views-addon' ),
						'param_name'   => 'element_content',
						'dynamic_data' => true,
						'value'        => $fusion_settings->get( 'fusion_addon_views_content' ) ? $fusion_settings->get( 'fusion_addon_views_content' ) : esc_attr__( 'Total views: %total_views%. Daily views: %today_views%', 'fusion-views-addon' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => esc_attr__( 'Font Size', 'fusion-builder' ),
						/* translators: URL for the link. */
						'description' => sprintf( esc_html__( 'Controls the font size of the text. Enter value including any valid CSS unit, ex: 20px. Leave empty if the global font size for the corresponding heading size (h1-h6) should be used: %s.', 'fusion-builder' ), $to_link ),
						'param_name'  => 'font_size',
						'value'       => '',
						'group'       => esc_attr__( 'Design', 'fusion-builder' ),
					),
					array(
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Text Color', 'fusion-views-addon' ),
						'description' => esc_attr__( 'Set the text color.', 'fusion-views-addon' ),
						'param_name'  => 'color',
						'default'     => $fusion_settings->get( 'fusion_addon_views_color' ) ? $fusion_settings->get( 'fusion_addon_views_color' ) : '',
						'group'       => esc_attr__( 'Design', 'fusion-views-addon' ),
					),
					array(
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Background Color', 'fusion-views-addon' ),
						'description' => esc_attr__( 'Set the background color.', 'fusion-views-addon' ),
						'param_name'  => 'background',
						'default'     => $fusion_settings->get( 'fusion_addon_views_background_color' ) ? $fusion_settings->get( 'fusion_addon_views_background_color' ) : '',
						'group'       => esc_attr__( 'Design', 'fusion-views-addon' ),
					),
					array(
						'type'        => 'radio_button_set',
						'heading'     => esc_attr__( 'Alignment', 'fusion-views-addon' ),
						'description' => esc_attr__( 'Choose to align the heading left, right or center.', 'fusion-views-addon' ),
						'param_name'  => 'content_align',
						'value'       => array(
							'auto'   => esc_attr__( 'Language Default', 'fusion-views-addon' ),
							'left'   => esc_attr__( 'Left', 'fusion-views-addon' ),
							'center' => esc_attr__( 'Center', 'fusion-views-addon' ),
							'right'  => esc_attr__( 'Right', 'fusion-views-addon' ),
						),
						'default'     => 'auto',
						'group'       => esc_attr__( 'Design', 'fusion-views-addon' ),
					),
					array(
						'type'             => 'dimension',
						'remove_from_atts' => true,
						'heading'          => esc_attr__( 'Padding', 'fusion-views-addon' ),
						'description'      => esc_attr__( 'In pixels or percentage, ex: 10px or 10%.', 'fusion-views-addon' ),
						'param_name'       => 'padding',
						'value'            => array(
							'padding_top'    => '',
							'padding_right'  => '',
							'padding_bottom' => '',
							'padding_left'   => '',
						),
						'group'            => esc_attr__( 'Design', 'fusion-views-addon' ),
					),
					'fusion_margin_placeholder' => array(
						'param_name' => 'margin',
						'value'      => array(
							'margin_top'    => '',
							'margin_right'  => '',
							'margin_bottom' => '',
							'margin_left'   => '',
						),
					),
					array(
						'type'        => 'select',
						'heading'     => esc_attr__( 'Separator', 'fusion-views-addon' ),
						'description' => esc_attr__( 'Choose the kind of the title separator you want to use.', 'fusion-views-addon' ),
						'param_name'  => 'style_type',
						'value'       => array(
							'none'             => esc_attr__( 'None', 'fusion-views-addon' ),
							'single solid'     => esc_attr__( 'Single Solid', 'fusion-views-addon' ),
							'single dashed'    => esc_attr__( 'Single Dashed', 'fusion-views-addon' ),
							'single dotted'    => esc_attr__( 'Single Dotted', 'fusion-views-addon' ),
							'double solid'     => esc_attr__( 'Double Solid', 'fusion-views-addon' ),
							'double dashed'    => esc_attr__( 'Double Dashed', 'fusion-views-addon' ),
							'double dotted'    => esc_attr__( 'Double Dotted', 'fusion-views-addon' ),
							'underline solid'  => esc_attr__( 'Underline Solid', 'fusion-views-addon' ),
							'underline dashed' => esc_attr__( 'Underline Dashed', 'fusion-views-addon' ),
							'underline dotted' => esc_attr__( 'Underline Dotted', 'fusion-views-addon' ),
						),
						'default'     => 'none',
						'group'       => esc_attr__( 'Design', 'fusion-views-addon' ),
					),
					array(
						'type'        => 'colorpickeralpha',
						'heading'     => esc_attr__( 'Separator Color', 'fusion-views-addon' ),
						'description' => esc_attr__( 'Defaults to text color.', 'fusion-views-addon' ),
						'param_name'  => 'separator_color',
						'default'     => '',
						'group'       => esc_attr__( 'Design', 'fusion-views-addon' ),
					),
					array(
						'type'        => 'checkbox_button_set',
						'heading'     => esc_attr__( 'Element Visibility', 'fusion-views-addon' ),
						'param_name'  => 'hide_on_mobile',
						'value'       => fusion_builder_visibility_options( 'full' ),
						'default'     => fusion_builder_default_visibility( 'array' ),
						'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-views-addon' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => esc_attr__( 'CSS Class', 'fusion-views-addon' ),
						'param_name'  => 'class',
						'value'       => '',
						'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-views-addon' ),
					),
				),
			)
		)
	);
}
add_action( 'fusion_builder_before_init', 'fusion_views_addon_map' );
