<?php

/**
 * WontrapiGo
 *
 * Class for accessing the Ontraport API via Ontraport's SDK for PHP.
 * This was created to make the SDK more accessable and add methods
 * for common use cases.
 *
 * @author 		github.com/oakwoodgates 
 * @copyright 	2017 	WPGuru4u
 * @link   		https://api.ontraport.com/doc/ 			OP API Documentation
 * @link   		https://api.ontraport.com/live/ 		OP API Docs
 * @link   		https://github.com/Ontraport/SDK-PHP/ 	Ontraport's SDK for PHP
 * @license 	https://opensource.org/licenses/MIT/ 	MIT
 * @version 	0.5.1 
 */

/**
 * MIT License
 *
 * Copyright (c) 2017 WPGuru4u
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

class WontrapiGo {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  0.1.0
	 */
	const VERSION = '0.5.1';

	/**
	 * Singleton instance of plugin
	 *
	 * @var WontrapiGo
	 * @since  0.1.0
	 */
	protected static $single_instance = null;

	/**
	 * App ID for Ontraport
	 *
	 * @var string
	 * @since  0.1.0
	 */
	public static $id = '';

	/**
	 * App Key for Ontraport
	 *
	 * @var string
	 * @since  0.1.0
	 */
	public static $key = '';

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @param  string $id         App ID for Ontraport
	 * @param  string $key        App Key for Ontraport
	 * @return WontrapiGo         A single instance of this class.
	 * @since  0.1.0	Initial	 
	 */
	public static function init( $id, $key ) {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self( $id, $key );
		}

		return self::$single_instance;
	}

	protected function __construct( $id, $key ) {
		require( 'includes/WontrapiHelp.php' );
		require( 'vendor/Ontraport/SDK-PHP/src/Ontraport.php' );
		self::$id = $id;
		self::$key = $key;
	}

	/**
	 * Set the App ID for Ontraport
	 * 
	 * @param string $id App ID for Ontraport
	 * @since  0.1.0
	 */
	public static function setID( $id ) {
		self::$id = $id;
	}

	/**
	 * Set the App Key for Ontraport
	 * 
	 * @param string $key App Key for Ontraport
	 * @since  0.1.0
	 */
	public static function setKey( $key ) {
		self::$key = $key;
	}

	/**
	 * Connect to Ontraport API
	 * 
	 * @return [type] [description]
	 * @since  0.1.0
	 */
	public static function connect() {
		return new \OntraportAPI\Ontraport( self::$id, self::$key );
	}


	/** 
	 * ************************************************************
	 * Objects 
	 * ************************************************************
	 */

	/**
	 * Create an object
	 * 
	 * This endpoint will add a new object to your database. 
	 * It can be used for any object type as long as the 
	 * correct parameters are supplied. 
	 * 
	 * This endpoint allows duplication. If you want to avoid duplicates,
	 * you should use - WontrapiGo::create_or_update_object()
	 * 
	 * @param  str|int $type Required - Object type (not for Custom Objects). Converts to objectID.
	 * @param  array   $args Parameters depend upon the object. Some may be required.
	 * @return json          Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#create-an-object OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function create_object( $type, $args = array() ) {
		$args['objectID'] = WontrapiHelp::objectID( $type );
		return self::connect()->object()->create( $args );
	}

	/**
	 * Create or merge an object
	 * 
	 * Looks for an existing object with a matching unique field and 
	 * merges supplied data with existing data. If no unique field is 
	 * supplied or if no existing object has a matching unique field, 
	 * a new object will be created.
	 * 
	 * @param  str|int $type Required - Object type (not for Custom Objects). Converts to objectID.
	 * @param  array   $args Parameters depend upon the object. Some may be required.
	 * @return json   		 Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#create-an-object OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function create_or_update_object( $type, $args = array() ) {
		$args['objectID'] = WontrapiHelp::objectID( $type );
		return self::connect()->object()->saveOrUpdate( $args );
	}

	/**
	 * Retrieve a single object
	 * 
	 * Retrieves all the information for an existing object of the specified object type.
	 * 
	 * @param  str|int $type Required - Object type (not for Custom Objects). Converts to objectID.
	 * @param  integer $id   Required - ID of object to get
	 * @return json   		 Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-a-single-object OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial        
	 */
	public static function get_object( $type, $id ) {
		$args = array(
			'objectID' 	=> WontrapiHelp::objectID( $type ),
			'id'		=> $id 
		);
		return self::connect()->object()->retrieveSingle( $args );
	}

	/**
	 * Retrieve multiple objects
	 * 
	 * Retrieves a collection of contacts based on a set of parameters. You can limit 
	 * unnecessary API requests by utilizing criteria and our pagination tools to 
	 * select only the data set you require.
	 * 
	 * @param  str|int $type Required - Object type (not for Custom Objects). Converts to objectID.
	 * @param  array   $args Array of parameters used to search, sort, etc objects
	 * @return json   	     Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-multiple-objects OP API Documentation
	 * @link   https://api.ontraport.com/doc/#criteria OP search critera
	 * @link   https://api.ontraport.com/doc/#pagination Max results returned is 50; may need pagination
	 * @author github.com/oakwoodgates 
	 * @since  0.3.0 Initial
	 */
	public static function get_objects( $type, $args = array() ) {
		$args['objectID'] = WontrapiHelp::objectID( $type );
		return self::connect()->object()->retrieveMultiple( $args );
	}

	/**
	 * Retrieve objects having a tag
	 * 
	 * @param  str|int $type Required - Object type (not for Custom Objects). Converts to objectID.
	 * @param  str|int $tag  Required - Tag ID or name
	 * @param  array  $args  Array of parameters used to search, sort, etc objects
	 * @link   https://api.ontraport.com/doc/#retrieve-objects-having-a-tag OP API Documentation
	 * @return json          Response from OP
	 * @author github.com/oakwoodgates 
	 * @since  0.5.0 Initial      
	 */
	public static function get_objects_tagged( $type, $tag, $args = array() ) {
		$args['objectID'] = WontrapiHelp::objectID( $type );
		if ( is_integer( $tag ) ) {
			$args['tag_id'] = $tag;
		} else {
			$args['tag_name'] = $tag;
		}
		return self::connect()->object()->retrieveAllWithTag( $args );
	}

	/**
	 * Get an object ID by associated email
	 * 
	 * Retrieves the IDs of contact objects or custom objects by their email fields. 
	 * You can retrieve an array of all the IDs of objects with matching emails, 
	 * or you can retrieve the first matching ID.
	 * 
	 * @param  string  $email   Required - Email of object to get
	 * @param  integer $all     0 for the first ID found, 1 for an array of all matching IDs
	 * @param  str|int $type    Object type. Converts to objectID. Default is Contact.
	 * @return integer|array   	ID's from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-a-single-object OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.4.0 Initial        
	 */
	public static function get_object_id_by_email( $email, $all = 0, $type = 0 ) {
		$args = array(
			'objectID' 	=> WontrapiHelp::objectID( $type ),
			'email'		=> $email,
			'all'		=> (int) $all 
		);
		$response = self::connect()->object()->retrieveIdByEmail( $args );
		$response = json_decode( $response, true );
		if ( $all ) {
			if ( isset( $response['data']['ids'] ) ) {
				return $response['data']['ids'];
			}
		} else {
			if ( isset( $response['data']['id'] ) ) {
				return (int) $response['data']['id'];
			}
		}

		return ( $all ) ? 0 : array();
	}

	/**
	 * Retrieve object meta
	 *
	 * Retrieves the field meta data for the specified object.
	 * 
	 * @param  str|int $type   Object type (not for Custom Objects). Converts to objectID.
	 *                         If none is supplied, meta for all objects will be retrieved.
	 * @param  string  $format Indicates whether the list should be indexed by object name or object type ID. 
	 *                         Possible values: 'byId' | 'byName'
	 * @return json            Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-object-meta OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial 
	 */
	public static function get_object_meta( $type = '', $format = 'byId' ) {
		$args = array(
			'objectID' 	=> WontrapiHelp::objectID( $type ),
			'format' => $format
		);
		return self::connect()->object()->retrieveMeta( $args );
	}

	/**
	 * Retrieve object collection info
	 *
	 * Retrieves information about a collection of objects, 
	 * such as the number of objects that match the given criteria.
	 * 
	 * @param  str|int $type Required - Object type (not for Custom Objects). Converts to objectID.
	 * @param  array   $args Optional - Params for search (see docs)
	 * @return json   		 Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-object-collection-info OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function get_object_collection_info( $type, $args = array() ) {
		$args['objectID'] = WontrapiHelp::objectID( $type );
		return self::connect()->object()->retrieveCollectionInfo( $args );
	}

	/**
	 * Update an object’s data
	 *
	 * Updates an existing object with given data. The object type 
	 * and ID of the object to update are required. The other fields 
	 * should only be used if you want to change the existing value.
	 * 
	 * @param  string  $type Required - Object type (not for Custom Objects). Converts to objectID.
	 * @param  integer $id   Required - ID of object to update
	 * @param  array   $args Parameters to update. Parameters depend upon the object.
	 * @return json   		 Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#update-an-object-39-s-data OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function update_object( $type, $id, $args = array() ) {
		$args['id'] = $id;
		$args['objectID'] = WontrapiHelp::objectID( $type );
		return self::connect()->object()->update( $args );
	}

	/**
	 * Delete a single object
	 *
	 * Deletes an existing object of the specified object type.
	 * 
	 * @param  string  $type Required - Object type (not for Custom Objects). Converts to objectID 
	 * @param  integer $id   Required - The ID of the specific object to delete
	 * @return json   		 Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#delete-a-single-object OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function delete_object( $type, $id ) {
		$args = array(
			'objectID' 	=> WontrapiHelp::objectID( $type ),
			'id'		=> $id 
		);
		return self::connect()->object()->deleteSingle( $args );
	}


	/** 
	 * ************************************************************
	 * Fields and Sections 
	 * ************************************************************
	 */

	/**
	 * Create a section and/or add fields to a section.
	 * 
	 * @param  Object  $prepared_section  A prepared section object (see WontrapiHelp::prepare_section() and 
	 *                                    WontrapiHelp::prepare_field())
	 * @param  int|str $type              ObjectID or name of Object Type to create section in (ex: Contacts)
	 * @return json                       Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#create-fields-and-sections-in-an-object-record OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.4.0 Initial
	 */
	public static function create_section( $prepared_section, $type = 0 ) {
		$params = $prepared_section->toRequestParams();
		$params['objectID'] = WontrapiHelp::objectID( $type );
		return self::connect()->object()->createFields( $section );
	}

	/**
	 * Retrieve fields from object meta
	 *
	 * Retrieves a single meta data field for the specified object.
	 *
	 * @param  string  $field  Name of field to retrieve, leave blank for all fields 
	 * @param  str|int $type   Object type (not for Custom Objects). Converts to objectID.
	 * @return json            Array of fields extracted from response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-fields-and-sections-in-an-object-record OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.4.0 Initial 
	 */
	public static function get_field( $field = '', $type = 0 ) {
		$response = self::connect()->object()->retrieveFields(array(
			'objectID' => WontrapiHelp::objectID( $type ),
			'field' => $field
		) );
		return $response;
	}

	/**
	 * Retrieve fields from object meta
	 *
	 * Retrieves a single meta data field for the specified object.
	 *
	 * @param  str     $section  Name of section to retrieve, leave blank for all fields 
	 * @param  str|int $type     Object type (not for Custom Objects). Converts to objectID.
	 * @return json              Array of fields extracted from response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-fields-and-sections-in-an-object-record OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.4.0 Initial 
	 */
	public static function get_section( $section = '', $type = 0 ) {
		$response = self::connect()->object()->retrieveFields(array(
			'objectID' => WontrapiHelp::objectID( $type ),
			'section' => $section
		) );
		return $response;
	}


	/** 
	 * ************************************************************
	 * Add Objects to / Remove Objects from 
	 * Tags, Sequences, and Campaigns
	 * ************************************************************
	 */

	/**
	 * Add an object to a sequence
	 *
	 * Adds one or more objects to one or more sequences.
	 * 
	 * @param  string $ids       Required - An array as a comma-delimited list of the IDs of the objects to be added to sequences.
	 * @param  string $sequences Required - An array as a comma-delimited list of the IDs of the sequence(s) to which objects should be added.
	 * @param  str|int $type     Object type (not for Custom Objects). Converts to objectID. Default is contact.
	 * @param  array  $args      Optional - Params for search (see docs)
	 * @return json   		     Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#add-an-object-to-a-sequence OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function add_to_sequence( $ids, $sequences, $type = 0, $args = array() ) {
		$args['objectID'] = WontrapiHelp::objectID( $type );
		$args['ids'] = $ids;
		$args['add_list'] = $sequences;
		return self::connect()->object()->addToSequence( $args );
	}

	/**
	 * Remove an object from a sequence
	 *
	 * This endpoint removes one or more objects from one or more sequences.
	 * 
	 * @param  string $ids       Required - An array as a comma-delimited list of the IDs of the objects to be removed from sequence(s).
	 * @param  string $sequences Required - An array as a comma-delimited list of the IDs of the sequences(s) from which to remove objects.
	 * @param  str|int $type     Object type (not for Custom Objects). Converts to objectID. Default is contact.
	 * @param  array  $args      Optional - Params for search (see docs)
	 * @return json   			 Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#remove-an-object-from-a-sequence OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function remove_from_sequence( $ids, $sequences, $type = 0, $args = array() ) {
		$args['objectID'] = WontrapiHelp::objectID( $type );
		$args['ids'] = $ids;
		$args['remove_list'] = $sequences;
		return self::connect()->object()->removeFromSequence( $args );
	}
	
	/**
	 * Tag an object
	 *
	 * Adds one or more tags to one or more objects.
	 * 
	 * @param  string $ids   Required - An array as a comma-delimited list of the IDs of the objects to be tagged.
	 * @param  string $tags  Required - An array as a comma-delimited list of the IDs of the tag(s) which should be added to objects.
	 * @param  str|int $type Object type (not for Custom Objects). Converts to objectID. Default is contact.
	 * @param  array  $args  Optional - Params for search (see docs)
	 * @return json   		 Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#tag-an-object OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function tag( $ids, $tags, $type = 0, $args = array() ) {
		$args['objectID'] = WontrapiHelp::objectID( $type );
		$args['ids'] = $ids;
		$args['add_list'] = $tags;
		return self::connect()->object()->addTag( $args );
	}

	/**
	 * Remove a tag from an object
	 *
	 * This endpoint removes one or more tags from one or more objects.
	 * 
	 * @param  string $ids   Required - An array as a comma-delimited list of the IDs of the objects to remove from tag(s).
	 * @param  string $tags  Required - An array as a comma-delimited list of the IDs of the tag(s) to be removed from objects.
	 * @param  str|int $type Object type (not for Custom Objects). Converts to objectID. Default is contact.
	 * @param  array  $args  Optional - Params for search (see docs)
	 * @return json   		 Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#remove-a-tag-from-an-object OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function untag( $ids, $tags, $type = 0, $args = array() ) {
		$args['objectID'] = WontrapiHelp::objectID( $type );
		$args['ids'] = $ids;
		$args['remove_list'] = $tags;
		return self::connect()->object()->removeTag( $args );
	}

	/**
	 * Subscribes one or more objects to one or more campaigns or sequences
	 * 
	 * @param  string  $ids      An array as a comma-delimited list of the IDs of the objects to be subscribed.
	 * @param  string  $list     An array as a comma-delimited list of the IDs of the campaign(s) or sequence(s) the objects should be subscribed to. 
	 * @param  string  $sub_type Either Campaign or Sequence
	 * @param  str|int $type     Object type (not for Custom Objects). Converts to objectID. Default is contact.
	 * @param  array   $args     Optional - Params for search (see docs)
	 * @return json              Response from Ontraport
	 * @link   http://api.ontraport.com/doc/#subscribe-an-object-to-a-campaign-or-sequence OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.5.0 Initial
	 */
	public static function subscribe( $ids, $list, $sub_type = 'Campaign', $type = 0, $args = array() ) {
		$args['objectID'] = WontrapiHelp::objectID( $type );
		$args['ids'] = $ids;
		$args['add_list'] = $list;
		$args['sub_type'] = $sub_type;
		return self::connect()->object()->subscribe( $args );
	}

	/**
	 * Unsubscribes one or more objects to one or more campaigns or sequences
	 * 
	 * @param  string  $ids      An array as a comma-delimited list of the IDs of the objects to unsubscribed.
	 * @param  string  $list     An array as a comma-delimited list of the IDs of the campaign(s) or sequence(s) to unsubscribe objects from. 
	 * @param  string  $sub_type Either Campaign or Sequence
	 * @param  str|int $type     Object type (not for Custom Objects). Converts to objectID. Default is contact.
	 * @param  array   $args     Optional - Params for search (see docs)
	 * @return json              Response from Ontraport
	 * @link   http://api.ontraport.com/doc/#unsubscribe-an-object-from-a-campaign-or-sequence OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.5.0 Initial
	 */
	public static function unsubscribe( $ids, $list, $sub_type = 'Campaign', $type = 0, $args = array() ) {
		$args['objectID'] = WontrapiHelp::objectID( $type );
		$args['ids'] = $ids;
		$args['remove_list'] = $list;
		$args['sub_type'] = $sub_type;
		return self::connect()->object()->unsubscribe( $args );
	}


	/** 
	 * ************************************************************
	 * Contacts 
	 * ************************************************************
	 */

	/**
	 * Create a contact
	 *
	 * Creates a new contact object. This endpoint allows duplication; if you want to 
	 * avoid duplicate emails, you should WontrapiGo::create_or_update_contact() instead.
	 * 
	 * @param  array  $args Data for the contact object
	 * @return json   		Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#create-a-contact OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function create_contact( $args = array() ) {
		return self::connect()->contact()->create( $args );
	}

	/**
	 * Merge or create a contact
	 *
	 * Looks for an existing contact with a matching email field and merges supplied data with 
	 * existing data. If no email is supplied or if no existing contact has a matching email 
	 * field, a new contact will be created. Recommended to avoid unwanted duplicate mailings.
	 * 
	 * @param  string $email Required - Contact's email (not technically required by Ontraport's API)
	 * @param  array  $args  Additional data to add to the contact
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#merge-or-create-a-contact OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function create_or_update_contact( $email, $args = array() ) {
		$args['email'] = $email;
		return self::connect()->contact()->saveOrUpdate( $args );
	}

	/**
	 * Retrieve a specific contact
	 *
	 * Retrieves all the information for an existing contact. The only parameter needed
	 * is the ID for the contact which is returned in the response upon contact creation.
	 * 
	 * @param  integer $id ID of the contact
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-a-specific-contact OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function get_contact( $id ) {
		$args = array( 'id' => $id );
		return self::connect()->contact()->retrieveSingle( $args );
	}

	/**
	 * Retrieve multiple contacts
	 *
	 * Retrieves a collection of contacts based on a set of parameters. You can limit 
	 * unnecessary API requests by utilizing criteria and our pagination tools to 
	 * select only the data set you require.
	 * 
	 * @param  array  $args Array of parameters used to search, sort, etc contacts
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-multiple-contacts OP API Documentation
	 * @link  https://api.ontraport.com/doc/#criteria OP search critera
	 * @author github.com/oakwoodgates 
	 * @since  0.3.0 Initial
	 */
	public static function get_contacts( $args = array() ) {
		return self::connect()->contact()->retrieveMultiple( $args );
	}

	/**
	 * Get contacts where a field is compared to a value.
	 *
	 * Example: To search contacts by email - get_contacts_where( 'email', '=', 'sample@email.com' );
	 * 
	 * @param  string  $field      Field to search
	 * @param  string  $operator   Possible values: > < >= <= = IN
	 * @param  str|int $value      Value to compare
	 * @param  array   $args       Array of additional parameters used to search, sort, etc contacts
	 * @return json   	           Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#criteria OP search critera
	 * @author github.com/oakwoodgates 
	 * @since  0.3.0 Initial
	 */
	public static function get_contacts_where( $field, $operator, $value, $args = array() ) {
		$args['condition'] = WontrapiHelp::prepare_search_condition( $field, $operator, $value );
		return self::get_contacts( $args );
	}

	/**
	 * Get contacts by email
	 *
	 * Note: it is possible to receive more than one contact if your contacts are not merged.
	 *
	 * @param  string $email Value to compare
	 * @param  array  $args  Array of additional parameters used to search, sort, etc contacts
	 * @return json   	     Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#criteria OP search critera
	 * @author github.com/oakwoodgates 
	 * @since  0.3.0 Initial
	 */
	public static function get_contacts_by_email( $email, $args = array() ) {
		return self::get_contacts_where( 'email', '=', $email, $args );
	}

	/**
	 * Get a contact's ID by associated email
	 * 
	 * Retrieves the IDs of contact objects by their email fields. 
	 * You can retrieve an array of all the IDs of contacts with matching emails, 
	 * or you can retrieve the first matching ID.
	 * 
	 * @param  string  $email   Required - Email of contact to get
	 * @param  integer $all     0 for the first ID found, 1 for an array of all matching IDs
	 * @return integer|array   	ID's from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-a-single-object OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.4.0 Initial         
	 */
	public static function get_contact_id_by_email( $email, $all = 0 ) {
		return self::get_object_id_by_email( $email, $all, 0 );
	}

	/**
	 * Update a contact
	 *
	 * Updates an existing contact with given data. The ID of the contact to update is required. 
	 * The other fields should only be used if you want to change the existing value.
	 * 
	 * @param  integer $id   Required - ID of the contact
	 * @param  array   $args Optional - Data to update
	 * @return json          Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#update-a-contact OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function update_contact( $id, $args = array() ) {
		$args['id'] = $id;
		return self::connect()->contact()->update( $args );
	}

	/**
	 * Delete a specific contact
	 *
	 * Deletes a specific contact by its ID
	 * 
	 * @param  integer $id ID of the contact
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#delete-a-specific-contact OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function delete_contact( $id ) {
		$args = array( 'id' => $id );
		return self::connect()->contact()->deleteSingle( $args );
	}

	/**
	 * Retrieve contact object meta
	 * 
	 * Retrieves the field meta data for the contact object.
	 * 
	 * @return json Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-contact-object-meta OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function get_contact_object_meta() {
		return self::connect()->contact()->retrieveMeta();
	}

	/**
	 * Retrieve contact collection info
	 *
	 * Retrieves information about a collection of contacts, such as the number of contacts that match the given criteria.
	 * 
	 * @param  array $args Search parameters
	 * @return json 	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-contact-collection-info OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial 
	 */
	public static function get_contact_collection_info( $args = array() ) {
		return self::connect()->contact()->retrieveCollectionInfo( $args );
	}


	/** 
	 * ************************************************************
	 * Forms (all form types)
	 * ************************************************************
	 */
	
	/**
	 * Retrieve a specific form
	 *
	 * Retrieves all the information for an existing form.
	 * 
	 * @param  integer $id The form ID
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-a-specific-form OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function get_form( $id ) {
		$args = array( 'id' => $id );
		return self::connect()->form()->retrieveSingle( $args );
	}

	/**
	 * Retrieve multiple forms
	 * 
	 * @param  array  $args Array of optional args
	 * @return json       Response from OP
	 * @link   https://api.ontraport.com/doc/#retrieve-multiple-forms OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.5.0 Initial
	 */
	public static function get_forms( $args = array() ) {
		return self::connect()->form()->retrieveMultiple( $args );
	}

	/**
	 * Retrieve form collection info
	 *
	 * Retrieves information about a collection of forms, such as the number of forms that match the given criteria.
	 * 
	 * @param  array $args Search parameters
	 * @return json 	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-form-collection-info OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial 
	 */
	public static function get_form_collection_info( $args = array() ) {
		return self::connect()->form()->retrieveCollectionInfo( $args );
	}

	/**
	 * Retrieve SmartForm meta
	 * 
	 * Retrieves the field meta data for a SmartForm. If you want to retrieve meta for another 
	 * form type, you should use WontrapiGo::get_object_meta() with the appropriate object type.
	 * 
	 * @return json Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-smartform-meta OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function get_smartform_object_meta() {
		return self::connect()->form()->retrieveMeta();
	}

	/**
	 * Retrieve Smart Form HTML
	 *
	 * Retrieves the HTML for a SmartForm by its ID. 
	 * This endpoint does not support ONTRAforms.
	 * 
	 * @param  integer $id The form ID
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-smart-form-html OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0 Initial
	 */
	public static function get_smartform_html( $id ) {
		$args = array( 'id' => $id );
		return self::connect()->form()->retrieveSmartFormHTML( $args );
	}

	/**
	 * Retrieve all blocks for form
	 *
	 * Retrieves IDs for all form blocks in a specified form or landing page.
	 * 
	 * @param  string $name Required - The name of the form or landing page.
	 * @return json 	    Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-all-blocks-for-form OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.5.0 Initial 
	 */
	public static function get_form_blocks( $name ) {
		$args = array( 'name' => $name );
		return self::connect()->form()->retrieveBlocksByForm( $args );
	}


	/** 
	 * ************************************************************
	 * Landing Pages
	 * ************************************************************
	 */

	/**
	 * Retrieve a specific landing page
	 *
	 * Retrieves all the information for an existing landing page.
	 * 
	 * @param  integer $id The landing page ID
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-a-specific-landing-page OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.2.0 Initial
	 */
	public static function get_landingpage( $id ) {
		$args = array( 'id' => $id );
		return self::connect()->landingpage()->retrieveSingle( $args );
	}

	/**
	 * Retrieve landing page meta
	 * 
	 * Retrieves the field meta data for the landing page object.
	 * 
	 * @return json Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-landing-page-meta OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.2.0 Initial
	 */
	public static function get_landingpage_object_meta() {
		return self::connect()->landingpage()->retrieveMeta();
	}

	/**
	 * Retrieve landing page collection info
	 *
	 * Retrieves information about a collection of landing pages, such as the number of landing pages that match the given criteria.
	 * 
	 * @param  array $args Search parameters (see docs)
	 * @return json 	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-landing-page-collection-info OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.2.0 Initial 
	 */
	public static function get_landingpage_collection_info( $args = array() ) {
		return self::connect()->landingpage()->retrieveCollectionInfo( $args );
	}

	/**
	 * Retrieve hosted URL
	 *
	 * Retrieves the hosted URL for a landing page by its ID.
	 * 
	 * @param  integer $id The landing page ID
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-hosted-url OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.2.0 Initial
	 */
	public static function get_landingpage_hosted_url( $id ) {
		$args = array( 'id' => $id );
		return self::connect()->landingpage()->getHostedURL( $args );
	}

	/** 
	 * ************************************************************
	 * Transactions 
	 * ************************************************************
	 */

	/**
	 * Retrieve a single transaction
	 *
	 * Retrieves all the information for an existing transaction.
	 * 
	 * @param  integer $id Required - The transaction ID
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-a-single-transaction OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.2.0 Initial
	 */
	public static function get_transaction( $id ) {
		$args = array( 'id' => $id );
		return self::connect()->transaction()->retrieveSingle( $args );
	}

	/**
	 * Retrieve multiple transactions
	 *
	 * Retrieves a collection of transactions based on a set of parameters. You can limit 
	 * unnecessary API requests by utilizing criteria and our pagination tools to 
	 * select only the data set you require.
	 * 
	 * @param  array $args Array of parameters used to search, sort, etc transactions
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-multiple-transactions OP API Documentation
	 * @link  https://api.ontraport.com/doc/#criteria OP search critera
	 * @author github.com/oakwoodgates 
	 * @since  0.3.0 Initial
	 */
	public static function get_transactions( $args = array() ) {
		return self::connect()->transaction()->retrieveMultiple( $args );
	}

	/**
	 * Get transactions for a contact.
	 *
	 * @param  int   $contact_id ID of Contact
	 * @param  array $args       Array of additional parameters used to search, sort, etc transactions
	 * @return json   	         Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#criteria OP search critera
	 * @author github.com/oakwoodgates 
	 * @since  0.3.0 Initial
	 */
	public static function get_transactions_by_contact_id( $contact_id, $args = array() ) {
		$args['condition'] = WontrapiHelp::prepare_search_condition( 'contact_id', '=', $contact_id );
		return self::get_transactions( $args );
	}

	/**
	 * Retrieve an order
	 *
	 * Retrieves all information about a specified order.
	 * 
	 * @param  integer $id Required - The transaction ID
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-an-order OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.2.0 Initial
	 */
	public static function get_order( $id ) {
		$args = array( 'id' => $id );
		return self::connect()->transaction()->retrieveOrder( $args );
	}

	/**
	 * Retrieve transaction object meta
	 * 
	 * Retrieves the field meta data for the transaction object.
	 * 
	 * @return json Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-transaction-object-meta OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.2.0 Initial
	 */
	public static function get_transaction_object_meta() {
		return self::connect()->transaction()->retrieveMeta();
	}

	/**
	 * Retrieve transaction collection info
	 *
	 * Retrieves information about a collection of transactions, such as the number of transactions that match the given criteria.
	 * 
	 * @param  array $args Search parameters
	 * @return json 	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#retrieve-transaction-collection-info OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.2.0 Initial 
	 */
	public static function get_transaction_collection_info( $args = array() ) {
		return self::connect()->transaction()->retrieveCollectionInfo( $args );
	}

	/**
	 * Convert transaction to collections
	 *
	 * Marks a transaction as in collections.
	 * 
	 * @param  integer $id Required - The transaction ID
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#convert-transaction-to-collections OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.2.0 Initial
	 */
	public static function transaction_to_collections( $id ) {
		$args = array( 'id' => $id );
		return self::connect()->transaction()->convertToCollections( $args );
	}

	/**
	 * Convert transaction to declined
	 *
	 * Marks a transaction as declined.
	 * 
	 * @param  integer $id Required - The transaction ID
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#convert-transaction-to-declined OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.2.0 Initial
	 */
	public static function transaction_to_declined( $id ) {
		$args = array( 'id' => $id );
		return self::connect()->transaction()->convertToDeclined( $args );
	}

	/**
	 * Mark transaction as paid
	 *
	 * Marks a transaction as paid.
	 * 
	 * @param  integer $id Required - The transaction ID
	 * @return json   	   Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#mark-transaction-to-paid OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.2.0 Initial
	 */
	public static function transaction_to_paid( $id ) {
		$args = array( 'id' => $id );
		return self::connect()->transaction()->markAsPaid( $args );
	}

	/**
	 * Process a transaction
	 *
	 * @param  int $contact_id       Required - The Contact ID
	 * @param  int $gateway_id       Required - The ID of the gateway to use for this transaction. Note that this is 
	 *                               the ID of the gateway object itself and not the external_id of the gateway. 
	 *                               A transaction cannot succeed without a valid gateway. 
	 * @param  arr $offer            Required - The product and pricing offer for the transaction.
	 * @param  int $invoice_template Required - The ID of the invoice template to use for this transaction. Default is 1.
	 * @param  arr $args             Other optional data to pass
	 * @return json                  Response from Ontraport
	 * @link   https://api.ontraport.com/doc/#process-a-transaction-manually OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.3.3 Initial              
	 */
	public static function transaction_process( $contact_id, $gateway_id, $offer, $invoice_template = 1, $args = array() ) {
		$args['contact_id'] = $contact_id;
		$args['gateway_id'] = $gateway_id;
		$args['invoice_template'] = $invoice_template;
		$args['offer'] = $offer;
		$args['chargeNow'] = 'chargeNow';
		return self::connect()->transaction()->processManual( $args );
	}

}
