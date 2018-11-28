<?php
/**
 * Wontrapi Options.
 *
 * @since   0.3.0
 * @package Wontrapi
 */



/**
 * Wontrapi Options class.
 *
 * @since 0.3.0
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
	 * Options Page hook.
	 *
	 * @var string
	 */
	protected $options_page = '';

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

		// Set our title.
		$this->title = esc_attr__( 'Wontrapi', 'wontrapi' );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.3.0
	 */
	public function hooks() {

		// Hook in our actions to the admin.
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
		
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
	 * Add menu options page.
	 *
	 * @since  0.3.0
	 */
	public function add_options_page() {
		$this->options_page = add_menu_page(
			$this->title,
			$this->title,
			'manage_options',
			$this->key,
			array( $this, 'admin_page_display' )
		);

		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @since  0.3.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Add custom fields to the options page.
	 *
	 * @since  0.3.0
	 */
	public function add_options_page_metabox() {

		// Add our CMB2 metabox.
		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove.
				'key'   => 'options-page',
				'value' => array( $this->key ),
			),
		) );

		$cmb->add_field( array(
			'name'    => __( 'API ID', 'wontrapi' ),
			'id'      => 'api_appid', 
			'type'    => 'text_medium',
		) );

		$cmb->add_field( array(
			'name'    => __( 'API Key', 'wontrapi' ),
			'id'      => 'api_key', 
			'type'    => 'text_medium',
		) );

		$cmb->add_field( array(
			'name'    => __( 'Ping Key', 'wontrapi' ),
			'id'      => 'ping_key', 
			'default' => 'wontrapi_key',
			'type'    => 'text_medium',
		) );

		$cmb->add_field( array(
			'name'    => __( 'Ping Key value', 'wontrapi' ),
			'id'      => 'ping_value', 
			'default' => '774562',
			'type'    => 'text_medium',
		) );

		$cmb->add_field( array(
			'name'    => __( 'Add tracking script?', 'wontrapi' ),
			'desc'    => __( 'To enable tracking, copy and paste the full script here. <a href="https://support.ontraport.com/hc/en-us/articles/217882408-Web-Page-Tracking" target="_blank">Click here for intructions</a>', 'wontrapi' ),
			'id'      => 'tracking', 
			'type'    => 'text',
		) );
	}
}
