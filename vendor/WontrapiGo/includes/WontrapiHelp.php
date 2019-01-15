<?php 
/**
 * WontrapiHelp
 *
 * Class for helping WontrapiGo
 *
 * @author 		github.com/oakwoodgates 
 * @copyright 	2017 	WPGuru4u
 * @link   		https://api.ontraport.com/doc/ 			OP API Documentation
 * @link   		https://api.ontraport.com/live/ 		OP API Docs
 * @link   		https://github.com/Ontraport/SDK-PHP/ 	Ontraport's SDK for PHP
 * @license 	https://opensource.org/licenses/MIT/ 	MIT
 */

class WontrapiHelp {

	/** 
	 * ************************************************************
	 * General helper methods 
	 * ************************************************************
	 */

	/**
	 * Get the ID of an object (contact, form, etc ) from a successfully
	 * created, updated, or retrieved request. 
	 * WARNING: If multiple objects passed, this returns the first ID found. 
	 * To return an array of all ID's in response, use get_ids_from_response()
	 * 
	 * @param  json|arr $response 	JSON response from Ontraport
	 * @return int 					ID of the object, or zero
	 * @author github.com/oakwoodgates 
	 * @since  0.3.0 Initial
	 */
	public static function get_id_from_response( $response ) {
		if( is_string( $response ) ) {
			$response = json_decode( $response, true );
		} 
		if ( isset( $response['data'] ) ) {

			if ( isset( $response['data']['id'] ) ) {
				return (int) $response['data']['id'];
			} elseif ( isset( $response['data']['attrs']['id'] ) ) {
				return (int) $response['data']['attrs']['id'];
			} elseif ( isset( $response['data'][0]['id'] ) ) {
				return (int) $response['data'][0]['id'];
			} elseif ( isset( $response['data']['ids'][0] ) ) {
				return (int) $response['data']['ids'][0];
			}
		} elseif ( isset( $response['id'] ) ) {
			return (int) $response['id'];
		} elseif ( isset( $response['attrs']['id'] ) ) {
			return (int) $response['attrs']['id'];
		} elseif ( isset( $response[0]['id'] ) ) {
			return (int) $response[0]['id'];
		} elseif ( isset( $response['ids'][0] ) ) {
			return (int) $response['ids'][0];
		}

		return 0;
	}

	/**
	 * Get the IDs of the objects (contact, form, etc ) from a successfully
	 * created, updated, or retrieved request.
	 * 
	 * @param  json|arr $response 	JSON response from Ontraport
	 * @return array          		Array of IDs of the objects, or empty array
	 * @author github.com/oakwoodgates 
	 * @since  0.4.0 Initial
	 */
	public static function get_ids_from_response( $response ) {
		if( is_string( $response ) ) {
			$response = json_decode( $response, true );
		} 

		if ( isset( $response['data'] ) ) {
			if ( isset( $response['data']['id'] ) ) {
				return array( $response['data']['id'] );
			} elseif ( isset( $response['data']['attrs']['id'] ) ) {
				return array( $response['data']['attrs']['id'] );
			} elseif ( isset( $response['data'][0]['id'] ) ) {
				$ids = array();
				foreach ( $response['data'] as $array ) {
					$ids[] = $array['id'];
				}
				return $ids;
			} elseif ( isset( $response['data']['ids'] ) ) {
				return $response['data']['ids'];
			}	
		} elseif ( isset( $response['id'] ) ) {
			return array( $response['id'] );
		} elseif ( isset( $response['attrs']['id'] ) ) {
			return array( $response['attrs']['id'] );
		} elseif ( isset( $response[0]['id'] ) ) {
			$ids = array();
			foreach ( $response as $array ) {
				$ids[] = $array['id'];
			}
			return $ids;
		} elseif ( isset( $response['ids'] ) ) {
			return $response['ids'];
		}

		return array();
	}

	/**
	 * Get the important stuff from a successfully created, updated, or retrieved request.
	 *
	 * This function was created because data can be returned different ways depending on
	 * if we are creating, updating, retrieving a single or multiple contacts, and whether
	 * we want to deal with multiple returned contacts or if first found will do.
	 * 
	 * @param  json|arr $response 	JSON response from Ontraport
	 * @param  bool 	$all 		Return all datasets (true) or first dataset (false)
	 * @return arr    				Array 
	 * @author github.com/oakwoodgates 
	 * @since  0.4.0 Initial
	 */
	public static function get_data_from_response( $response, $all = true ) {

		if ( is_string( $response ) ) {
			$response = json_decode( $response, true );
		} 

		if ( !empty( $response['data'] ) ) {
			$data = $response['data'];
			// return typical response from popular objects first (contact & transactions) 
			if ( !empty( $data['id'] ) ) {
				return $data;
			// from updating a contact or object
			} elseif ( !empty( $data['attrs'] ) ) {
				return $data['attrs'];
			} elseif ( is_array( $data ) ) {
				if ( is_numeric( key( $data ) ) ) {
					if ( !empty( $data[0]['id'] ) || !empty( $data[0]['form_id'] ) ) {
						// multiple contacts or objects
						if ( $all ) {
							return $data;
						} else {
							return $data[0];
						}
					} else {
						// from retrieve object meta
					//	return reset( $data );						
					//	@todo make function for getting retrieve object meta data
						return $data;						
					}
				} else {
					// not formatted like popular objects, or from creating fields and sections
					return $data;
				}
			} else {
				// we have a string response
				return $data;
			}
		} else {
			return 0;
		}
	}

	/**
	 * Prepare a simple A JSON encoded string to more specifically set criteria 
	 * for which contacts to bring back. For example, to check that a field 
	 * equals a certain value. See criteria examples for more details.
	 * 
	 * @param  string  $field      Field to search
	 * @param  string  $operand    Possible values: > < >= <= = IN
	 * @param  str|int $value      Value to compare
	 * @return string              String of data like "{field}{=}{value}"
	 * @link   https://api.ontraport.com/doc/#criteria Ontraport criteria docs
	 * @author github.com/oakwoodgates 
	 * @since  0.3.0 Initial
	 * @since  0.4.0 Utilize SDK
	 */
	public static function prepare_search_condition( $field, $operand, $value ) {
		$condition = new OntraportAPI\Criteria( $field, $operand, $value );
		return $condition->fromArray();
		/*
		if ( is_numeric ( $value ) ) {
			$condition = "{$field}{$operand}{$value}";
		} else {
			$condition = "{$field}{$operand}'{$value}'";
		}
		return $condition;
		*/
	}

	/**
	 * See if contact has a tag
	 *
	 * @param  json $contact_data JSON response from WontrapiGo::get_contact();
	 * @param  int  $tag          Tag ID in Ontraport
	 * @return bool               true if contact has tag
	 * @author github.com/oakwoodgates 
	 * @since  0.3.1 Initial
	 */
	public static function contact_has_tag( $contact, $tag ) {
		$contact = self::get_data_from_response( $contact, false );
		if ( isset( $contact['contact_cat'] ) ) {
			$contact_tags = $contact['contact_cat'];
			if ( $contact_tags ) {
				$contact_tags = array_filter( explode( '*/*',$contact_tags ) );
				if ( in_array( $tag, $contact_tags ) ){
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Get an array of Tags from a retrieved Contact
	 *
	 * @param  json $contact_data JSON response from WontrapiGo::get_contact();
	 * @return array
	 * @author github.com/oakwoodgates 
	 * @since  0.5.0 Initial
	 */
	public static function contact_tag_array( $contact ) {
		$contact = self::get_data_from_response( $contact, false );
		if ( isset( $contact['contact_cat'] ) ) {
			$contact_tags = $contact['contact_cat'];
			if ( $contact_tags ) {
				return array_filter( explode( '*/*', $contact_tags ) );
			}
		}
		return array();
	}


	/**
	 * Creates the markup for a field to be created. 
	 * Does not actually create the field in OP.
	 * 
	 * @param  string  $name     Required - the name (or alias) of the field to be created
	 * @param  string  $type     The field type. Possible types are:
	 *                           text (default), check, country, fulldate, list, longtext,
	 *                           numeric, price, phone, state, drop, email, sms, address
	 * @return object            To be passed to a function to create the field in OP.
	 * @since  0.4.0 Initial
	 */
	public static function prepare_field( $name, $type = 'text' ) {
		return new OntraportAPI\Models\FieldEditor\ObjectField( $name, $type ); 
	}

	/**
	 * Creates the markup for a dropdown field (with options) to be created. 
	 * Does not actually create the field in OP.
	 * 
	 * @param  string  $name     Required - the name (or alias) of the field to be created
	 * @param  array   $options  Required - array of options.
	 * @return object            To be passed to a function to create the field in OP.
	 * @since  0.5.0 Initial
	 */
	public static function prepare_dropdown_field( $name, $options ) {
		$dropdown = new OntraportAPI\Models\FieldEditor\ObjectField( $name, 'drop' ); 
		$dropdown->addDropOptions( $options );
		return $dropdown;
	}

	/**
	 * Creates the markup for a field options to be created. 
	 * Does not actually create the field or options in OP.
	 * 
	 * @param  string  $field    Required - a prepared field (see prepare_field())
	 * @param  array   $options  Array of options
	 * @param  string  $action   Add, remove, or replace options of a drop or list field type.
	 * @return object            To be passed to a function to create the field in OP.
	 * @since  0.4.0 Initial
	 */
	public static function field_options( $field, $options, $action = 'add' ) {

		switch ( $action ) {
			case 'add':
				$field->addDropOptions( $options );
				break;
			case 'remove':
				$field->removeDropOptions( $options );
				break;
			case 'replace':
				$field->replaceDropOptions( $options );
				break;
			default:
				$field->addDropOptions( $options );
				break;
		}
		return $field; 
	}

	/**
	 * Creates the markup for a section to be created. 
	 * Does not actually create the section or fields in OP.
	 * 
	 * @param  string $name   Required - the name of the section to be created
	 * @param  array $col_1   Array of fields objects to be added to the section.  
	 *                        Use prepare_field() function to create the fields.
	 * @param  array $col_2   Second column of fields
	 * @param  array $col_3   Third column of fields
	 * @return object         To be passed to a function to create the section and fields in OP.
	 * @since  0.4.0 Initial
	 */
	public static function prepare_section( $name, $col_1 = array(), $col_2 = array(), $col_3 = array() ) {

		$section = new OntraportAPI\Models\FieldEditor\ObjectSection( $name, $col_1 );

		if ( $col_2 ) {
			$section->putFieldsInColumn( 1, $col_2 );
		//	$section = self::add_col( $section, $col_2, 2 );
		}

		if ( $col_3 ) {
			$section->putFieldsInColumn( 2, $col_2 );
		//	$section = self::add_col( $section, $col_3, 3 );
		}

		return $section;
	}

	/**
	 * Puts fields into a column to be used within a section
	 * @param object $section 	A prepared section object (see prepare_section())
	 * @param object $field 	A prepared field object (see prepare_field())
	 * @param int    $col 		The column to add (1, 2, or 3)
	 * @return obj 				A section object
	 * @since  0.4.0 Initial
	 */
	public static function add_col( $section, $field, $col = 1 ) {
		$col = $col - 1;
		$section->putFieldsInColumn( $col, $field );
		return $section;
	}

	/**
	 * Count objects
	 * 
	 * Get count from, or count objects in a response
	 * 
	 * @param  str|arr  $data JSON response or decoded array from response
	 * @return int 		The value of the count key or a count of objects in a response
	 * @since  0.5.0 	Initial
	 */
	public static function get_count( $data ) {
		$count = 0;
		$data = self::get_data_from_response( $data );

		if ( !$data )
			return $count;

		if ( isset( $data['count'] ) ) {
			return $data['count'];
		}

		if ( !empty( $data ) ) {
			foreach ( $data as $obj ) {
				$count++;
			}
		} 
		return $count;
	}

	/**
	 * Get array of data from object meta response
	 *
	 * Retrieves the set of meta data for the specified object.
	 * Prepares the result in a way that is ready to be accessed
	 * independent of object type.
	 * 
	 * @param  str|int $type  Required - Object type (not for Custom Objects). Converts to objectID.
	 * @return array          Array of meta
	 * @uses   WontrapiGo::get_object_meta()
	 * @link   https://api.ontraport.com/doc/#retrieve-object-meta OP API Documentation
	 * @author github.com/oakwoodgates 
	 * @since  0.5.0 Initial 
	 */
	public static function get_objects_meta( $type, $response ) {
		$number = WontrapiHelp::objectID( $type );
		$response = WontrapiHelp::get_data_from_response( $response );
		if ( isset( $response[$number] ) ) {
			return $response[$number];
		}
		return array();
	}

	/**
	 * Get objectID for type
	 * 
	 * @param  string  $type Type of object
	 * @return integer       Object's objectID
	 * @author github.com/oakwoodgates 
	 * @link   https://api.ontraport.com/live/
	 * @since  0.1.0 Initial
	 */
	public static function objectID( $type ) {
		if ( is_numeric( $type ) )
			return $type;

		// let's not deal with strangeLetterCasing; lowercase ftw
		$type = strtolower( $type );
		// find the objectID
		switch( $type ) {
			case 'automationlogitems':
				$id = 100;
				break;
			case 'blasts':
				$id = 13;
				break;
			case 'campaignbuilderitems':
				$id = 140;
				break;
			case 'campaigns':
				$id = 75;
				break;
			case 'commissions':
				$id = 38;
				break;
			case 'contacts':
				$id = 0;
				break;
			case 'contents':
				$id = 78;
				break;
			case 'couponcodes':
				$id = 124;
				break;
			case 'couponproducts':
				$id = 125;
				break;
			case 'coupons':
				$id = 123;
				break;
			case 'creditcards':
				$id = 45;
				break;
			case 'customdomains':
				$id = 58;
				break;
			case 'customervalueitems':
				$id = 96;
				break;
			case 'customobjectrelationships':
				$id = 102;
				break;
			case 'customobjects':
				$id = 99;
				break;
			case 'deletedorders':
				$id = 146;
				break;
			case 'facebookapps':
				$id = 53;
				break;
			case 'forms':
				$id = 122;
				break;
			case 'fulfillmentlists':
				$id = 19;
				break;
			case 'gateways':
				$id = 70;
				break;
			case 'groups':
				$id = 3;
				break;
			case 'imapsettings':
				$id = 101;
				break;
			case 'invoices': // not an actual ontraport type, but when transactions are returned with WontrapiGo::get_object_meta( 'Transactions' ) they are referred to as "Invoice"
				$id = 46;
				break;
			case 'landingpages':
				$id = 20;
				break;
			case 'leadrouters':
				$id = 69;
				break;
			case 'leadsources':
				$id = 76;
				break;
			case 'logitems':
				$id = 4;
				break;
			case 'mediums':
				$id = 77;
				break;
			case 'messages':
				$id = 7;
				break;
			case 'messagetemplates':
				$id = 68;
				break;
			case 'notes':
				$id = 12;
				break;
			case 'offers':
				$id = 65;
				break;
			case 'openorders':
				$id = 44;
				break;
			case 'orders':
				$id = 52;
				break;
			case 'partnerproducts':
				$id = 87;
				break;
			case 'partnerprograms':
				$id = 35;
				break;
			case 'partnerpromotionalitems':
				$id = 40;
				break;
			case 'partners':
				$id = 36;
				break;
			case 'postcardorders':
				$id = 27;
				break;
			case 'products':
				$id = 16;
				break;
			case 'productsaleslogs':
				$id = 95;
				break;
			case 'purchasehistorylogs':
				$id = 30;
				break;
			case 'purchases':
				$id = 17;
				break;
			case 'referrals':
				$id = 37;
				break;
			case 'roles':
				$id = 61;
				break;
			case 'rules':
				$id = 6;
				break;
			case 'salesreportitems':
				$id = 94;
				break;
			case 'scheduledbroadcasts':
				$id = 23;
				break;
			case 'sequences':
				$id = 5;
				break;
			case 'sequencesubscribers':
				$id = 8;
				break;
			case 'shippedpackages':
				$id = 47;
				break;
			case 'shippingcollecteditems':
				$id = 97;
				break;
			case 'shippingfulfillmentruns':
				$id = 49;
				break;
			case 'shippingmethods':
				$id = 64;
				break;
			case 'smartforms':
				$id = 22; // informed guess from https://api.ontraport.com/doc/#retrieve-smartform-meta
				break;
			case 'staffs': // now 'users'
				$id = 2;
				break;
			case 'subscriberretentionitems':
				$id = 92;
				break;
			case 'subscriptionsaleitems':
				$id = 93;
				break;
			case 'tags':
				$id = 14;
				break;
			case 'tagsubscribers':
				$id = 138;
				break;
			case 'taskhistoryitems':
				$id = 90;
				break;
			case 'tasknotes':
				$id = 89;
				break;
			case 'taskoutcomes':
				$id = 66;
				break;
			case 'tasks':
				$id = 1;
				break;
			case 'taxes':
				$id = 63;
				break;
			case 'taxescollecteditems':
				$id = 98;
				break;
			case 'terms':
				$id = 79;
				break;
			case 'trackedlinks':
				$id = 80;
				break;
			case 'transactions':
				$id = 46;
				break;
			case 'upsellforms':
				$id = 42;
				break;
			case 'urlhistoryitems':
				$id = 88;
				break;
			case 'users':
				$id = 2;
				break;
			case 'webhooks':
				$id = 145;
				break;
			case 'wordpressmemberships':
				$id = 43;
				break;
			case 'wordpresssites':
				$id = 67;
				break;
			default:
				$id = '';
				break;
		}
		return $id;
	}


	/**
	 * DEPRECATED
	 */

	/**
	 * DEPRECATED - Use get_data_from_response()
	 * 
	 * Get the important stuff from a successfully created, updated, or retrieved request.
	 * 
	 * @param  json $response 	JSON response from Ontraport
	 * @param  bool $all 		Return all datasets (true) or first dataset (false)
	 * @param  bool $array 		To decode as array (true) or object (false) via json_decode
	 * @return obj|array 		Object or array (empty string if no valid response passed)
	 * @author github.com/oakwoodgates 
	 * @since  0.3.1 Initial
	 * @since  0.4.0 Deprecated - Use get_data_from_response()
	 */
	public static function get_object_from_response( $response, $all = false, $array = false ) {
		if( is_string( $response ) ) {
			$response = json_decode( $response, $array );
		} else {
			$response = json_decode( json_encode( $response ), $array );
		}
		if ( is_array( $response ) ) {
			if ( isset( $response['data']['id'] ) ) {
				return $response['data'];
			} elseif ( isset( $response['data']['attrs']['id'] ) ) {
				return $response['data']['attrs'];
			} elseif ( isset( $response['data'][0]['id'] ) ) {
				if ( $all ) {
					return $response['data'];
				} else {
					return $response['data'][0];
				}
			} elseif ( isset( $response['id'] ) ) {
				return $response;
			} elseif ( isset( $response['data'] ) ) {
				return $response['data'];
			} elseif ( isset( $response[0] ) ) {
				if ( ( is_array( $response[0] ) && isset( $response[0]['id'] ) ) 
				|| ( is_object( $response[0] ) && isset( $response[0]->id ) ) ) 
				{
					if ( $all ) {
						return $response;
					} else {
						return $response[0];
					}
				}
			}
		} else {
			if ( isset( $response->data->id ) ) {
				return $response->data;
			} elseif ( isset( $response->data->attrs->id ) ) {
				return $response->data->attrs;
			} elseif ( isset( $response->data->{'0'}->id ) ) {
				if ( $all ) {
					return $response->data;
				} else {
					return $response->data->{'0'};
				}
			} elseif ( isset( $response->id ) ) {
				return $response;
			} elseif ( isset( $response->data ) ) {
				if ( is_array( $response->data ) && isset( $response->data[0] ) ) {
					if ( $all ) {
						return $response->data;
					} else { 
						return $response->data[0];
					}
				}
				return $response->data;
			} elseif ( isset( $response->{'0'}->id ) ) {
				if ( $all ) {
					return $response;
				} else {
					return $response->{'0'};
				}
			}
		}

		return 0;
	}

}
