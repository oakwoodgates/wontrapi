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
	protected $key = 'wontrapi_options';

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
	}

	/**
	 * Register our setting to WP.
	 *
	 * @since  0.3.0
	 */
	public function admin_init() {
		register_setting( $this->key, $this->key );
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
					<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
						<h1>Wontrapi Configuration Settings
							<?php // echo esc_html( get_admin_page_title() ); ?></h1>
						<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
					</div>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<h3>Sidebar</h3>
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
				'value' => array( $this->key ),
			),
		) );

		$cmb->add_field( array(
			'name' => 'API Settings',
			'desc' => 'Creates the connection in Ontraport. For information on obtaining an API key, <a href="https://support.ontraport.com/hc/en-us/articles/217882248-API-in-ONTRAPORT#how-to-obtain-an-api-key" target="_blank">click here</a>.',
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

		$options = get_option( $this->key, array() );
		$ping_value = (!empty($options['ping_value'])) ? $options['ping_value'] : rand();
		if ( !empty($options['ping_value']) ) {
			$ping_value = $options['ping_value'];
		} elseif ( !empty($_POST['ping_value'])) {
			$ping_value = $_POST['ping_value'];
		} else {
			$ping_value = rand();
		}

		$cmb->add_field( array(
			'name'    => __( 'Ping Key Value', 'wontrapi' ),
			'id'      => 'ping_value',
			'default' => $ping_value,
			'type'    => 'text_medium',
			'desc'    => __( '<br/>This value is used when sending data from Ontraport to your website. <br/>When creating a Rule that will ping this website, include the parameter like this: <br/>wontrapi_key='.$ping_value, 'wontrapi' ),
			'attributes'  => array(
				'required'    => 'required',
			)
		) );

		$cmb->add_field( array(
			'name'    => __( 'Section Title', 'wontrapi' ),
			'id'      => 'section_title', 
			'desc'    => __( 'Creates a section in Ontraport to store fields and data.', 'wontrapi' ),
			'default' => 'Wontrapi - ' . get_bloginfo( 'name' ),
			'type'    => 'text_medium',
			'attributes'  => array(
				'required'    => 'required',
			)
		) );

		$cmb->add_field( array(
			'name' => 'Site Title',
			'desc' => 'This is a title description',
			'type' => 'title',
			'id'   => 'wiki_test_title2'
		) );

		$cmb->add_field( array(
			'name'    => __( 'Add tracking script?', 'wontrapi' ),
			'desc'    => __( 'To enable tracking, copy and paste the full script here. <a href="https://support.ontraport.com/hc/en-us/articles/217882408-Web-Page-Tracking" target="_blank">Click here for intructions</a>', 'wontrapi' ),
			'id'      => 'tracking', 
			'type'    => 'text'
		) );


		/**
		 * Registers secondary options page, and set main item as parent.
		 */
		$secondary_options = new_cmb2_box( array(
			'id'           => 'wontrapi_secondary_options_page',
			'title'        => esc_html__( 'Secondary Options', 'cmb2' ),
			'object_types' => array( 'options-page' ),
			'option_key'   => 'wontrapi_secondary_options',
			'parent_slug'  => 'wontrapi_options',
		) );
		$secondary_options->add_field( array(
			'name'    => esc_html__( 'Test Radio', 'cmb2' ),
			'desc'    => esc_html__( 'field description (optional)', 'cmb2' ),
			'id'      => 'radio',
			'type'    => 'radio',
			'options' => array(
				'option1' => esc_html__( 'Option One', 'cmb2' ),
				'option2' => esc_html__( 'Option Two', 'cmb2' ),
				'option3' => esc_html__( 'Option Three', 'cmb2' ),
			),
		) );

		do_action( 'wontrapi_options_page' );
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
//	add_action( 'cmb2_admin_init',       'wontrapi_options_page_register_main_options_metabox' );
add_action( 'wontrapi_options_page', 'wontrapi_options_page_register_main_options_metabox' );
