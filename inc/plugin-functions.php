<?php

namespace Fusion_Views_Addon;

use Fusion_Dynamic_Data_Callbacks;

use RuntimeException;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

#region -- Add to fusion dynamic data.

add_action( 'fusion_set_dynamic_params', 'Fusion_Views_Addon\\add_views_count_to_fusion_builder_dynamic_data' );

/**
 * Filter function. Add a the views of a post to fusion dynamic data selector.
 *
 * @param array $dynamic_data Previous dynamic data.
 * @return array
 */
function add_views_count_to_fusion_builder_dynamic_data( $dynamic_data ) {
	if ( ! is_array( $dynamic_data ) ) {
		return $dynamic_data;
	}

	// Try to find best group to fit in.
	$group = esc_attr__( 'Other', 'fusion-builder' );
	if ( isset( $dynamic_data['post_title']['group'] ) ) {
		$group = $dynamic_data['post_title']['group'];
	}

	$dynamic_data['fusion_views_addon'] = array(
		'label'    => esc_html__( 'Total post views', 'fusion-builder' ),
		'id'       => 'fusion_views_addon',
		'group'    => $group,
		'callback' => array(
			'function' => 'Fusion_Views_Addon\\get_fusion_dynamic_data_total_post_views',
			'ajax'     => false,
		),
	);

	return $dynamic_data;
}

/**
 * Retrieve the views of a post to fusion dynamic data.
 *
 * @return string
 */
function get_fusion_dynamic_data_total_post_views() {
	if ( ! is_callable( Fusion_Dynamic_Data_Callbacks::class, 'get_post_id' ) ) {
		return '';
	}

	$post_id = Fusion_Dynamic_Data_Callbacks::get_post_id();

	try {
		$views_class = new Views_Counter( $post_id );
	} catch ( RuntimeException $e ) {
		return '';
	}

	$views_formatted = $views_class->get_total_views_format_i18n();

	/* translators: %s - will be replaced with the number of views. */
	$views_text = sprintf( __( '%s Total Visitors', 'fusion-views-addon' ), $views_formatted );

	return $views_text;
}

#endregion -- Add to fusion dynamic data.
