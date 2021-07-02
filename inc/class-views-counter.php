<?php

namespace Fusion_Views_Addon;

use RuntimeException;

class Views_Counter {

	/**
	 * The post id used set/retrieve views.
	 *
	 * @var int
	 */
	protected $post_id;

	const TOTAL_VIEWS__META_NAME = 'fusion_addon_total_views';
	const TODAY_VIEWS__META_NAME = 'fusion_addon_today_views';
	const TODAY_DATE__META_NAME  = 'fusion_addon_today_date_views';

	/**
	 * Construct the function
	 *
	 * @throws RuntimeException If the post ID is not valid type.
	 * @param int|string $post_id The post id to set/retrieve views.
	 */
	public function __construct( $post_id ) {
		if ( ! is_numeric( $post_id ) || ! ( $post_id > 0 ) ) {
			throw new RuntimeException( 'Post id is not valid.' );
		}

		$this->post_id = (int) $post_id;
	}

	/**
	 * Get a string, representing the total views, formatting to be displayed on
	 * frontend.
	 *
	 * @return int
	 */
	public function get_total_views_format_i18n() {
		return number_format_i18n( $this->get_total_views_num() );
	}

	/**
	 * Get a string, representing the daily/today views, formatting to be
	 * displayed on frontend.
	 *
	 * @return int
	 */
	public function get_today_views_format_i18n() {
		return number_format_i18n( $this->get_today_views_num() );
	}

	/**
	 * Get an int, representing the today views.
	 *
	 * @return int
	 */
	public function get_total_views_num() {
		$total_views = get_post_meta( $this->post_id, self::TOTAL_VIEWS__META_NAME, true );
		if ( ! is_numeric( $total_views ) ) {
			$total_views = 0;
		}

		return (int) $total_views;
	}

	/**
	 * Get an int, representing the daily/today views.
	 *
	 * @return int
	 */
	public function get_today_views_num() {
		if ( ! $this->are_views_stored_from_today() ) {
			return 0;
		}

		$today_views = get_post_meta( $this->post_id, self::TODAY_VIEWS__META_NAME, true );
		if ( ! is_numeric( $today_views ) ) {
			$today_views = 0;
		}

		return (int) $today_views;
	}

	/**
	 * Increases the post views by 1, when a page loads.
	 *
	 * Function should be called on a WP action, like 'wp'.
	 *
	 * @return void
	 */
	public static function increase_post_views_on_page_load() {
		$is_builder = ( function_exists( 'fusion_is_preview_frame' ) && fusion_is_preview_frame() ) || ( function_exists( 'fusion_is_builder_frame' ) && fusion_is_builder_frame() );
		if ( ! is_singular() || is_admin() || is_preview() || $is_builder ) {
			return;
		}

		global $post;
		try {
			$views_counter = new Views_Counter( $post->ID );
		} catch ( RuntimeException $e ) {
			return;
		}

		$views_counter->increase_post_views();
	}

	/**
	 * Increase the post views by 1.
	 *
	 * @return void
	 */
	public function increase_post_views() {
		$this->increase_total_views();
		$this->increase_today_views();
	}

	/**
	 * Increase the total views by 1.
	 *
	 * @return void
	 */
	protected function increase_total_views() {
		$total_views = $this->get_total_views_num();
		$total_views++;
		update_post_meta( $this->post_id, self::TOTAL_VIEWS__META_NAME, $total_views );
	}

	/**
	 * Increase the daily/today views by 1.
	 *
	 * @return void
	 */
	protected function increase_today_views() {
		$today_views = $this->get_today_views_num();
		$today_views++;
		update_post_meta( $this->post_id, self::TODAY_VIEWS__META_NAME, $today_views );

		if ( ! $this->are_views_stored_from_today() ) {
            // phpcs:ignore WordPress.DateTime -- Date should depend on timezone.
			update_post_meta( $this->post_id, self::TODAY_DATE__META_NAME, date( 'd-m-Y' ) );
		}
	}

	/**
	 * Check if the daily views date, is today date.
	 *
	 * @return bool
	 */
	public function are_views_stored_from_today() {
		$post_meta_today = get_post_meta( $this->post_id, self::TODAY_DATE__META_NAME, true );
		$today           = date( 'd-m-Y' ); // phpcs:ignore WordPress.DateTime -- Date should depend on timezone.

		if ( $today === $post_meta_today ) {
			return true;
		}

		return false;
	}

}
