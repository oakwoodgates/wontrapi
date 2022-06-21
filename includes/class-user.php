<?php 
/**
 * Wontrapi User.
 *
 * @since   0.4.0
 * @package User
 */

/**
 * Wontrapi Actions.
 *
 * @since 0.4.0
 */
class Wontrapi_User {
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
		add_action( 'cmb2_admin_init', [ $this, 'meta' ] );
		add_action( 'user_register',   [ $this, 'user_register' ], 20, 1 );
		add_action( 'wp_login',        [ $this, 'user_login' ], 20, 2 );
	}

	public function user_register( $user_id ) {
		
	}

	public function user_login( $user_login, $user ) {

	}

	public function meta(){
		$p = 'won_';
		$cmb = new_cmb2_box( array(
			'id'               => $p . 'edit',
			'title'            => __( 'User Profile Metabox', 'wontrapi' ), // Doesn't output for user boxes
			'object_types'     => array( 'user' ), // Tells CMB2 to use user_meta vs post_meta
			'show_names'       => true,
			'new_user_section' => 'add-new-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
		) );
	
		$cmb->add_field( array(
			'name'    => 'Ontraport Contact ID',
			'id'      => Wontrapi_Core::$user_contact_id_key,
			'type'    => 'text',
		) );
	/*
		$cmb->add_field( array(
			'name'    => 'Website Subscriber ID',
			'id'      => $p . 'wsid',
			'type'    => 'text',
		) );
	*/
	}
}



