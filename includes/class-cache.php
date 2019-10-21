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
}
