<?php
/**
 * Wontrapi Options class.
 *
 * @since 0.3.0
 * @package Wontrapi
 */
class Wontrapi_Options {
	/**
	 * Parent plugin class.
	 *
	 * @var    Wontrapi
	 * @since  0.3.0
	 */
	protected $plugin = null;

	/**
	 * Option key, and option page slug.
	 *
	 * @var    string
	 * @since  0.3.0
	 */
	protected $auth = 'wontrapi_auth';

	/**
	 * Options page metabox ID.
	 *
	 * @var    string
	 * @since  0.3.0
	 */
	protected $metabox_id = 'wontrapi_options_metabox';

	/**
	 * Options Page title.
	 *
	 * @var    string
	 * @since  0.3.0
	 */
	protected $title = '';

	/**
	 * Constructor.
	 *
	 * @since  0.3.0
	 *
	 * @param  Wontrapi $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		// Set our title.
		$this->title = esc_attr__( 'Wontrapi', 'wontrapi' );

		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.3.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'cmb2_admin_init', array( $this, 'metabox' ) );		
	//	add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_menu', array( $this, 'submenu' ), -1 );
	}

	/**
	 * Register our setting to WP.
	 *
	 * @since  0.3.0
	 */
	public function admin_init() {
		register_setting( $this->plugin->slug, $this->plugin->slug );
		register_setting( $this->auth, $this->auth, array( 'default' => array() ) );
	}

	public function submenu() {
		add_submenu_page ( 
			$this->plugin->slug,
			$this->title,
			$this->title,
			'manage_options',
			$this->plugin->slug
		);
	}

	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @since  0.4.0
	 */
	public function display() {
		?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" style="position: relative;">
					<div class="wrap cmb2-options-page <?php echo esc_attr( $this->plugin->slug ); ?>">
						<h1>Wontrapi Configuration Settings
						<?php // echo esc_html( get_admin_page_title() ); ?></h1>
						<?php cmb2_metabox_form( $this->metabox_id, $this->plugin->slug ); ?>
					</div>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<!-- <h3>Sidebar</h3> -->
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Add custom fields to the options page.
	 *
	 * @since  0.4.0
	 */
	public function metabox() {

		// Add our CMB2 metabox.
		$cmb = new_cmb2_box( array(
			'id'           => $this->metabox_id,
			'title'        => $this->title,
			'object_types' => array( 'options-page' ),
			'display_cb'   => array( $this, 'display' ),
			'show_on'      => array(
				// These are important, don't remove.
				'key'   => 'options-page',
				'value' => array( $this->plugin->slug ),
			),
		) );

		$cmb->add_field( array(
			'name' => 'API Settings',
			'desc' => 'Creates the connection in Ontraport. For information on obtaining an API key, <a href="https://app.ontraport.com/#!/api_settings/listAll" target="_blank">click here</a>.',
			'type' => 'title',
			'id'   => 'wiki_test_title'
		) );

		$cmb->add_field( array(
			'name'    => __( 'API ID', 'wontrapi' ),
			'id'      => 'api_appid', 
			'type'    => 'text_medium',
			'attributes'  => array(
				'required'    => 'required',
			)
		) );

		$cmb->add_field( array(
			'name'    => __( 'API Key', 'wontrapi' ),
			'id'      => 'api_key', 
			'type'    => 'text_medium',
			'attributes'  => array(
				'required'    => 'required',
			)
		) );

		$cmb->add_field( array(
			'name' => 'Within Ontraport',
			'desc' => 'This is a title description',
			'type' => 'title',
			'id'   => 'wiki_test_title1'
		) );

		$options = get_option( $this->plugin->slug, array() );
	//	$ping_value = ( !empty( $options['ping_value'] ) ) ? $options['ping_value'] : rand();
		if ( isset( $_POST['ping_value'] ) ) {
			$ping_value = $_POST['ping_value'];
		} elseif ( isset( $options['ping_value'] ) ) {
			$ping_value = $options['ping_value'];
		} else {
			$ping_value = wp_generate_password( 24, false, false );
			$options['ping_value'] = $ping_value;
			update_option( $this->plugin->slug, $options );
		}

		$cmb->add_field( array(
			'name'    => __( 'Ping Key Value', 'wontrapi' ),
			'id'      => 'ping_value',
			'default' => $ping_value,
			'type'    => 'text_medium',
			'desc'    => __( '<br/>This value is used as a password when sending data from Ontraport to your website. <br/>Only use letters and/or numbers. <br/>When creating a Rule that will ping this website, include a parameter like this: <br/>wontrapi_key='.$ping_value, 'wontrapi' ),
			'attributes'  => array(
				'required'    => 'required',
			)
		) );

/*		$cmb->add_field( array(
			'name'    => __( 'Section Title', 'wontrapi' ),
			'id'      => 'section_title', 
			'desc'    => __( '<br/>Creates a section in Ontraport to store fields and data for Contacts. <br/>Plugins and developers can hook into Wontrapi and store data in this section. <br/>It is recommended to only set this once and not change it.', 'wontrapi' ),
			'default' => 'Wontrapi - ' . get_bloginfo( 'name' ),
			'type'    => 'text_medium',
			'attributes'  => array(
				'required'    => 'required',
			)
		) );
*/
		$cmb->add_field( array(
			'name' => 'Ontraport Tracking Script',
			'desc' => 'To enable tracking, copy and paste the full script below. To obtain the script, <a href="https://app.ontraport.com/#!/account/view&components_pane_nav_tabs=Developer+Preferences+and+Resources" target="_blank">click here for intructions</a>',
			'type' => 'title',
			'id'   => 'wiki_test_title2'
		) );

		$cmb->add_field( array(
			'name'    => __( 'Enter tracking script?', 'wontrapi' ),
			'desc'    => __( 'Optional', 'wontrapi' ),
			'id'      => 'tracking', 
			'type'    => 'text'
		) );

		do_action( 'wontrapi_options_page', $cmb );
	}

}


function wontrapi_options_page_register_main_options_metabox() {

	/**
	 * Registers tertiary options page, and set main item as parent.
	 */
	$tertiary_options = new_cmb2_box( array(
		'id'           => 'wontrapi_tertiary_options_page',
		'title'        => esc_html__( 'Tertiary Options', 'cmb2' ),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'wontrapi_tertiary_options',
		'parent_slug'  => 'wontrapi_options',
	) );
	$tertiary_options->add_field( array(
		'name' => esc_html__( 'Test Text Area for Code', 'cmb2' ),
		'desc' => esc_html__( 'field description (optional)', 'cmb2' ),
		'id'   => 'textarea_code',
		'type' => 'textarea_code',
	) );
}
// use this
// add_action( 'wontrapi_options_page', 'wontrapi_options_page_register_main_options_metabox' );
// not this
//	add_action( 'cmb2_admin_init',       'wontrapi_options_page_register_main_options_metabox' );
