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

	}


}
