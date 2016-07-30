<?php
/**
 * Wontrapi Functions
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

function wontrapi_objects() {
	return wontrapi()->objects;
}

function wontrapi_get_object( $type, $ontraport_id ) {
	return wontrapi()->objects->get_object( $type, $ontraport_id );
}

function wontrapi_get_objects( $type, $ontraport_ids = array(), $params = array() ) {
	return wontrapi()->objects->get_objects( $type, $ontraport_ids, $params );
}
/**
 * Get a list of objects from Ontraport.
 * example: 'Contacts', 'state', '=', 'NY'
 *
 * @param  string $type 	Type of object from Ontraport
 * @param  string $field 	A contact field from Ontraport
 * @param  string $op    	Operator (ex: =,<,>)
 * @param  mixed  $value 	Value of $field to compare
 * @return [type]        [description]
 */
function wontrapi_get_objects_by( $type, $field, $value, $op = '=', $params = array() ) {
	return wontrapi()->objects->get_objects_by_condition( $type, $field, $value, $op, $type = 'auto', $params );
}

function wontrapi_get_contact( $ontraport_id ) {
	return wontrapi_get_object( 'contacts', $ontraport_id );
}

function wontrapi_get_contacts( $ontraport_ids = array() ) {
	return wontrapi_get_objects( 'contacts', $ontraport_ids );
}
/**
 * Get list of contacts from Ontraport by $field $op $value
 * example: 'email', '=', 'user@email.com'
 *
 * @param  string $field 	A contact field from Ontraport
 * @param  string $op    	Operator (ex: =,<,>)
 * @param  mixed  $value 	Value of $field to compare
 * @return [type]        [description]
 */
function wontrapi_get_contacts_by( $field, $value, $op = '=', $params = array() ) {
	return wontrapi()->objects->get_objects_by_condition( 'contacts', $field, $value, $op, $type = 'auto', $params );
}

/**
 * Pass the User's ID from WP. Checks user_meta for 'wontrapi_id' which
 * represents the id of the user/contact object in Ontraport. Otherwise
 * search Ontraport for user's wp email. If found, updates user_meta in WP
 * and returns user object from Ontraport.
 *
 * @param  string 	$user_id ID of user in WordPress
 * @return OBJECT	User object from Ontraport
 */
function wontrapi_get_contact_by_uid( $user_id ) {
	$contact = '';
	if ( ! $user_id ){
		return $contact;
	}

	// check user meta for Ontraport object id
	$op_user_id = get_user_meta( $user_id, 'wontrapi_id', true );

	if ( $op_user_id ) {
		// check that the contact actually exists in Ontraport
		$maybe_contact = wontrapi_get_contact( $op_user_id );
		// double check there is an Ontraport object id
		if ( $maybe_contact->data->id ) {
			// return the contact object from Ontraport
			$contact = $maybe_contact;
			return $contact;
		}
	}

	// if the User meta doesn't exist, get WP User object
	$user = get_user_by( 'ID', $user_id );
	// get WP User's email
	$email = $user->email;
	// find this email in Ontraport
	$search = wontrapi_get_contacts_by( 'email', '=', $email );
	// check for Ontraport object id
	$op_user_id = $search->data[0]->id;

	if ( $op_user_id ) {
		// add Ontraport object id to User meta so it's easier next time
		update_user_meta( $user_id, 'wontrapi_id', $op_user_id );
		// return the contact object from Ontraport
		$contact = wontrapi_get_contact( $op_user_id );
		return $contact;
	}

	return $contact;
}

function wontrapi_update_or_create_object( $obj_type, $email, $params = array() ){
	return wontrapi()->objects->update_or_create_object ( $obj_type, $email, $params );
}

function wontrapi_update_or_create_contact( $email, $params = array() ){
	return wontrapi()->objects->update_or_create_object ( 'contacts', $email, $params );
}
