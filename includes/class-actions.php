<?php
/**
 * Wontrapi Actions.
 *
 * @since   0.4.0
 * @package Wontrapi
 */

/**
 * Wontrapi Actions.
 *
 * @since 0.4.0
 */
class Wontrapi_Actions {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.4.0
	 *
	 * @var   Wontrapi
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  0.4.0
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
	 * @since  0.4.0
	 */
	public function hooks() {
	//	add_action( 'init', 			array( $this, 'listen' ) );
		add_action( 'wp_footer', 		array( $this, 'tracking_script' ) );
		add_action( 'rest_api_init', 	array( $this, 'register_post_route' ) );
	}

	function listen() {
		$data = get_option( 'wontrapi_options' );

		if ( empty( $data['ping_value'] ) ) {
			return;
		}

		$ping_key = ( !empty( $data['ping_key'] ) ) ? $data['ping_key'] : 'wontrapi_key';
		$ping_val = $data['ping_value'];
		if ( isset( $_POST["$ping_key"] ) ) {
			if ( $_POST["$ping_key"] == $ping_val ) {
				if ( isset( $_POST['wontrapi_action'] ) ) {
					$action = sanitize_key( $_POST['wontrapi_action'] );
					// fire a specific action based on the event
					do_action( "wontrapi_post_action_{$action}", $_POST );
				}
			}
		}
	}

	function tracking_script() {
		$data = get_option( 'wontrapi_options' );
		if ( !empty( $data['tracking'] ) ) {
			echo $data['tracking'];
		}
	}

	/**
	 * This function is where we register our routes for our example endpoint.
	 */
	function register_post_route() {
		// register_rest_route() handles more arguments but we are going to stick to the basics for now.
		register_rest_route( 'wontrapi/v1', '/post', array(
			// By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
			'methods'  => 'POST',
			// Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
			'callback' => 'Wontrapi_Actions::do_post_endpoint',
		) );
	}

	/**
	 * This is our callback function that embeds our phrase in a WP_REST_Response
	 */
	static function do_post_endpoint() {

		$data = get_option( 'wontrapi_options' );

		if ( empty( $data['ping_value'] ) ) {
			return rest_ensure_response( 'No value in options' );
		}

		$ping_key = ( !empty( $data['ping_key'] ) ) ? $data['ping_key'] : 'wontrapi_key';
		$ping_val = $data['ping_value'];
		if ( isset( $_POST["$ping_key"] ) ) {
			if ( $_POST["$ping_key"] == $ping_val ) {
				if ( isset( $_POST['wontrapi_action'] ) ) {
					$action = sanitize_key( $_POST['wontrapi_action'] );
					// fire a specific action based on the event
					do_action( "wontrapi_post_action_{$action}", $_POST );
					return rest_ensure_response( 'Success! ' . $action );
				}
			}
		}

		// rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
		return rest_ensure_response( 'Hello World, this is the WordPress REST API' );
	}
}
