<?php
/**
 * Wontrapi Functions
 *
 * Note:
 * $user_id 	= A WordPress User's ID
 * $contact_id 	= An Ontraport Contact's ID (https://app.ontraport.com/#!/contact/edit&id=1234567)
 * $opuid 		= The $contact_id stored in the WordPress database (OntraPort User ID)
 * 
 * @since 0.1.1
 * @package Wontrapi
 */
/**
 * Wrapper to get wontrapi_options from database
 * @since  0.1.1
 * @param  string $key [description]
 * @return mixed      [description]
 */

function wontrapi_get_option( $key = '' ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		return cmb2_get_option( 'wontrapi_options', $key );
	} else {
		$options = get_option( 'wontrapi_options' );
		return $options[$key];
	}
}

function wontrapi_get_opuid( $user_id ) {
	return get_user_meta( $user_id, 'won_cid', true );
}

function wontrapi_update_opuid( $user_id, $opuid ) {
	return update_user_meta( $user_id, 'won_cid', $opuid );
}

function wontrapi_get_website_subscriber_id( $user_id ) {
	return get_user_meta( $user_id, 'won_wsid', true );
}

function wontrapi_update_website_subscriber_id( $user_id, $ws_id ) {
	return update_user_meta( $user_id, 'won_wsid', $ws_id );
}

function wontrapi_op__get_contact_by_contact_id( $contact_id ) {
	return WontrapiGo::get_contact( $contact_id );
}

function wontrapi_op__get_contact_by_wp_email( $email ) {
	return WontrapiGo::get_contact_by_email( $email );
}

function wontrapi_op__get_contact_id_by_wp_email( $email ) {
	$contact = WontrapiGo::get_contact_by_email( $email );
	return WontrapiHelp::get_id_from_response( $contact );
}

function wontrapi_get_contact_id_by_user_id( $user_id, $create = false ) {

	// first, check wp database
	$opuid = wontrapi_get_opuid( $user_id );

	if ( $opuid ) {
		return $opuid;
	} else {
		$user = get_user_by( 'ID', $user_id );
		$opuid = wontrapi_op__get_contact_id_by_wp_email( $user->user_email );
	}
	if ( $opuid ) {
		wontrapi_update_opuid( $user_id, $opuid );
		return $opuid;
	} elseif ( $create ) {
		$opuid = wontrapi_op__add_or_update_contact( $user_id, $user->user_email );
		wontrapi_update_opuid( $user_id, $opuid );
	}

	return $opuid;

//	return WontrapiGo::get_contact( $opuid );
}

function wontrapi_op__add_or_update_contact( $user_id, $email, $args = array() ) {
	$response = WontrapiGo::create_or_update_contact( $email, $args );
	$opuid = WontrapiHelp::get_id_from_response( $response );
	wontrapi_update_opuid( $user_id, $opuid );
	return $opuid;
}

function wontrapi_op__tag_contact( $user_id, $tag_ids, $args = array() ) {
	$contact_id = wontrapi_get_contact_id_by_user_id( $user_id );
	return WontrapiGo::add_tag_to_contact( $contact_id, $tag_ids, $args );
}

function wontrapi_op__untag_contact( $user_id, $tag_ids, $args = array() ) {
	$contact_id = wontrapi_get_contact_id_by_user_id( $user_id );
	return WontrapiGo::remove_tag_from_contact( $contact_id, $tag_ids, $args );
}

function wontrapi_listen() {
	$p = $_POST;
	$data = get_option( 'wontrapi_options' );
	$ping_key = $data['ping_key'];
	$ping_val = $data['ping_value'];
	if ( isset( $p["$ping_key"] ) ) {
		if ( $p["$ping_key"] == $ping_val ) {
			if ( isset( $p['wontrapi_action'] ) ) {
				do_action( "wontrapi_post_action_{$p['wontrapi_action']}", $p );
			} else {
				do_action( 'wontrapi_post_action', $p );
			}
		}
	}
}
add_action( 'init', 'wontrapi_listen' );

function wontrapi_user(){
	$p = 'won_';
	$cmb = new_cmb2_box( array(
		'id'               => $p . 'edit',
		'title'            => __( 'User Profile Metabox', 'wontrapi' ), // Doesn't output for user boxes
		'object_types'     => array( 'user' ), // Tells CMB2 to use user_meta vs post_meta
		'show_names'       => true,
		'new_user_section' => 'add-new-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
	) );

	$cmb->add_field( array(
		'name'    => 'Ontraport Contact ID',
		'id'      => $p . 'cid',
		'type'    => 'text',
	) );
/*
	$cmb->add_field( array(
		'name'    => 'Website Subscriber ID',
		'id'      => $p . 'wsid',
		'type'    => 'text',
	) );
*/
}
add_action( 'cmb2_admin_init', 'wontrapi_user' );
