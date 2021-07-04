<?php

namespace Fusion_Views_Addon;

use Fusion_Dynamic_Data_Callbacks;

use RuntimeException;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

#region -- Add Total/Daily views to fusion dynamic data.

add_action( 'fusion_set_dynamic_params', 'Fusion_Views_Addon\\add_total_views_count_to_fusion_builder_dynamic_data' );
add_action( 'fusion_set_dynamic_params', 'Fusion_Views_Addon\\add_today_views_count_to_fusion_builder_dynamic_data' );

/**
 * Filter function. Add the total views of a post to fusion dynamic data selector.
 *
 * @param array $dynamic_data Previous dynamic data.
 * @return array
 */
function add_total_views_count_to_fusion_builder_dynamic_data( $dynamic_data ) {
	if ( ! is_array( $dynamic_data ) ) {
		return $dynamic_data;
	}

	// Try to find best group to fit in.
	$group = esc_attr__( 'Other', 'fusion-builder' );
	if ( isset( $dynamic_data['post_title']['group'] ) ) {
		$group = $dynamic_data['post_title']['group'];
	}

	$dynamic_data['fusion_total_views_addon'] = array(
		'label'    => esc_html__( 'Total post views', 'fusion-builder' ),
		'id'       => 'fusion_total_views_addon',
		'group'    => $group,
		'callback' => array(
			'function' => 'Fusion_Views_Addon\\get_fusion_dynamic_data_total_post_views',
			'ajax'     => false,
		),
	);

	return $dynamic_data;
}

/**
 * Filter function. Add the daily views of a post to fusion dynamic data selector.
 *
 * @param array $dynamic_data Previous dynamic data.
 * @return array
 */
function add_today_views_count_to_fusion_builder_dynamic_data( $dynamic_data ) {
	if ( ! is_array( $dynamic_data ) ) {
		return $dynamic_data;
	}

	// Try to find best group to fit in.
	$group = esc_attr__( 'Other', 'fusion-builder' );
	if ( isset( $dynamic_data['post_title']['group'] ) ) {
		$group = $dynamic_data['post_title']['group'];
	}

	$dynamic_data['fusion_today_views_addon'] = array(
		'label'    => esc_html__( 'Today post views', 'fusion-builder' ),
		'id'       => 'fusion_today_views_addon',
		'group'    => $group,
		'callback' => array(
			'function' => 'Fusion_Views_Addon\\get_fusion_dynamic_data_today_post_views',
			'ajax'     => false,
		),
	);

	return $dynamic_data;
}

/**
 * Retrieve the total views of a post to fusion dynamic data.
 *
 * @return string
 */
function get_fusion_dynamic_data_total_post_views() {
	$views_class = get_fusion_dynamic_data_post_views_obj();
	if ( false === $views_class ) {
		return '';
	}

	$views_formatted = $views_class->get_total_views_format_i18n();
	$views_num       = $views_class->get_total_views_num();

	/* translators: %s - will be replaced with the number of views. */
	$views_text = sprintf( _n( '%s Visitor', '%s Total Visitors', $views_num, 'fusion-views-addon' ), $views_formatted );

	return $views_text;
}

/**
 * Retrieve the total views number of a post to fusion dynamic data.
 *
 * @return string
 */
function get_fusion_dynamic_data_total_post_views_num() {
	$views_class = get_fusion_dynamic_data_post_views_obj();
	if ( false === $views_class ) {
		return '';
	}

	$views_num = $views_class->get_total_views_num();

	return $views_num;
}

/**
 * Retrieve the today views of a post to fusion dynamic data.
 *
 * @return string
 */
function get_fusion_dynamic_data_today_post_views() {
	$views_class = get_fusion_dynamic_data_post_views_obj();
	if ( false === $views_class ) {
		return '';
	}

	$views_formatted = $views_class->get_today_views_format_i18n();
	$views_num       = $views_class->get_today_views_num();

	/* translators: %s - will be replaced with the number of views. */
	$views_text = sprintf( _n( '%s Today Visitor', '%s Today Visitors', $views_num, 'fusion-views-addon' ), $views_formatted );

	return $views_text;
}

/**
 * Retrieve the today views of a post to fusion dynamic data.
 *
 * @return string
 */
function get_fusion_dynamic_data_today_post_views_num() {
	$views_class = get_fusion_dynamic_data_post_views_obj();
	if ( false === $views_class ) {
		return '';
	}

	$views_num = $views_class->get_today_views_num();

	return $views_num;
}

/**
 * Retrieve the today views of a post to fusion dynamic data.
 *
 * @return false|Views_Counter
 */
function get_fusion_dynamic_data_post_views_obj() {
	if ( ! is_callable( Fusion_Dynamic_Data_Callbacks::class, 'get_post_id' ) ) {
		return false;
	}

	$post_id = Fusion_Dynamic_Data_Callbacks::get_post_id();

	try {
		$views_class = new Views_Counter( $post_id );
	} catch ( RuntimeException $e ) {
		return false;
	}

	return $views_class;
}

#endregion -- Add to fusion dynamic data.
