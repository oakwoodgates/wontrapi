<?php
/**
 * Wontrapi Cache.
 *
 * @since   0.5.2
 * @package Wontrapi
 */

/**
 * Wontrapi Cache.
 *
 * @since 0.5.2
 */
class Wontrapi_Cache {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.5.2
	 *
	 * @var   Wontrapi
	 */
	protected $plugin = null;

	public static $cache = array();
	public static $current = array();

	/**
	 * Constructor.
	 *
	 * @since  0.5.2
	 *
	 * @param  Wontrapi $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.5.2
	 */
	public function hooks() {
		add_action( 'user_register', array( $this, 'user_register' ), 20, 1 );
		add_action( 'wp_login', array( $this, 'user_login' ), 20, 2 );
	}

	public static function user_register( $user_id ) {
		
	}

	public static function user_login( $user_login, $user ) {

	}


	public static function set( $type = 'contact', $id = 0, $data = 0 ) {
		if ( ! (int) $id )
			return false;

		$data = WontrapiHelp::get_data_from_response( $data, false );

		if ( empty( WontrapiHelp::get_id_from_response( $data ) ) ) 
			return false;

		switch ( $type ) {
			case 'user':
				self::$cache["user_$id"] = $data;
				return set_transient( 'wontrapi_user_' . $id, $data, 1800 );

			case 'current':
				self::$cache["current"] = $data;
				return set_transient( 'wontrapi_contact_' . $id, $data, 1800 );

			case 'contact':
				self::$cache["contact_$id"] = $data;
				return set_transient( 'wontrapi_contact_' . $id, $data, 1800 );
		
			default:
				return false;
		}
	}


	public static function delete( $type = 'contact', $id = 0 ) {
		if ( ! (int) $id )
			return false;

		switch ( $type ) {
			case 'contact':
				self::$cache["contact_$id"] = 0;
				return delete_transient( 'wontrapi_contact_' . $id );

			case 'user':
				self::$cache["user_$id"] = 0;
				return delete_transient( 'wontrapi_user_' . $id );
		
			default:
				return false;
		}
	}


	/**
	 * 
	 */
	public static function get_user( $user_id = 0 ) {
		if ( ! (int) $user_id )
			return false;

		if ( ! empty( self::$cache["user_$user_id"] ) ) {
			return self::$cache["user_$user_id"];
		}

		$data = get_transient( 'wontrapi_user_' . $user_id );

		if ( ! empty( $data ) ) {
			self::$cache["user_$user_id"] = $data;
			return $data;
		} else {
			// It wasn't there, so regenerate the data and save the transient
			// $data = self::get_by_user_id( $user_id );

			$user = get_user_by( 'id', $user_id );
			if ( empty( $user->ID ) ) {
				return false;
			}

			$contact_id = wontrapi_get_opuid( $user_id );

			// try to get by contact_id
			if ( $contact_id ) {
				$data = WontrapiGo::get_contact( $contact_id );
				$data = WontrapiHelp::get_data_from_response( $data, false );
				// check that our local contact_id's user wasn't deleted or merged in OP
				if ( ! $data ) {
					wontrapi_delete_opuid( $user_id ); // false contact_id
				}
			}

			if ( ! $data ) {
				// try to get by email
				$data = WontrapiGo::get_contacts_by_email( $email );
				$data = WontrapiHelp::get_data_from_response( $data, true );
				$ids = WontrapiHelp::get_ids_from_response( $data );

				// only update contact_id in database if single contact found in OP
				if ( 1 === count( $ids ) ) {
					wontrapi_update_opuid( $user_id, $ids[0] );
				}

				$data = WontrapiHelp::get_data_from_response( $data, false );
			}

			if ( $data ) {
				self::set( 'user', $user_id, $data );
				return $data;
			}
		}
		return false;
	}


	public static function get_current() {

		// quick storage
		if ( ! empty( self::$current ) ) {
			return self::$current;
		}

		if ( $user_id = get_current_user_id() ) {

			$data = self::get_user( $user_id );
			if ( $data ) {
				self::$current = $data;
				return $data;
			}
		}

		$contact_id = 0;
		if ( ! empty( $_GET['cid'] ) ) {
			$contact_id = (int) $_GET['cid'];
		}

		$email = 0;
		if ( ! empty( $_GET['email'] ) ) {
			$email = urldecode( $_GET['email'] );
			if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				$email = 0;
			}
		}

		if ( $contact_id && $email ) {
			$contact = WontrapiGo::get_contact( $contact_id );
			$data = WontrapiHelp::get_data_from_response( $contact, false );
			if ( ! empty( $data->email ) && ( $data->email === $email ) ) {
				self::set( 'contact', $contact_id, $data );
				self::$current = $data;
				return $data;
			}
		}
		return false;
	}


 	// @TODO FIX ME!!!!
/*	public static function get_contact( $value ) {

		$email = $contact_id = $data = 0;

		if ( filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
			$email = $value;
			// todo - logic for get by email
			$contacts = WontrapiGo::get_contacts_by_email( $email );

		} elseif ( (int) $value ) {
			$contact_id = $value;
			if ( ! empty( self::$cache["contact_$contact_id"] ) ) {
				return self::$cache["contact_$contact_id"];
			}
			$data = get_transient( 'wontrapi_contact_' . $contact_id );
			if ( false === $data ) {
				$data = self::get_by_contact_id( $contact_id );
				if ( $data ) {
					self::set( 'contact', $contact_id, $data );
					return $data;
				}
			} else {
				self::$cache["contact_$contact_id"] = $data;
				return $data;
			}
		} else {
			return false;
		}
		return false;
	}
*/


}
