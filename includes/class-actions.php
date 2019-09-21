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
		add_action( 'init', 		array( $this, 'listen' ) );
		add_action( 'wp_footer', 	array( $this, 'tracking_script' ) );
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

}
