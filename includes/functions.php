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


///////////////////////////////////////////////////////////////////////////////


/**
 * HELPERS
 */


function wontrapi_get_options() {
	return Wontrapi_Core::get_options();
}

/**
 * Get single option from options page
 * @param  string $key [description]
 * @return [type]      [description]
 */
function wontrapi_get_option( $key = '' ) {
	return Wontrapi_Core::get_option( $key );
}


function wontrapi_get_data( $response, $all = false ) {
	return WontrapiHelp::get_data_from_response( $response, $all );
}

function wontrapi_get_id( $response ) {
	return WontrapiHelp::get_id_from_response( $response );
}

///////////////////////////////////////////////////////////////////////////////


/**
 * CONTACT and the DATABASE
 */


/**
 * Get a user's Contact_ID from OP (stored in user_meta)
 */
function wontrapi_get_opuid( $user_id = 0 ) {
	return Wontrapi_Core::get_user_contact_id_from_meta( $user_id );
}

/**
 * Store user's OP Contact_ID in meta
 */
function wontrapi_update_opuid( $user_id = 0, $contact_id = 0 ) {
	return Wontrapi_Core::update_user_contact_id_meta( $user_id, $contact_id );
}

/**
 * Delete a user's OP Contact_ID from user_meta
 */
function wontrapi_delete_opuid( $user_id = 0 ) {
	return Wontrapi_Core::delete_user_contact_id_meta( $user_id );
}

function wontrapi_get_user_transient( $user_id = 0 ) {
	return Wontrapi_Core::get_user_transient( $user_id );
}

function wontrapi_set_user_transient( $user_id = 0, $contact_data ) {
	return Wontrapi_Core::set_user_transient( $user_id, $contact_data );
}

function wontrapi_delete_user_transient( $user_id = 0 ) {
	return Wontrapi_Core::delete_user_transient( $user_id );
}

///////////////////////////////////////////////////////////////////////////////


/**
 * GET CONTACTS
 */

/**
 * Get user's contact ID
 */
function wontrapi_get_user_contact_id( $user_id = 0 ) {
	$contact_id = wontrapi_get_opuid( $user_id );

	if ( ! $contact_id ) {
		$contact = wontrapi_get_contact_by_user_id( $user_id ); 
		$contact_id = wontrapi_get_id( $contact );
	}
	return $contact_id;
}


/**
 * Get a contact from OP by userID in WP
 */
function wontrapi_get_contact_by_user_id( $user_id = 0 ) {
	return Wontrapi_Core::get_contact_by_user_id( $user_id );
}


/**
 * Get a contact from OP
 * 
 * @param  string $email A valid email address
 * @return arr|false     Array or false (bool)
 */
function wontrapi_get_contact_by_contact_id( $contact_id = 0 ) {
	return Wontrapi_Core::get_contact_by_contact_id( $contact_id );
}


/**
 * Get a contact from OP by email
 *
 * If successful, will return single contact 
 * 
 * @param  string    $email A valid email address
 * @return arr|false        Array or false 
 */
function wontrapi_get_contact_by_email( $email = '' ) {
	return Wontrapi_Core::get_contact_by_email( $email );
}


/**
 * Get all contacts from OP by email
 *
 * If successful, will return array of contacts.
 * 
 * @param  string    $email A valid email address
 * @return arr|false        Array or false 
 */
function wontrapi_get_contacts_by_email( $email = '' ) {
	return Wontrapi_Core::get_contact_by_email( $email, true );
}




///////////////////////////////////////////////////////////////////////////////


/**
 * ADD or UPDATE CONTACTS
 */


function wontrapi_add_or_update_contact( $email = '', $contact_data = [] ) {
	return Wontrapi_Core::add_or_update_contact( $email, $contact_data );
}

///////////////////////////////////////////////////////////////////////////////


/**
 * ADD/REMOVE TAGS, SEQUENCES, CAMPAIGNS to/from USERS
 */

function wontrapi_add_user_to( $user_id = 0, $ids = [], $item = 'tag' ) {

	$contact_id = wontrapi_get_user_contact_id( $user_id );

	if ( $contact_id ) {
		switch ( $item ) {
			case 'tag':
				return wontrapi_add_tag_to_contact( $contact_id, $ids );
			case 'sequence':
				return wontrapi_add_contact_to_sequence( $contact_id, $ids );
			case 'campaign':
				return wontrapi_add_contact_to_campaign( $contact_id, $ids );
		}
	} 
	return 0;
}

function wontrapi_remove_user_from( $user_id = 0, $ids = [], $item = 'tag' ) {

	$contact_id = wontrapi_get_user_contact_id( $user_id );

	if ( $contact_id ) {
		switch ( $item ) {
			case 'tag':
				return wontrapi_remove_tag_from_contact( $contact_id, $ids );
			case 'sequence':
				return wontrapi_remove_contact_from_sequence( $contact_id, $ids );
			case 'campaign':
				return wontrapi_remove_contact_from_sequence( $contact_id, $ids );
		}
	} 
	return 0;
}

/**
 * ADD/REMOVE CONTACTS to/from TAGS
 */
function wontrapi_add_tag_to_contact( $contact_id = 0, $tag_ids = [] ) {
	return WontrapiGo::tag( $contact_id, $tag_ids );
}

function wontrapi_remove_tag_from_contact( $contact_id = 0, $tag_ids = [] ) {
	return WontrapiGo::untag( $contact_id, $tag_ids );
}


/**
 * ADD/REMOVE CONTACTS to/from SEQUENCES
 */

function wontrapi_add_contact_to_sequence( $contact_id, $sequence_ids ) {
	return WontrapiGo::add_to_sequence( $contact_id, $sequence_ids );
}

function wontrapi_remove_contact_from_sequence( $contact_id, $sequence_ids ) {
	return WontrapiGo::remove_from_sequence( $contact_id, $sequence_ids );
}


/**
 * ADD/REMOVE CONTACTS to/from CAMPAIGNS
 */


function wontrapi_add_contact_to_campaign( $contact_id, $campaign_ids ) {
	return WontrapiGo::subscribe( $contact_id, $campaign_ids );
}

function wontrapi_remove_contact_from_campaign( $contact_id, $campaign_ids ) {
	return WontrapiGo::unsubscribe( $contact_id, $campaign_ids );
}

