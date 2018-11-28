<?php 

function wontrapi_tracking_script() {
	$data = get_option( 'wontrapi_options' );
	if ( !empty( $data['tracking'] ) )
		echo $data['tracking'];

}
add_action( 'wp_footer', 'wontrapi_tracking_script' );
