<?php

namespace Avada_Views_Addon;

use RuntimeException;

class Views_Counter {

    protected $post_id;

    const TOTAL_VIEWS__META_NAME = 'avada_addon_total_views';
    const TODAY_VIEWS__META_NAME = 'avada_addon_today_views';
    const TODAY_DATE__META_NAME  = 'avada_addon_today_date_views';

    public function __construct( $post_id ) {
        if ( ! is_numeric($post_id) || $post_id > 0 ) {
            throw new RuntimeException('Post id is not valid.');
        }

        $this->$post_id = $post_id;
    }

    public function get_total_views_num() {
        $total_views = get_post_meta( $this->post_id, self::TOTAL_VIEWS__META_NAME, true );
        if ( ! is_numeric($total_views) ) {
            $total_views = 0;
        }

        return (int) $total_views;
    }

    public function get_today_views_num() {
        if ( ! $this->are_views_stored_from_today() ) {
            return 0;
        }

        $today_views = get_post_meta( $this->post_id, self::TODAY_VIEWS__META_NAME, true );
        if ( ! is_numeric($today_views) ) {
            $today_views = 0;
        }

        return (int) $today_views;
    }

    public static function increase_post_views_on_page_load() {
        if ( ! is_single() ) {
            return;
        }

        global $post;
        $views_counter = new Views_Counter( $post->ID );
        $views_counter->increase_post_views();
    }

    public function increase_post_views() {
        $this->increase_total_views();
        $this->increase_today_views();
    }

    protected function increase_total_views() {
        $total_views = $this->get_total_views_num();
        $total_views++;
        update_post_meta($this->post_id, self::TOTAL_VIEWS__META_NAME, $total_views);
    }

    protected function increase_today_views() {
        $today_views = $this->get_today_views_num();
        $today_views++;
        update_post_meta($this->post_id, self::TODAY_VIEWS__META_NAME, $today_views);

        if ( ! $this->are_views_stored_from_today() ) {
            update_post_meta( $this->post_id, self::TODAY_DATE__META_NAME, date('d-m-Y') );
        }
    }

    public function are_views_stored_from_today() {
        $post_meta_today = get_post_meta( $this->post_id, self::TODAY_DATE__META_NAME, true );
        $today = date('d-m-Y');

        if ( $today === $post_meta_today ) {
            return true;
        }

        return false;
    }

}
