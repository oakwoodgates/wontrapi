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
			$data = maybe_unserialize( $data );
			self::$cache["user_$user_id"] = $data;
		} else {
			// It wasn't there, so regenerate the data and save the transient
			$data = wontrapi_get_contacts_by_user_id( $user_id );
			$data = WontrapiHelp::get_data_from_response( $data, false );
			self::set_user( $user_id, $data );
		}
		return $data;
	}

	public static function set_user( $user_id = 0, $data = '' ) {
		if ( ! (int) $user_id || empty( $data ) )
			return false;

		if ( ! is_serialized( $data ) )
			$data = maybe_serialize( $data );

		self::$cache["user_$user_id"] = $data;
		return set_transient( 'wontrapi_user_' . $user_id, $data, 1800 );
	}

	public static function delete_user( $user_id = 0 ) {
		if ( ! (int) $user_id )
			return false;

		return delete_transient( 'wontrapi_user_' . $user_id );
	}


	public static function get_contact( $value ) {

		$email = $contact_id = $data = 0;

		if ( filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
			$email = $value;
			// todo - logic for get by email
		} elseif ( (int) $value ) {
			$contact_id = $value;
			if ( ! empty( self::$cache["contact_$contact_id"] ) ) {
				return self::$cache["contact_$contact_id"];
			}
			$data = get_transient( 'wontrapi_contact_' . $contact_id );
			if ( false === $data ) {
				$data = wontrapi_get_contact_by_contact_id( $contact_id );
				$data = WontrapiHelp::get_data_from_response( $data, false );
				self::set_contact( $contact_id, $data );
				return $data;
			}
			$data = maybe_unserialize( $data );
			self::$cache["contact_$contact_id"] = $data;
		} else {
			return false;
		}

		return $data;
	}

	public static function set_contact( $contact_id = 0, $data = '' ) {
		if ( ! (int) $contact_id || empty( $data ) )
			return false;

		if ( ! is_serialized( $data ) )
			$data = maybe_serialize( $data );

		self::$cache["contact_$contact_id"] = $data;
		return set_transient( 'wontrapi_contact_' . $contact_id, $data, 1800 );
	}

	public static function delete_contact( $contact_id = 0 ) {
		if ( ! (int) $contact_id )
			return false;

		return delete_transient( 'wontrapi_contact_' . $contact_id );
	}

}
