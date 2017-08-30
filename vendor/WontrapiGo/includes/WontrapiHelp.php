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
 * @version 	0.3.0 
 */

class WontrapiHelp {

	/**
	 * Singleton instance
	 *
	 * @var WontrapiHelp
	 * @since  0.3.0
	 */
	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 * 
	 * @return WontrapiHelp A single instance of this class.
	 * @since  0.1.0	Initial	 
	 */
	public static function init() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	protected function __construct() {}

	/** 
	 * ************************************************************
	 * General helper methods 
	 * ************************************************************
	 */

	/**
	 * Get the ID of an object (contact, form, etc ) from a successfully
	 * created, updated, or retrieved request.
	 * 
	 * @param  json $response JSON response from Ontraport
	 * @return int            ID of the object
	 * @author github.com/oakwoodgates 
	 * @since  0.3.0 Initial
	 */
	public static function get_id_from_response( $response ) {
		$response = json_decode( $response );
		$id = 0;
		if ( isset( $response->data->id ) ) {
			$id = $response->data->id;
		} elseif ( isset( $response->data->attrs->id ) ) {
			$id = $response->data->attrs->id;
		} elseif ( isset( $response->data[0]->id ) ) {
			$id = $response->data[0]->id;
		}
		return intval( $id );
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
	 */
	public static function prepare_search_condition( $field, $operand = '=', $value ) {

		if ( is_numeric ( $value ) ) {
			$condition = "{$field}{$operand}{$value}";
		} else {
			$condition = "{$field}{$operand}'{$value}'";
		}

		return $condition;
	}

	/**
	 * Return a JSON response the way you want it
	 * 
	 * @param  string $option   How do you want the $response returned?
	 *                          object|id|json
	 * @param  json  $response  JSON response from Ontraport
	 * @return mixed            Object (stdClass), ID (integer), or JSON (string)
	 * @author github.com/oakwoodgates 
	 * @since  0.3.0 Initial
	 */
	public static function return( $option = 'object', $response ){
		switch( $option ) {
			case 'object':
				return json_decode( $response );
				break;
			case 'id':
				return self::get_id_from_response( $response );
				break;
			case 'json':
			default:
				return $response;
				break;
		}
	}

	/**
	 * Get objectID for type
	 * 
	 * @param  string  $type Type of object
	 * @return integer       Object's objectID
	 * @author github.com/oakwoodgates 
	 * @since  0.1.0
	 */
	public static function objectID( $type ) {
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
			case 'staffs':
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

}