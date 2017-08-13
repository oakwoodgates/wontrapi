<?php
/**
 * Wontrapi Options
 *
 * @since 0.1.1
 * @package Wontrapi
 */

/**
 * Wontrapi Options class.
 *
 * @since 0.1.1
 */
class Wontrapi_Options {
	/**
	 * Parent plugin class
	 *
	 * @var    class
	 * @since  0.1.1
	 */
	protected $plugin = null;

	/**
	 * Option key, and option page slug
	 *
	 * @var    string
	 * @since  0.1.1
	 */
	protected $key = 'wontrapi_options';

	/**
	 * Options page metabox id
	 *
	 * @var    string
	 * @since  0.1.1
	 */
	protected $metabox_id = 'wontrapi_options_metabox';

	/**
	 * Options Page title
	 *
	 * @var    string
	 * @since  0.1.1
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor
	 *
	 * @since  0.1.1
	 * @param  object $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		$this->title = __( 'Wontrapi Options', 'wontrapi' );
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  0.1.1
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
	}

	/**
	 * Register our setting to WP
	 *
	 * @since  0.1.1
	 * @return void
	 */
	public function admin_init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 *
	 * @since  0.1.1
	 * @return void
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
	 * Admin page markup. Mostly handled by CMB2
	 *
	 * @since  0.1.1
	 * @return void
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
	 * @since  0.1.1
	 * @return void
	 */
	public function add_options_page_metabox() {

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

		/*
		Add your fields here

		$cmb->add_field( array(
			'name'    => __( 'Test Text', 'myprefix' ),
			'desc'    => __( 'field description (optional)', 'myprefix' ),
			'id'      => 'test_text', // no prefix needed
			'type'    => 'text',
			'default' => __( 'Default Text', 'myprefix' ),
		) );
		*/
		$cmb->add_field( array(
			'name'    => __( 'API ID', 'wontrapi' ),
			'id'      => 'api_appid', // no prefix needed
			'type'    => 'text',
		) );

		$cmb->add_field( array(
			'name'    => __( 'API Key', 'wontrapi' ),
			'id'      => 'api_key', // no prefix needed
			'type'    => 'text',
		) );
	}
}
