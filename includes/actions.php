<?php

function wontrapi_listen() {
	$p = $_POST;
	$data = get_option( 'wontrapi_options' );
	if ( empty( $data['ping_key'] ) || empty( $data['ping_value'] ) ) {
		return;
	}
	$ping_key = $data['ping_key'];
	$ping_val = $data['ping_value'];
	if ( isset( $p["$ping_key"] ) ) {
		if ( $p["$ping_key"] == $ping_val ) {
			if ( isset( $p['wontrapi_action'] ) ) {
				// fire a specific action based on the event
				do_action( "wontrapi_post_action_{$p['wontrapi_action']}", $p );
		//	} else {
				// fire a general action for all events
		//		do_action( 'wontrapi_post_action', $p );
			}
		}
	}
}
add_action( 'init', 'wontrapi_listen' );

function wontrapi_tracking_script() {
	$data = get_option( 'wontrapi_options' );
	if ( !empty( $data['tracking'] ) ) {
		echo $data['tracking'];
	}
}
add_action( 'wp_footer', 'wontrapi_tracking_script' );
