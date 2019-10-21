<?php
/**
 * Wontrapi Cache.
 *
 * @since   0.3.0
 * @package Wontrapi
 */

/**
 * Wontrapi Cache.
 *
 * @since 0.3.0
 */
class Wontrapi_Cache {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.3.0
	 *
	 * @var   Wontrapi
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  0.3.0
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
	 * @since  0.3.0
	 */
	public function hooks() {

	}

	/**
	 * 
	 */
	public static function get_user( $user_id = 0 ) {
		if ( ! (int) $user_id )
			return false;

		$data = get_transient( 'wontrapi_user_' . $user_id );
		if ( ! empty( $data ) ) {
			$data = maybe_unserialize( $data );
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

		return set_transient( 'wontrapi_user_' . $user_id, $data, 1800 );
	}

	public static function delete_user( $user_id = 0 ) {
		if ( ! (int) $user_id )
			return false;

		return delete_transient( 'wontrapi_user_' . $user_id );
	}

}
