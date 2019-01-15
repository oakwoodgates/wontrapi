<?php
/**
 * Deprecated Wontrapi Functions
 * Some of our older beta functions that clients are still using
 *
 * Note:
 * $user_id 	= A WordPress User's ID
 * $contact_id 	= An Ontraport Contact's ID (https://app.ontraport.com/#!/contact/edit&id=1234567)
 * $opuid 		= The $contact_id stored in the WordPress database (OntraPort User ID)
 *
 * @since 0.4.0
 * @package Wontrapi
 */

function wontrapi_op__get_contact_by_contact_id( $contact_id = 0 ) {
	return wontrapi_get_contact_by_contact_id( $contact_id );
}

function wontrapi_op__get_contact_by_wp_email( $email = '' ) {
	return wontrapi_get_contacts_by_email( $email, true );
}

function wontrapi_get_contact_by_user_id( $user_id, $create = false ) {
	$contacts = wontrapi_get_contacts_by_user_id( $user_id, $create );
	if ( WontrapiHelp::get_id_from_response( $contacts ) ) {
		return WontrapiHelp::get_data_from_response( $contacts, false, false );
	}
	return 0;
}

function wontrapi_op__get_contact_id_by_wp_email( $email = '' ) {
	$contact = wontrapi_get_contacts_by_email( $email );
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

function wontrapi_op__add_or_update_contact1( $email, $args = array() ) {
	$response = WontrapiGo::create_or_update_contact( $email, $args );
	$opuid = WontrapiHelp::get_id_from_response( $response );
	return $opuid;
}

function wontrapi_op__tag_contact( $user_id, $tag_ids, $args = array() ) {
	$contact_id = wontrapi_get_contact_id_by_user_id( $user_id );
	return WontrapiGo::add_tag_to_contact( $contact_id, $tag_ids, $args );
}

function wontrapi_add_tags_to_contacts( $op_id, $tags ) {
	return WontrapiGo::add_tag_to_contact( $op_id, $tags );
}

function wontrapi_op__untag_contact( $user_id, $tag_ids, $args = array() ) {
	$contact_id = wontrapi_get_contact_id_by_user_id( $user_id );
	return WontrapiGo::remove_tag_from_contact( $contact_id, $tag_ids, $args );
}
