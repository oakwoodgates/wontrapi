<?php


function wontrapi_click_and_buy( $atts = array() ) {

	$invoice_template = (isset($atts['invoice_template'])?$atts['invoice_template']:1);
	$gateway_id = $atts['gateway_id'];
	$offer = array(

	);

	$success_link = '';
	

	$contact_id = $_COOKIE["contact_id"];

	WontrapiGo::transaction_process( $contact_id, $invoice_template = 1, $gateway_id, $offer );

}
add_shortcode( 'wontrapi_click_and_buy', 'wontrapi_click_and_buy' );
