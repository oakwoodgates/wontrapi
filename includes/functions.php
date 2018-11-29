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


/**
 * Helpers
 */

/**
 * Get single option from options page
 * @param  string $key [description]
 * @return [type]      [description]
 */
function wontrapi_get_option( $key = '' ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		return cmb2_get_option( 'wontrapi_options', $key );
	} else {
		$options = get_option( 'wontrapi_options' );
		return $options[$key];
	}
}

function wontrapi_get_data( $response, $all = false ) {
	return WontrapiHelp::get_data_from_response( $response, $all );
}

///////////////////////////////////////////////////////////////////////////////



/**
 * CONTACT_ID and the DATABASE
 */


/**
 * Get a user's Contact_ID from OP (stored in user_meta)
 */
function wontrapi_get_opuid( $user_id = 0 ) {
	return get_user_meta( $user_id, 'won_cid', true );
}

/**
 * Store user's OP Contact_ID in meta
 */
function wontrapi_update_opuid( $user_id = 0, $opuid = 0 ) {
	return update_user_meta( $user_id, 'won_cid', $opuid );
}

/**
 * Delete a user's OP Contact_ID from user_meta
 */
function wontrapi_delete_opuid( $user_id = 0 ) {
	return delete_user_meta( $user_id, 'won_cid' );
}

function wontrapi_get_website_subscriber_id( $user_id ) {
	return get_user_meta( $user_id, 'won_wsid', true );
}

function wontrapi_update_website_subscriber_id( $user_id, $ws_id ) {
	return update_user_meta( $user_id, 'won_wsid', $ws_id );
}


///////////////////////////////////////////////////////////////////////////////



/**
 * GET CONTACTS
 */


/**
 * Get a contact from OP
 * 
 * @param  string $email A valid email address
 * @return str|false     JSON string or false (bool)
 */
function wontrapi_get_contact_by_contact_id( $contact_id = 0 ) {
	$contact = WontrapiGo::get_contact( $contact_id );
	return ( WontrapiHelp::get_id_from_response( $contact ) ) ? $contact : false;
}

/**
 * Get a contact from OP by email
 *
 * If successful, will return array of contacts within the data object
 * of the json response.
 * 
 * @param  string    $email A valid email address
 * @return str|false        JSON string or false 
 */
function wontrapi_get_contacts_by_email( $email = '' ) {
	$contacts = WontrapiGo::get_contacts_by_email( $email );
	return ( WontrapiHelp::get_id_from_response( $contacts ) ) ? $contacts : false;
}

/**
 * Get contacts from OP by userID in WP
 */
function wontrapi_get_contacts_by_user_id( $user_id = 0 ) {
	$contact = 0;
	$contact_id = wontrapi_get_opuid( $user_id );

	// try to get by contact_id
	if ( $contact_id ) {
		$contact = wontrapi_get_contact_by_contact_id( $contact_id );
	//	$contact = WontrapiGo::get_contact( $contact_id );
		// check that our local contact_id's user wasn't deleted or merged in OP
		if ( WontrapiHelp::get_id_from_response( $contact ) ) {
			return $contact;
		} 
		$contact = 0; // reset
		wontrapi_delete_opuid( $user_id ); // false contact_id
	}

	// try to get by email
	$user = get_user_by( 'id', $user_id );
	if ( $user ) {
		$contact = wontrapi_get_contacts_by_email( $user->user_email ); 
	//	$contact = WontrapiGo::get_contacts_by_email( $email );
		$ids = WontrapiHelp::get_ids_from_response( $contact );
		// do we have a contact?
		if ( $ids ) {
			// don't update contact_id in database if multiple contacts found in OP
			if ( ! isset( $ids[1] ) ) {
				wontrapi_update_opuid( $user_id, $ids[0] );
			}
			return $contact;
		}
		$contact = 0; // reset
	}

	return $contact;
}


///////////////////////////////////////////////////////////////////////////////

/**
 * ADD CONTACTS
 */

function wontrapi_add_or_update_contact( $email = '', $args = array() ) {

	$response = WontrapiGo::create_or_update_contact( $email, $args );

	$contact_id = WontrapiHelp::get_id_from_response( $response );
	$user_id = email_exists( $email );

	if ( $contact_id && $user_id ) {
		wontrapi_update_opuid( $user_id, $contact_id );
	}

	return $response;
}


///////////////////////////////////////////////////////////////////////////////



/**
 * TAG CONTACTS and USERS
 */


/**
 * [wontrapi_add_tag_to_contact description]
 * @param  int|str|arr 	$contact_id [description]
 * @param  int|str|arr 	$tag_id     [description]
 * @param  array  		$args       [description]
 * @return [type]             [description]
 */
function wontrapi_add_tag_to_contact( $contact_id, $tag_ids ) {
	return WontrapiGo::tag( $contact_id, $tag_ids );
}

function wontrapi_remove_tag_from_contact( $contact_id, $tag_ids ) {
	return WontrapiGo::untag( $contact_id, $tag_ids );
}

function wontrapi_add_tag_to_user( $user_id, $tag_ids ) {
	$contact = wontrapi_get_contacts_by_user_id( $user_id ); 
	$contact_id = WontrapiHelp::get_id_from_response( $contact );
	if ( $contact_id ) {
		return wontrapi_add_tag_to_contact( $contact_id, $tag_ids );
	} 
	return 0;
}

function wontrapi_remove_tag_from_user( $user_id, $tag_ids ) {
	$contact = wontrapi_get_contacts_by_user_id( $user_id ); 
	$contact_id = WontrapiHelp::get_id_from_response( $contact );
	if ( $contact_id ) {
		return wontrapi_remove_tag_from_contact( $contact_id, $tag_ids );
	} 
	return 0;
}



///////////////////////////////////////////////////////////////////////////////
