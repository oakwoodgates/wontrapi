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
class Wontrapi_Core {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.5.2
	 *
	 * @var   Wontrapi
	 */
	protected $plugin = null;

	public static $transient_prefix = 'wontrapi_user_';

	public static $option_name = 'wontrapi_options';

	public static $user_meta_prefix = 'won_';

	public static $user_contact_id_key = 'won_cid';

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
		add_action( 'wp_footer',     [ $this, 'tracking_script' ] );
		add_action( 'rest_api_init', [ $this, 'register_post_route' ] );
	}

	function tracking_script() {
		$data = wontrapi_get_option( 'tracking' );
		if ( !empty( $data['tracking'] ) ) {
			echo $data['tracking'];
		}
	}

	public static function get_options() {
		return get_option( self::$option_name, [] );
	}
	
	/**
	 * Get single option from options page
	 * @param  string $key [description]
	 * @return [type]      [description]
	 */
	public static function get_option( $key = '' ) {
		$options = self::get_options();
		if ( !empty( $key ) ) {
			$options = ( !empty( $options[$key] ) && !is_null( $options[$key] ) ) ? $options[$key] : null;
		}
		return $options;
	}

	public static function get_data( $data, $all = false ) {
		return WontrapiHelp::get_data_from_response( $data, $all );
	}

	public static function get_id( $data ) {
		return WontrapiHelp::get_id_from_response( $data );
	}

	/**
	 * Get a user's Contact_ID from OP (stored in user_meta)
	 */
	public static function get_user_contact_id_from_meta( $user_id = 0 ) {
		return get_user_meta( $user_id, self::$user_contact_id_key, true );
	}

	/**
	 * Store user's OP Contact_ID in meta
	 */
	public static function update_user_contact_id_meta( $user_id = 0, $contact_id = 0 ) {
		return update_user_meta( $user_id, self::$user_contact_id_key, $contact_id );
	}

	/**
	 * Delete a user's OP Contact_ID from user_meta
	 */
	public static function delete_user_contact_id_meta( $user_id = 0 ) {
		return delete_user_meta( $user_id, self::$user_contact_id_key );
	}


	public static function get_user_transient( $user_id ) {
		return get_transient( self::$transient_prefix . $user_id );
	}

	public static function set_user_transient( $user_id = 0, $data = '' ) {
		if ( ! (int) $user_id || empty( $data ) )
			return false;

		$data = self::get_data( $data, false );
		$contact_id = self::get_id( $data );

		if ( $contact_id ) {
			// set transient
			$transient_set = set_transient( self::$transient_prefix . $user_id, $data, 1800 );
			// keep user meta fresh
			self::update_user_contact_id_meta( $user_id, $contact_id );

			if ( $transient_set ) {
				return $data;
			}
		}

		return false;
	}

	public static function delete_user_transient( $user_id = 0 ) {
		if ( ! (int) $user_id )
			return false;

		return delete_transient( self::$transient_prefix . $user_id );
	}

	/**
	 * 
	 */
	public static function get_contact_by_user_id( $user_id = 0 ) {
		if ( ! (int) $user_id )
			return false;

		// check database
		$contact = self::get_user_transient( $user_id );

		if ( ! empty( $contact ) ) {
			$contact = maybe_unserialize( $contact );
			return $contact;
		} else {

			$contact_id = self::get_user_contact_id_from_meta( $user_id );

			if ( $contact_id ) {
				$contact = self::get_contact_by_contact_id( $contact_id );

				// check that our local contact_id's user wasn't deleted or merged in OP
				if ( ! $contact ) {
					self::delete_user_contact_id_meta( $user_id ); // false contact_id
				}
			}

			if ( ! $contact ) {
				// try to get by email
				$user = get_user_by( 'id', $user_id );
				if ( $user ) {
					$contact = self::get_contact_by_email( $user->user_email );
					$ids = WontrapiHelp::get_ids_from_response( $contact );
					// do we have a contact?
					if ( $ids ) {
						// don't update contact_id in database if multiple contacts found in OP
						if ( count( $ids ) === 1 ) {
							self::update_user_contact_id_meta( $user_id, $ids[0] );
						}
						$contact = self::get_data( $contact, false );
					} 
				} 
			}
			// set transient
			if ( $contact ) {
				self::set_user_transient( $user_id, $contact );
			}

		}
		return $contact;
	}

	public static function get_contact_by_contact_id( $contact_id = 0 ) {

		if ( $contact_id ) {
			$contact = WontrapiGo::get_contact( $contact_id );
			return self::get_data( $contact, false );
		} 
		return false;

	}

	public static function get_contact_by_email( $email = '', $all = false ) {

		if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$contacts = WontrapiGo::get_contacts_by_email( $email );
			$data = self::get_data( $contacts, $all );
			$user_id = email_exists( $email );
			if ( !empty( $data ) && $user_id ) {
				self::set_user_transient( $user_id, $data );
			} elseif ( empty( $data ) ) {
				// create contact in OP?
				// probably not
			}

			return $data;
		} 

		return false;
	}

	/**
	 * Creates or updates a contact given an email. 
	 */
	public static function add_or_update_contact( $email = '', $args = [] ) {

		$user_id = email_exists( $email );

		$contact_data = apply_filters( 'wontrapi_pre_add_or_update_contact', $args, $email, $user_id );

		$response = WontrapiGo::create_or_update_contact( $email, $contact_data );

		$data = self::get_data( $response );
		$contact_id = self::get_id( $data );

		if ( $contact_id && $user_id ) {
			self::set_user_transient( $user_id, $data );
		}
	
		do_action( 'wontrapi_contact_added_or_updated', $contact_id, $data, $email, $user_id );

		return $data;
	}

	/**
	 * This function is where we register our routes for our endpoint.
	 */
	function register_post_route() {
		register_rest_route( 'wontrapi/v1', '/post', array(
			// By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
			'methods'  => 'POST',
			// Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
			'callback' => 'Wontrapi_Core::do_post_endpoint',
		) );
	}

	/**
	 * This is our callback function that embeds our phrase in a WP_REST_Response
	 */
	static function do_post_endpoint() {

		$data = wontrapi_get_options();

		if ( empty( $data['ping_value'] ) ) {
			return rest_ensure_response( 'No value in options' );
		}

		$ping_val = $data['ping_value'];
		if ( isset( $_POST['wontrapi_key'] ) ) {
			if ( $_POST['wontrapi_key'] == $ping_val ) {
				if ( isset( $_POST['wontrapi_action'] ) ) {
					$actions = $_POST['wontrapi_action'];
					$actions = explode(',', $actions);
					foreach ( $actions as $action ) {
						$action = sanitize_key( $action );
						// fire a specific action based on the event
						do_action( "wontrapi_post_action_{$action}", $_POST );
					}
					return rest_ensure_response( 'Success!' );
				}
			}
		}

		// rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
		return rest_ensure_response( 'Invalid request' );
	}
}
